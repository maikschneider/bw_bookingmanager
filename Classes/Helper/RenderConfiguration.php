<?php

namespace Blueways\BwBookingmanager\Helper;

/**
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * PHP version 7.2
 *
 * @package  BwBookingManager
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http://www.blueways.de
 */
class RenderConfiguration
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot[]
     */
    protected $timeslots;

    /**
     * @var \DateTime|null
     */
    protected $startDate;

    /**
     * @var \DateTime|null
     */
    protected $endDate;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar;

    /**
     * RenderConfiguration constructor.
     *
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar|null $calendar
     */
    public function __construct($startDate = null, $endDate = null, $calendar = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calendar = $calendar;
    }

    /**
     * @return array
     */
    public function getConfigurationForList()
    {
        $daysInRange = $this->startDate->diff($this->endDate)->days;

        $days = $this->getDaysArrayForRange($this->startDate, $daysInRange);

        return array(
            'days' => $days,
        );
    }

    /**
     * @param \DateTime $startDate
     * @param integer $daysCount
     * @return array
     */
    private function getDaysArrayForRange($startDate, $daysCount)
    {
        $days = [];

        for ($i = 0; $i < $daysCount; $i++) {
            $days[$i] = [
                'date' => clone $startDate,
                'timeslots' => $this->getTimeslotsForDay($startDate),
                'isCurrentDay' => $this->isCurrentDay($startDate),
                'isNotInMonth' => !($startDate->format('m') == $this->startDate->format('m')),
                'isInPast' => $this->isInPast($startDate)
            ];
            $days[$i]['isBookable'] = $this->getDayIsBookable($days[$i]['timeslots']);

            $startDate->modify('+1 day');
        }
        return $days;
    }

    /**
     * @param \DateTime $day
     * @return array
     */
    private function getTimeslotsForDay($day)
    {
        $timeslots = [];
        $dayEnd = clone $day;
        $dayEnd->setTime(23, 59, 59);

        foreach ($this->timeslots as $timeslot) {
            if (!($timeslot->getEndDate() < $day || $timeslot->getStartDate() > $dayEnd)) {
                $timeslots[] = $timeslot;
            }
        }
        return $timeslots;
    }

    /**
     * @param \DateTime $day
     * @return bool
     * @throws \Exception
     */
    private function isCurrentDay($day)
    {
        $now = new \DateTime('now');
        return $now->format('d.m.Y') === $day->format('d.m.Y');
    }

    /**
     * @param \DateTime $day
     * @return bool
     * @throws \Exception
     */
    private function isInPast($day)
    {
        $now = new \DateTime('now');
        return $day < $now;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot[] $timeslots
     * @return bool
     */
    private function getDayIsBookable($timeslots)
    {
        $isBookable = false;
        foreach ($timeslots as $timeslot) {
            $slotIsBookable = $timeslot->getIsBookable();
            if ($slotIsBookable) {
                $isBookable = true;
            }
        }
        return $isBookable;
    }

    /**
     * @return array
     */
    public function getConfigurationForWeek()
    {
        $weekStart = clone $this->startDate;
        $weekStart->modify('tomorrow');
        $weekStart->modify('last monday');
        $weekStart->setTime(0, 0, 0);

        $nextWeek = clone $weekStart;
        $nextWeek->modify('+1 week');
        $prevWeek = clone $weekStart;
        $prevWeek->modify('-1 week');

        $days = $this->getDaysArrayForWeek($weekStart);

        return array(
            'days' => $days,
            'nextWeek' => [
                'date' => $nextWeek,
                'day' => $nextWeek->format('j'),
                'month' => $nextWeek->format('m'),
                'year' => $nextWeek->format('Y'),
            ],
            'prevWeek' => [
                'date' => $prevWeek,
                'day' => $prevWeek->format('j'),
                'month' => $prevWeek->format('m'),
                'year' => $prevWeek->format('Y'),
            ],
        );
    }

    /**
     * @param \DateTime $weekStart
     * @return array
     */
    private function getDaysArrayForWeek($weekStart)
    {
        return $this->getDaysArrayForRange($weekStart, 7);
    }

    /**
     * @return array
     */
    public function getConfigurationForMonth()
    {
        $monthStart = clone $this->startDate;
        $monthStart->modify('first day of this month');
        $monthStart->modify('last monday');
        $monthStart->setTime(0, 0, 0);

        $monthEnd = clone $this->startDate;
        $monthEnd->modify('last day of this month');
        $monthEnd->setTime(23, 59, 59);

        $nextMonth = clone $this->startDate;
        $nextMonth->modify('first day of next month');
        $prevMonth = clone $this->startDate;
        $prevMonth->modify('first day of last month');

        // #weeks = #mondays
        $numberOfWeeks = \Blueways\BwBookingmanager\Helper\TimeslotManager::dayCount($monthStart, $monthEnd, 1);

        $weeks = [];

        for ($i = 0; $i < $numberOfWeeks; $i++) {
            $weekStart = clone $monthStart;

            $weeks[] = $this->getDaysArrayForWeek($weekStart);

            $monthStart->modify('next monday');
        }

        return array(
            'weeks' => $weeks,
            'nextMonth' => [
                'date' => $nextMonth,
                'day' => $nextMonth->format('j'),
                'month' => $nextMonth->format('m'),
                'year' => $nextMonth->format('Y'),
            ],
            'prevMonth' => [
                'date' => $prevMonth,
                'day' => $prevMonth->format('j'),
                'month' => $prevMonth->format('m'),
                'year' => $prevMonth->format('Y'),
            ],
        );
    }

    /**
     * @param integer $daysCount
     * @return array
     */
    public function getConfigurationForDays($daysCount)
    {
        $daysStart = clone $this->startDate;
        $daysStart->setTime(0, 0, 0);

        $daysEnd = clone $this->startDate;
        $daysEnd->modify('+' . $daysCount . ' days');
        $daysEnd->setTime(23, 59, 59);

        $nextdays = clone $this->startDate;
        $nextdays->modify('+' . ($daysCount + 1) . ' days');
        $prevdays = clone $this->startDate;
        $prevdays->modify('-' . ($daysCount + 1) . ' days');

        $days = $this->getDaysArrayForRange($daysStart, $daysCount);

        return array(
            'days' => $days,
            'nextDays' => [
                'date' => $nextdays,
                'day' => $nextdays->format('j'),
                'month' => $nextdays->format('m'),
                'year' => $nextdays->format('Y'),
            ],
            'prevdays' => [
                'date' => $prevdays,
                'day' => $prevdays->format('j'),
                'month' => $prevdays->format('m'),
                'year' => $prevdays->format('Y'),
            ],
        );
    }

    /**
     * @param $timeslots
     */
    public function setTimeslots($timeslots)
    {
        $this->timeslots = $timeslots;
    }

    private function getEntriesForDay($day)
    {
        $entries = [];
        $dayEnd = clone $day;
        $dayEnd->setTime(23, 59, 59);

        foreach ($this->calendar->getEntries() as $entry) {
            if (!($entry->getEndDate() < $day || $entry->getStartDate() > $dayEnd)) {
                $entries[] = $entry;
            }
        }
        return $entries;
    }

    private function isSameDay($day1, $day2)
    {
        return $day1->diff($day2)->days == 0;
    }
}
