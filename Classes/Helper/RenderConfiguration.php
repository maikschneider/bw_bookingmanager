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
     * @var \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf
     */
    protected $dateConf;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar;

    /**
     * RenderConfiguration constructor.
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    public function __construct($dateConf, $calendar)
    {
        $this->dateConf = $dateConf;
        $this->calendar = $calendar;
    }

    /**
     * @return array
     * @throws \Exception
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
     * @throws \Exception
     */
    private function getDaysArrayForRange($startDate, $daysCount)
    {
        $days = [];

        for ($i = 0; $i < $daysCount; $i++) {
            $days[$i] = [
                'date' => clone $startDate,
                'timeslots' => $this->getTimeslotsForDay($startDate),
                'isCurrentDay' => $this->isCurrentDay($startDate),
                'isNotInMonth' => !($startDate->format('m') == $this->dateConf->start->format('m')),
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

        // @TODO: check if direct_bookings are possible

        return $isBookable;
    }

    /**
     * @param $timeslots
     */
    public function setTimeslots($timeslots)
    {
        $this->timeslots = $timeslots;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRenderConfiguration()
    {
        if ($this->dateConf->viewType === 0) {
            return $this->getConfigurationForMonth();
        }
        if ($this->dateConf->viewType === 1) {
            return $this->getConfigurationForWeek();
        }
        if ($this->dateConf->viewType === 2) {
            return $this->getConfigurationForDays($this->dateConf::DEFAULT_DAYS_LENGTH);
        }
        return [];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getConfigurationForMonth()
    {
        $monthStart = clone $this->dateConf->start;
        $monthEnd = clone $this->dateConf->end;

        $nextMonth = $this->dateConf->next;
        $prevMonth = $this->dateConf->prev;

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
     * @param \DateTime $weekStart
     * @return array
     * @throws \Exception
     */
    private function getDaysArrayForWeek($weekStart)
    {
        return $this->getDaysArrayForRange($weekStart, 7);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getConfigurationForWeek()
    {
        $weekStart = clone $this->dateConf->start;

        $nextWeek = $this->dateConf->next;
        $prevWeek = $this->dateConf->prev;

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
     * @param integer $daysCount
     * @return array
     * @throws \Exception
     */
    public function getConfigurationForDays($daysCount)
    {
        $daysStart = clone $this->dateConf->start;

        $nextdays = $this->dateConf->next;

        $prevdays = $this->dateConf->prev;

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
