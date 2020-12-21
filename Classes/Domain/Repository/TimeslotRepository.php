<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use Blueways\BwBookingmanager\Domain\Model\Timeslot;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Timeslots
 */
class TimeslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array|\Blueways\BwBookingmanager\Domain\Model\Timeslot[]|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findInRange($calendar, \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf)
    {
        $timeslots = $this->findAllPossibleByDateRange([$calendar->getUid()], $dateConf->start, $dateConf->end);
        $timeslotManager = new \Blueways\BwBookingmanager\Helper\TimeslotManager(
            $timeslots,
            $calendar,
            $dateConf->start,
            $dateConf->end
        );
        $timeslots = $timeslotManager->getTimeslots();

        return $timeslots;
    }

    /**
     * @param int
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllPossibleByDateRange(
        array $calendars,
        \DateTime $startDate,
        \DateTime $endDate
    ) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr([
                // no repeatable events starting during date range
                $query->logicalAnd([
                    $query->in('calendar', $calendars),
                    $query->equals('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                    $query->greaterThanOrEqual('startDate', $startDate->getTimestamp()),
                    $query->lessThanOrEqual('startDate', $endDate->getTimestamp()),
                ]),
                // repeating events that end during or after date range
                // these events can be in the past and occur in range after repeat function
                $query->logicalAnd([
                    $query->in('calendar', $calendars),
                    $query->greaterThan('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                    $query->lessThan('startDate', $endDate->getTimestamp()),
                    $query->logicalOr([
                        $query->equals('repeatEnd', 0),
                        $query->greaterThanOrEqual('repeatEnd', $startDate->getTimestamp())
                    ])
                ]),
            ])
        );

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    public function getTimeslotsForCalendars($calendars, \DateTime $startDate, \DateTime $endDate)
    {
        $timeslotResults = [];
        foreach ($calendars as $calendar) {
            $timeslotResults[] = $this->getTimeslotsInCalendar($calendar->getUid(), $startDate, $endDate);
        }
        $timeslots = array_merge([], ...$timeslotResults);
        $timeslots = $this->mapTimeslotResultToObjects($timeslots);
        return $timeslots;
    }

    public function getTimeslotsInCalendar(int $calendarUid, \DateTime $startDate, \DateTime $endDate)
    {
        $sql = "select 	uid,
		CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.start_date)))) as UNSIGNED) as t_start_date,
		CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.end_date)))) as UNSIGNED) as t_end_date,
        CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.start_date)))) as UNSIGNED) as start_date,
		CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.end_date)))) as UNSIGNED) as end_date,
        t.*,
        CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.start_date)))) as UNSIGNED) as start_date,
		CAST(UNIX_TIMESTAMP(concat(dates.date, ' ', TIME(FROM_UNIXTIME(t.end_date)))) as UNSIGNED) as end_date,
#		dates.date,
#		TIME(FROM_UNIXTIME(t.start_date)) as start,
#		TIME(FROM_UNIXTIME(t.end_date)) as end,
#		group_concat(entry_uid) as entries
        COUNT(entry_uid) as entries

from tx_bwbookingmanager_domain_model_timeslot as t

cross join (

select
	date,
	DAYOFWEEK(date) as weekday, is_holiday
from
	(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) date
	  from
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
	 	(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
		(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
	) v

left join (select start_date, end_date, uid from tx_bwbookingmanager_domain_model_blockslot b inner join tx_bwbookingmanager_calendar_blockslot_mm m on m.uid_foreign=b.uid where b.deleted=0 and b.hidden=0 and m.uid_local=" . $calendarUid . ") b on
	(DATE(FROM_UNIXTIME(start_date))<=date and DATE(FROM_UNIXTIME(end_date))>=date)

left join (select start_date, end_date, uid is not null as is_holiday from tx_bwbookingmanager_domain_model_holiday h inner join tx_bwbookingmanager_calendar_holiday_mm hm on hm.uid_foreign=h.uid where h.deleted=0 and h.hidden=0 and hm.uid_local=" . $calendarUid . ") h on
	(DATE(FROM_UNIXTIME(h.start_date))<=date and DATE(FROM_UNIXTIME(h.end_date))>=date)

where
 date between '" . $startDate->format('Y-m-d') . "' and '" . $endDate->format('Y-m-d') . "' and
 uid is null

group by date

) as dates

left join (select uid as entry_uid, start_date as entry_start, end_date as entry_date, timeslot from tx_bwbookingmanager_domain_model_entry where deleted=0 and hidden=0) e on (timeslot=uid and DATE(FROM_UNIXTIME(entry_start))=dates.date)

where
	calendar = " . $calendarUid . " AND
	deleted = 0 AND
	hidden = 0 AND
	(holiday_setting=0 or (holiday_setting=1 and is_holiday is null) or (holiday_setting=2 and is_holiday=1)) AND

	(	repeat_type = 0 OR repeat_type = 1 OR
		repeat_type = 4 AND
		(
			(SUBSTRING(REVERSE(BIN(repeat_days)), 1, 1) = 1) AND weekday = 1 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 2, 1) = 1) AND weekday = 2 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 3, 1) = 1) AND weekday = 3 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 4, 1) = 1) AND weekday = 4 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 5, 1) = 1) AND weekday = 5 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 6, 1) = 1) AND weekday = 6 OR
			(SUBSTRING(REVERSE(BIN(repeat_days)), 7, 1) = 1) AND weekday = 7
		)
	)

group by t_start_date, t_end_date

order by dates.date;";

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->statement($sql);

        return $query->execute(true);
    }

    public function mapTimeslotResultToObjects(array $timeslots)
    {
        $dataMapper = $this->objectManager->get(DataMapper::class);
        $dataMap = $dataMapper->getDataMap($this->objectType);

        return $dataMapper->map(
            $dataMap->getClassName(),
            $timeslots
        );
    }
}
