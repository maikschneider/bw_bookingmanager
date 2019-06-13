<?php

namespace Blueways\BwBookingmanager\Helper;

use Blueways\BwBookingmanager\Domain\Model\Timeslot;

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
     * @var \Blueways\BwBookingmanager\Domain\Model\Entry[]
     */
    protected $entries;

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
     * @param $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    /**
     * @param $timeslots
     */
    public function setTimeslots($timeslots)
    {
        // sort timeslots
        usort($timeslots, function (Timeslot $a, Timeslot $b) {
            return $a->getStartDate() > $b->getStartDate();
        });

        $this->timeslots = $timeslots;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRenderConfiguration()
    {
        $start = $this->dateConf->start;
        $daysCount = $this->dateConf->start->diff($this->dateConf->end)->days;

        $configuration = [];
        $configuration['timeslots'] = $this->timeslots;
        $configuration['entries'] = $this->entries;
        $configuration['days'] = $this->getDaysArray($start, $daysCount);
        $configuration['weeks'] = $this->getWeeksArray($daysCount);
        $configuration['next'] = [
            'date' => $this->dateConf->next,
            'day' => $this->dateConf->next->format('j'),
            'month' => $this->dateConf->next->format('m'),
            'year' => $this->dateConf->next->format('Y'),
            'link' => '/api/calendar/' . $this->calendar->getUid() . '/' .
                $this->dateConf->next->format('j') . '-' .
                $this->dateConf->next->format('m') . '-' .
                $this->dateConf->next->format('Y') . '.json'
        ];
        $configuration['prev'] = [
            'date' => $this->dateConf->prev,
            'day' => $this->dateConf->prev->format('j'),
            'month' => $this->dateConf->prev->format('m'),
            'year' => $this->dateConf->prev->format('Y'),
            'link' => '/api/calendar/' . $this->calendar->getUid() . '/' .
                $this->dateConf->prev->format('j') . '-' .
                $this->dateConf->prev->format('m') . '-' .
                $this->dateConf->prev->format('Y') . '.json'
        ];

        return $configuration;
    }

    /**
     * @param \DateTime $startDate
     * @param integer $daysCount
     * @param bool $returnOffsets
     * @return array
     * @throws \Exception
     */
    private function getDaysArray($startDate, $daysCount)
    {
        $days = [];
        $date = clone $startDate;

        for ($i = 0; $i <= $daysCount; $i++) {
            $days[$i] = $this->getDayArray($date);
            $date->modify('+1 day');
        }
        return $days;
    }

    /**
     * @param \DateTime $date
     * @return array
     * @throws \Exception
     */
    private function getDayArray($date)
    {
        $timeslots = $this->getTimeslotsForDay($date);
        $entries = $this->getEntriesForDay($date);

        $day = [];
        $day['date'] = $date->format('c');
        $day['entries'] = $this->getEntryOffsets($entries);
        $day['timeslots'] = $this->getTimeslotOffsets($timeslots);
        $day['isCurrentDay'] = $this->isCurrentDay($date);
        $day['isNotInMonth'] = !($date->format('m') == $this->dateConf->startOrig->format('m'));
        $day['isInPast'] = $this->isInPast($date);
        $day['isSelectedDay'] = $this->isSelectedDay($date);
        $day['bookableTimeslotsStatus'] = $this->getBookableTimeslotsStatus($timeslots);
        $day['hasBookableTimeslots'] = (boolean)$day['bookableTimeslotsStatus'];
        $day['isDirectBookable'] = $this->isDirectBookable($entries);
        $day['isBookable'] = ((!$day['isInPast'] || $day['isCurrentDay']) && ($day['hasBookableTimeslots'] || $day['isDirectBookable']));

        return $day;
    }

    /**
     * @param \DateTime $day
     * @return array<Timeslot>
     */
    private function getTimeslotsForDay($day)
    {
        $timeslots = [];

        if (!$this->timeslots) {
            return $timeslots;
        }

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
     * @return array
     */
    private function getEntriesForDay(\DateTime $day)
    {
        $entries = [];

        if (!$this->entries) {
            return $entries;
        }

        $dayEnd = clone $day;
        $dayEnd->setTime(23, 59, 59);

        foreach ($this->entries as $entry) {
            if (!($entry->getEndDate() < $day || $entry->getStartDate() > $dayEnd)) {
                $entries[] = $entry->getUid();
            }
        }
        return $entries;
    }

    private function getEntryOffsets($entries)
    {
        $offsets = [];
        foreach ($this->entries as $key => $entry) {
            if (in_array($entry, $entries)) {
                $offsets[] = $key;
            }
        }
        return $offsets;
    }

    private function getTimeslotOffsets($timeslots)
    {
        $offsets = [];
        foreach ($this->timeslots as $key => $timeslot) {
            if (in_array($timeslot, $timeslots)) {
                $offsets[] = $key;
            }
        }
        return $offsets;
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
     * @param \DateTime $day
     * @return bool
     */
    private function isSelectedDay($day)
    {
        return $this->dateConf->startOrig->format('d.m.Y') === $day->format('d.m.Y');
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot[] $timeslots
     * @return float|int
     */
    private function getBookableTimeslotsStatus($timeslots)
    {
        if (!sizeof($timeslots)) {
            return 0;
        }

        $bookableCount = 0;
        foreach ($timeslots as $timeslot) {
            if ($timeslot->getIsBookable()) {
                $bookableCount++;
            }
        }

        return $bookableCount / sizeof($timeslots);
    }

    /**
     * @param $entries
     * @return bool
     */
    private function isDirectBookable($entries)
    {
        return $this->calendar->isDirectBooking() && !sizeof($entries);
    }

    /**
     * @param int $daysCount
     * @return array
     */
    private function getWeeksArray($daysCount)
    {
        $weeks = [];

        $dayOffset = 0;

        while ($dayOffset < $daysCount) {

            $week = [];

            for ($j = 0; $j < 7; $j++) {
                $week[] = $dayOffset;
                $dayOffset++;
            }

            $weeks[] = $week;
        }

        return $weeks;
    }
}
