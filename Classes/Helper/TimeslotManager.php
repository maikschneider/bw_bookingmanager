<?php

namespace Blueways\BwBookingmanager\Helper;

use \Blueways\BwBookingmanager\Domain\Model\Timeslot;

/**
 * This class oganizes the correct arrangement of timeslots
 */
class TimeslotManager
{

    /**
     * @var array<\Blueways\BwBookingmanager\Domain\Model\Timeslot>|\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Timeslot> $timeslots
     */
    protected $timeslots = null;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    protected $calendar = null;

    /**
     * @var \DateTime $startDate
     */
    protected $startDate = null;

    /**
     * @var \DateTime $endDate
     */
    protected $endDate = null;

    /**
     * @var array $filterCritera
     */
    protected $filterCritera;

    /**
     * __construct
     *
     * @param $timeslots
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct(
        $timeslots,
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        \DateTime $endDate
    ) {
        $this->timeslots = $timeslots;
        $this->calendar = $calendar;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * starts all organising actions and returns the finished timeslots
     */
    public function getTimeslots()
    {
        $this->repeatTimeslots();
        $this->createFilterCritera();
        $this->filterTimeslots();
        $this->filterEntries();

        return $this->timeslots;
    }

    /**
     * checks every slot for type of repeat and merges duplicated slots back
     *
     * @return void
     */
    public function repeatTimeslots()
    {
        $timeslots = $this->timeslots->toArray();
        $newTimeslots = [];
        foreach ($timeslots as $timeslot) {
            $repeatType = $timeslot->getRepeatType();
            if ($repeatType === Timeslot::REPEAT_DAILY) {
                $newTimeslots = array_merge($newTimeslots, $this->repeatDailyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_WEEKLY) {
                $newTimeslots = array_merge($newTimeslots, $this->repeatWeeklyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_MONTHLY) {
                $newTimeslots = array_merge($newTimeslots, $this->repeatMonthlyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_MULTIPLE_WEEKLY) {
                $newTimeslots = array_merge($newTimeslots, $this->repeatMultipleWeeklyTimeslot($timeslot));
            }
        }

        $this->timeslots = array_merge($timeslots, $newTimeslots);
    }

    /**
     * create array of critera that timeslots need to pass
     */
    private function createFilterCritera()
    {
        $this->filterCritera = [
            'in' => [
                [$this->startDate, $this->endDate]
            ],
            'inAny' => [

            ],
            'notIn' => [

            ],
            'holidays' => [

            ]
        ];

        // creteria for Blockslots
        $blockslots = $this->calendar->getBlockslots();
        if ($blockslots) {
            foreach ($blockslots as $blockslot) {
                $blockStartDate = $blockslot->getStartDate();
                $blockEndDate = $blockslot->getEndDate();

                // check if block is inside date range
                // so add its dates to filterCritera
                if (!($blockEndDate < $this->startDate || $blockStartDate > $this->endDate)) {
                    $this->filterCritera['notIn'][] = [$blockStartDate, $blockEndDate];
                }
            }
        }

        // start+end dates for Holidays
        $holidays = $this->calendar->getHolidays();
        if ($holidays) {
            foreach ($holidays as $holiday) {
                $holiStartDate = $holiday->getStartDate()->setTime(0, 0, 0);
                $holiEndDate = $holiday->getEndDate()->setTime(23, 59, 59);

                // check if block is inside date range
                // so add its dates to filterCritera
                if (!($holiEndDate < $this->startDate || $holiStartDate > $this->endDate)) {
                    $this->filterCritera['holidays'][] = [$holiStartDate, $holiEndDate];
                }
            }
        }
    }

    /**
     * removes timeslots that do not pass filterCritera
     */
    private function filterTimeslots()
    {
        $this->timeslots = array_filter(
            $this->timeslots,
            function (Timeslot $timeslot) {

                // check timeslot if holidays should move to 'in' or 'out' critera array
                if ($timeslot->getHolidaySetting() === Timeslot::HOLIDAY_NOT_DURING) {
                    foreach ($this->filterCritera['holidays'] as $range) {
                        if ($timeslot->getEndDate() < $range[0] || $timeslot->getStartDate() > $range[1]) {
                            continue;
                        }
                        return false;
                    }
                }

                // filter timeslots for holiday setting to be within
                if ($timeslot->getHolidaySetting() === Timeslot::HOLIDAY_ONLY_DURING) {
                    $notInAny = true;
                    foreach ($this->filterCritera['holidays'] as $range) {
                        if ($timeslot->getStartDate() >= $range[0] && $timeslot->getEndDate() <= $range[1]) {
                            $notInAny = false;
                        }
                    }
                    if ($notInAny) {
                        return false;
                    }
                }

                // check for date range to be within
                // it is allowed that events start in the past, as long as they end in the given range or even alter
                foreach ($this->filterCritera['in'] as $range) {
                    if (($timeslot->getStartDate() < $range[0] && $timeslot->getEndDate() < $range[0])
                        || ($timeslot->getStartDate() > $range[1])
                    ) {
                        return false;
                    }
                }

                // check for date range to be not within
                // only this is valid: [slot] |blocked| [slot]
                // this is not valid   [ slot |] blocked [| slot ]
                foreach ($this->filterCritera['notIn'] as $range) {
                    if ($timeslot->getEndDate() < $range[0] || $timeslot->getStartDate() > $range[1]) {
                        continue;
                    }
                    return false;
                }

                // all checks passed
                return true;
            }
        );
    }

    private function filterEntries()
    {
        foreach ($this->timeslots as $timeslot) {
            $timeslotStartDate = $timeslot->getStartDate();
            $timeslotEndDate = $timeslot->getEndDate();

            $entries = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
            foreach ($timeslot->getEntries() as $entry) {
                if ($entry->getCalendar()->getUid() === $this->calendar->getUid() && $entry->getStartDate() == $timeslotStartDate && $entry->getEndDate() == $timeslotEndDate) {
                    $entries->attach($entry);
                }
            };
            $timeslot->setEntries($entries);
        }
    }

    /**
     * duplicates daily timeslot for whole date range
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return array
     */
    private function repeatDailyTimeslot($timeslot)
    {
        $newTimeslots = [];

        // default fill the whole date range with that timeslot
        $daysToFillTimeslots = $this->endDate->diff($this->startDate)->days + 1;
        $dateToStartFilling = clone $timeslot->getStartDate();
        $dateToStartFilling->setDate(
            $this->startDate->format('Y'),
            $this->startDate->format('m'),
            $this->startDate->format('d')
        );
        $dateStartEndDiff = $timeslot->getStartDate()->diff($timeslot->getEndDate());

        $timezone = new \DateTimeZone("Europe/Berlin");

        // create new timeslots and modify start and end date
        for ($i = 0; $i < $daysToFillTimeslots; $i++) {
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+' . $i . ' days');

            // DST fix
            $transitions = $timezone->getTransitions(
                $timeslot->getStartDate()->getTimestamp(),
                $newStartDate->getTimestamp()
            );
            $lastTransitionIndex = sizeof($transitions) - 1;
            if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('+1 hour');
            }
            if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('-1 hour');
            }

            $newEndDate = clone $newStartDate;
            $newEndDate->add($dateStartEndDiff);

            // dont add new timeslot if placed before actual timeslot or even at same time
            if ($newStartDate <= $timeslot->getStartDate()) {
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if ($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate) {
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
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $dayOfWeek
     * @return bool|float
     */
    public static function dayCount($from, $to, $day)
    {
        $wF = $from->format('w');
        $wT = $to->format('w');
        if ($wF < $wT) {
            $isExtraDay = $day >= $wF && $day <= $wT;
        } elseif ($wF == $wT) {
            $isExtraDay = $wF == $day;
        } else {
            $isExtraDay = $day >= $wF || $day <= $wT;
        }

        return floor($from->diff($to)->days / 7) + $isExtraDay;
    }

    /**
     * duplicates weekly timeslots in date range
     *
     * @param Timeslot $timeslot
     * @return array
     */
    private function repeatWeeklyTimeslot($timeslot)
    {
        $newTimeslots = [];

        // default fill the all mondays (or tuesdays..) of date range
        $daysToFillTimeslots = $this->dayCount(
            $this->startDate,
            $this->endDate,
            $timeslot->getStartDate()->format('w')
        );

        $dateToStartFilling = clone $timeslot->getStartDate();
        $dateToStartFilling->setDate(
            $this->startDate->format('Y'),
            $this->startDate->format('m'),
            $this->startDate->format('d')
        );
        $dateToStartFilling->modify('-1 days');
        $dateToStartFilling->modify('next ' . $timeslot->getStartDate()->format('l H:i:s'));
        $dateStartEndDiff = $timeslot->getStartDate()->diff($timeslot->getEndDate());

        $timezone = new \DateTimeZone('Europe/Berlin');

        for ($i = 0; $i < $daysToFillTimeslots; $i++) {
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+' . $i . ' weeks');

            // DST fix
            $transitions = $timezone->getTransitions(
                $timeslot->getStartDate()->getTimestamp(),
                $newStartDate->getTimestamp()
            );
            $lastTransitionIndex = sizeof($transitions) - 1;
            if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('+1 hour');
            }
            if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('-1 hour');
            }

            $newEndDate = clone $newStartDate;
            $newEndDate->add($dateStartEndDiff);

            // dont add new timeslot if placed before actual timeslot or even at same time
            if ($newStartDate <= $timeslot->getStartDate()) {
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if ($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate) {
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
     * @TODO: implement
     */
    private function repeatMonthlyTimeslot($timeslot)
    {
        return [];
    }

    /**
     * @param Timeslot $timeslot
     * @return array
     */
    private function repeatMultipleWeeklyTimeslot($timeslot)
    {
        $timeslots = [];
        $repeatDays = $timeslot->getRepeatDaysSelectedWeekDays();
        $startWeekDay = (int)$timeslot->getStartDate()->format('w');
        $daysToCrawl = $this->endDate->diff($timeslot->getStartDate())->days;

        for ($i=1; $i<= $daysToCrawl; $i++) {
            $currentWeekDay = ($i + $startWeekDay) % 7;
            if(in_array($currentWeekDay, $repeatDays)) {

                $newTimeslot = clone $timeslot;
                $newStartDate = clone $timeslot->getStartDate();
                $newStartDate->modify('+ '.$i.' days');
                $newEndDate = clone $timeslot->getEndDate();
                $newEndDate->modify('+ ' . $i . ' days');

                $newTimeslot->setStartDate($newStartDate);
                $newTimeslot->setEndDate($newEndDate);

                $timeslots[] = $newTimeslot;
            }
        }

        return $timeslots;

    }
}
