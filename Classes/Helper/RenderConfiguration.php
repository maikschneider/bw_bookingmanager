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

        $days = [];

        for ($i = 0; $i < 7; $i++) {

            $days[$i] = [
                'date' => clone $weekStart,
                'timeslots' => $this->getTimeslotsForDay($weekStart),
                'isCurrentDay' => $this->isCurrentDay($weekStart),
            ];

            $weekStart->modify('+1 day');

        }

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

    public function getConfigurationForMonth()
    {

    }

    public function setTimeslots($timeslots)
    {
        $this->timeslots = $timeslots;
    }
}
