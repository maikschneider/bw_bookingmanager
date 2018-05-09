<?php
namespace Blueways\BwBookingmanager\Domain\Repository;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * The repository for Timeslots
 */
class TimeslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findAllPossibleByDateRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        \DateTime $endDate
        ){
            $query = $this->createQuery();
            $query->matching(
                $query->logicalOr([
                    // no repeatable events starting during date range
                    $query->logicalAnd([
                        $query->contains('calendars', $calendar),
                        $query->equals('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                        $query->greaterThanOrEqual('startDate', $startDate->format('Y-m-d 00:00:00')),
                        $query->lessThanOrEqual('startDate', $endDate->format('Y-m-d 23:59:59')),
                    ]),
                    // repeating events that end during or after date range
                    // these events can be in the past and occur in range after repeat function
                    $query->logicalAnd([
                        $query->contains('calendars', $calendar),
                        $query->greaterThan('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                        $query->lessThan('startDate', $endDate->format('Y-m-d 23:59:59'))
                    ])
                ])
            );

            return $query->execute();
        }

    public function filterTimeslotsInDateRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        $timeslots,
        \DateTime $startDate,
        \DateTime $endDate
        ){
            $blockslots = $calendar->getBlockslots();

            foreach ($calendar->getBlockslots() as $blockslot) {

                $blockStartDate = $blockslot->getStartDate();
                $blockEndDate = $blockslot->getEndDate();

                if(
                    ($blockStartDate <= $startDate && $blockEndDate > $startDate) ||
                    ($blockStartDate >= $startDate && $blockStartDate < $endDate)
                    ){
                    var_dump('Block greift!');
                }
            }

            return $timeslots;
        }
    
    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $timeslots
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function repeatTimeslotsInDateRange($timeslots, \DateTime $startDate, \DateTime $endDate)
    {
        $timeslots = $timeslots->toArray();
        $newTimeslots = [];
        foreach ($timeslots as $timeslot) {
            $repeatType = $timeslot->getRepeatType();
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_DAILY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatDailyTimeslotInDateRange($timeslot, $startDate, $endDate));
            }
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatWeeklyTimeslotInDateRange($timeslot, $startDate, $endDate));
            }
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_MONTHLY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatMonthlyTimeslotInDateRange($timeslot, $startDate, $endDate));
            }
        }

        return array_merge($timeslots, $newTimeslots);
    }

    private function repeatDailyTimeslotInDateRange($timeslot, \DateTime $startDate, \DateTime $endDate)
    {
        $newTimeslots = [];

        // default fill the whole date range with that timeslot
        $daysToFillTimeslots = $endDate->diff($startDate)->days + 1;
        $dateToStartFilling = $startDate;

        // create new timeslots and modify start and end date
        for($i=0; $i<$daysToFillTimeslots; $i++){
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+'.$i.' days');
            $newEndDate = clone $dateToStartFilling;
            $newEndDate->modify('+'.$i.' days');

            // dont add new timeslot if placed before actual timeslot or even at same time
            if($newStartDate <= $timeslot->getStartDate()){
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate){
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);

            $newTimeslots[] = $newTimeslot;
        }

        return $newTimeslots;
    }

    /**
     * Counts the occurences of a day of week in a date range
     * credits to this crazy motherfucker: https://stackoverflow.com/questions/20068975/count-the-no-of-fridays-or-any-between-two-specific-dates/20071461#20071461
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $dayOfWeek
     */
    private function dayCount($from, $to, $day) {

        $wF = $from->format('w');
        $wT = $to->format('w');
        if ($wF < $wT)      $isExtraDay = $day >= $wF && $day <= $wT;
        elseif ($wF == $wT) $isExtraDay = $wF == $day;
        else                $isExtraDay = $day >= $wF || $day <= $wT;

        return floor($from->diff($to)->days / 7) + $isExtraDay;
    }

    private function repeatWeeklyTimeslotInDateRange($timeslot, \DateTime $startDate, \DateTime $endDate)
    {
        $newTimeslots = [];

        // default fill the all mondays (or tuesdays..) of date range
        $daysToFillTimeslots = $this->dayCount($startDate, $endDate, $timeslot->getStartDate()->format('w'));
        $dateToStartFilling = clone $startDate;
        $dateToStartFilling->modify('-1 days');
        $dateToStartFilling->modify('next '.$timeslot->getStartDate()->format('l'));

        for($i=0; $i<$daysToFillTimeslots; $i++){
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+'.$i.' weeks');
            $newEndDate = clone $dateToStartFilling;
            $newEndDate->modify('+'.$i.' weeks');

            // dont add new timeslot if placed before actual timeslot or even at same time
            if($newStartDate <= $timeslot->getStartDate()){
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate){
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);

            $newTimeslots[] = $newTimeslot;
        }

        return $newTimeslots;
    }

    private function repeatMonthlyTimeslotInDateRange($timeslot, \DateTime $startDate, \DateTime $endDate)
    {
        return [];
    }


    public function findInCurrentMonth(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
        ){
        $today = new \DateTime('now');
        return $this->findInMonth($calendar, $today);
    }

    public function findInCurrentWeek(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
        ){
        $today = new \DateTime('now');
        return $this->findInWeek($calendar, $today);
    }

    public function findInMonth(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInMonth
    ){
        $startDate = clone $dayInMonth;
        $startDate->modify('first day of this month');

        $endDate = clone $dayInMonth;
        $endDate->modify('last day of this month');

        $timeslots = $this->findAllPossibleByDateRange($calendar, $startDate, $endDate);
        $timeslots = $this->repeatTimeslotsInDateRange($timeslots, $startDate, $endDate);
        $timeslots = $this->filterTimeslotsInDateRange($calendar, $timeslots, $startDate, $endDate);
        
        return $timeslots;
    }

    public function findInWeek(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInWeek
    ){
        $startDate = clone $dayInWeek;
        $startDate->modify('tomorrow');
        $startDate->modify('last monday');

        $endDate = clone $dayInWeek;
        $endDate->modify('yesterday');
        $endDate->modify('next sunday');

        return $this->findAllPossibleByDateRange($calendar, $startDate, $endDate);
    }
}
