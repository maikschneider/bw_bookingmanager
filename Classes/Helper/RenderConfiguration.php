<?php
namespace Blueways\BwBookingmanager\Helper;

class RenderConfiguration
{
    protected $timeslots;
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null)
    {
        $this->startDate = $startDate;
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

    private function getDaysArrayForWeek($weekStart)
    {
        $days = [];

        for ($i = 0; $i < 7; $i++) {

            $days[$i] = [
                'date' => clone $weekStart,
                'timeslots' => $this->getTimeslotsForDay($weekStart),
                'isCurrentDay' => $this->isCurrentDay($weekStart),
                'isNotInMonth' => !($weekStart->format('m') == $this->startDate->format('m'))
            ];

            $weekStart->modify('+1 day');
        }
        return $days;
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

    private function isSameDay($day1, $day2)
    {
        return $day1->diff($day2)->days == 0;
    }

    private function isCurrentDay($day)
    {
        return $this->isSameDay($day, $this->startDate);
    }

    public function setTimeslots($timeslots)
    {
        $this->timeslots = $timeslots;
    }
}
