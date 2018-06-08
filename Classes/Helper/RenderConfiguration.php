<?php
namespace Blueways\BwBookingmanager\Helper;

class RenderConfiguration
{
    protected $timeslots;
    protected $startDate;
    protected $endDate;
    protected $calendar;

    public function __construct($startDate = null, $endDate = null, $calendar = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calendar = $calendar;
    }

    public function getConfigurationForList()
    {
        $startDate = clone $this->startDate;
        $daysInRange = $this->startDate->diff($this->endDate)->days;

        $days = $this->getDaysArrayForRange($this->startDate, $daysInRange);

        return array(
            'days' => $days,
        );

    }

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

    private function getDaysArrayForRange($startDate, $daysCount)
    {
        $days = [];

        for ($i = 0; $i < $daysCount; $i++) {

            $days[$i] = [
                'date' => clone $startDate,
                'timeslots' => $this->getTimeslotsForDay($startDate),
                'isCurrentDay' => $this->isCurrentDay($startDate),
                'isNotInMonth' => !($startDate->format('m') == $this->startDate->format('m')),
            ];

            $startDate->modify('+1 day');
        }
        return $days;

    }

    private function getDaysArrayForWeek($weekStart)
    {
        return $this->getDaysArrayForRange($weekStart, 7);
    }

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
        $nextMonth->modify('+1 month');
        $prevMonth = clone $this->startDate;
        $prevMonth->modify('-1 month');

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

    private function isCurrentDay($day)
    {
        $now = new \DateTime('now');
        return $now->format('d.m.Y') === $day->format('d.m.Y');
    }

    public function setTimeslots($timeslots)
    {
        $this->timeslots = $timeslots;
    }
}
