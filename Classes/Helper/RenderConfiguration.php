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
        $numberOfWeeks = \Blueways\BwBookingmanager\Helper\TimeslotManager::dayCount($this->dateConf->start,
            $this->dateConf->end, 1);

        return array(
            'days' => $this->getDaysArray($start, $daysCount),
            'weeks' => $this->getWeeksArray($start, $numberOfWeeks),
            'next' => [
                'date' => $this->dateConf->next,
                'day' => $this->dateConf->next->format('j'),
                'month' => $this->dateConf->next->format('m'),
                'year' => $this->dateConf->next->format('Y'),
                'link' => '/api/calendar/' . $this->calendar->getUid() . '/' . $this->dateConf->next->format('j') . '-' . $this->dateConf->next->format('m') . '-' . $this->dateConf->next->format('Y') . '.json'
            ],
            'prev' => [
                'date' => $this->dateConf->prev,
                'day' => $this->dateConf->prev->format('j'),
                'month' => $this->dateConf->prev->format('m'),
                'year' => $this->dateConf->prev->format('Y'),
                'link' => '/api/calendar/' . $this->calendar->getUid() . '/' . $this->dateConf->prev->format('j') . '-' . $this->dateConf->prev->format('m') . '-' . $this->dateConf->prev->format('Y') . '.json'
            ],
        );
    }

    /**
     * @param \DateTime $startDate
     * @param integer $daysCount
     * @return array
     * @throws \Exception
     */
    private function getDaysArray($startDate, $daysCount)
    {
        $days = [];
        $date = clone $startDate;

        for ($i = 0; $i <= $daysCount; $i++) {
            $days[$i] = [
                'date' => clone $date,
                'timeslots' => $this->getTimeslotsForDay($date),
                'entries' => $this->getEntriesForDay($date),
                'isCurrentDay' => $this->isCurrentDay($date),
                'isNotInMonth' => !($date->format('m') == $this->dateConf->startOrig->format('m')),
                'isInPast' => $this->isInPast($date),
                'isSelectedDay' => $this->isSelectedDay($date)
            ];
            $days[$i]['bookableTimeslotsStatus'] = $this->getBookableTimeslotsStatus($days[$i]['timeslots']);
            $days[$i]['hasBookableTimeslots'] = (boolean)$days[$i]['bookableTimeslotsStatus'];
            $days[$i]['isDirectBookable'] = $this->isDirectBookable($days[$i]['entries']);
            $days[$i]['isBookable'] = ((!$days[$i]['isInPast'] || $days[$i]['isCurrentDay']) && ($days[$i]['hasBookableTimeslots'] || $days[$i]['isDirectBookable']));

            $date->modify('+1 day');
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

        // sort timeslots
        usort($timeslots, function(Timeslot $a, Timeslot $b) {
           return $a->getStartDate() > $b->getStartDate();
        });

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
                $entries[] = $entry;
            }
        }
        return $entries;
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
     * @param \DateTime $start
     * @param integer $numberOfWeeks
     * @return array
     * @throws \Exception
     */
    private function getWeeksArray($start, $numberOfWeeks)
    {
        $weeks = [];
        $weekstart = clone $start;

        for ($i = 0; $i < $numberOfWeeks; $i++) {

            $weekstart = clone $weekstart;

            $weeks[] = $this->getDaysArray($weekstart, 6);

            $weekstart->modify('next monday');
        }

        return $weeks;
    }
}
