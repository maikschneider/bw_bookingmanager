<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * Entry
 */
class Ics extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar>
     */
    protected $calendars;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $options;

    /**
     * @var string
     */
    protected $entryTitle;

    /**
     * @var string
     */
    protected $entryLocation;

    /**
     * @var string
     */
    protected $entryDescription;

    /**
     * @var string
     */
    protected $timeslotTitle;

    /**
     * @var string
     */
    protected $timeslotLocation;

    /**
     * @var string
     */
    protected $timeslotDescription;

    /**
     * @var string
     */
    protected $blockslotTitle;

    /**
     * @var string
     */
    protected $blockslotLocation;

    /**
     * @var string
     */
    protected $blockslotDescription;

    /**
     * @var string
     */
    protected $holidayTitle;

    /**
     * @return string
     */
    public function getHolidayTitle(): string
    {
        return $this->holidayTitle;
    }

    /**
     * @param string $holidayTitle
     */
    public function setHolidayTitle(string $holidayTitle)
    {
        $this->holidayTitle = $holidayTitle;
    }

    /**
     * @return string
     */
    public function getHolidayLocation(): string
    {
        return $this->holidayLocation;
    }

    /**
     * @param string $holidayLocation
     */
    public function setHolidayLocation(string $holidayLocation)
    {
        $this->holidayLocation = $holidayLocation;
    }

    /**
     * @return string
     */
    public function getHolidayDescription(): string
    {
        return $this->holidayDescription;
    }

    /**
     * @param string $holidayDescription
     */
    public function setHolidayDescription(string $holidayDescription)
    {
        $this->holidayDescription = $holidayDescription;
    }

    /**
     * @var string
     */
    protected $holidayLocation;

    /**
     * @var string
     */
    protected $holidayDescription;

    /**
     * @var int
     */
    protected $startDate;

    /**
     * @var int
     */
    protected $endDate;

    /**
     * Ics constructor.
     */
    public function __construct()
    {
        $this->calendars = new ObjectStorage();
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        $date = new \DateTime();

        if ($this->startDate) {
            $timeTransformations = ['', 'first day of previous month', 'first day of january this year', '-100 days'];
            $date->setTimestamp(strtotime($timeTransformations[$this->startDate]));
        }

        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        $date = new \DateTime();

        $timeTransformations = ['+6 months', '+1 year', '+2 years'];
        $date->setTimestamp(strtotime($timeTransformations[$this->endDate]));

        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getCalendars(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->calendars;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $calendars
     */
    public function setCalendars(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $calendars)
    {
        $this->calendars = $calendars;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEntryTitle(): string
    {
        return $this->entryTitle;
    }

    /**
     * @param string $entryTitle
     */
    public function setEntryTitle(string $entryTitle)
    {
        $this->entryTitle = $entryTitle;
    }

    /**
     * @return string
     */
    public function getEntryLocation(): string
    {
        return $this->entryLocation;
    }

    /**
     * @param string $entryLocation
     */
    public function setEntryLocation(string $entryLocation)
    {
        $this->entryLocation = $entryLocation;
    }

    /**
     * @return string
     */
    public function getEntryDescription(): string
    {
        return $this->entryDescription;
    }

    /**
     * @param string $entryDescription
     */
    public function setEntryDescription(string $entryDescription)
    {
        $this->entryDescription = $entryDescription;
    }

    /**
     * @return string
     */
    public function getTimeslotTitle(): string
    {
        return $this->timeslotTitle;
    }

    /**
     * @param string $timeslotTitle
     */
    public function setTimeslotTitle(string $timeslotTitle)
    {
        $this->timeslotTitle = $timeslotTitle;
    }

    /**
     * @return string
     */
    public function getTimeslotLocation(): string
    {
        return $this->timeslotLocation;
    }

    /**
     * @param string $timeslotLocation
     */
    public function setTimeslotLocation(string $timeslotLocation)
    {
        $this->timeslotLocation = $timeslotLocation;
    }

    /**
     * @return string
     */
    public function getTimeslotDescription(): string
    {
        return $this->timeslotDescription;
    }

    /**
     * @param string $timeslotDescription
     */
    public function setTimeslotDescription(string $timeslotDescription)
    {
        $this->timeslotDescription = $timeslotDescription;
    }

    /**
     * @return string
     */
    public function getBlockslotTitle(): string
    {
        return $this->blockslotTitle;
    }

    /**
     * @param string $blockslotTitle
     */
    public function setBlockslotTitle(string $blockslotTitle)
    {
        $this->blockslotTitle = $blockslotTitle;
    }

    /**
     * @return string
     */
    public function getBlockslotLocation(): string
    {
        return $this->blockslotLocation;
    }

    /**
     * @param string $blockslotLocation
     */
    public function setBlockslotLocation(string $blockslotLocation)
    {
        $this->blockslotLocation = $blockslotLocation;
    }

    /**
     * @return string
     */
    public function getBlockslotDescription(): string
    {
        return $this->blockslotDescription;
    }

    /**
     * @param string $blockslotDescription
     */
    public function setBlockslotDescription(string $blockslotDescription)
    {
        $this->blockslotDescription = $blockslotDescription;
    }

    /**
     * e.g. 73 => [1,0,0,1,0,0,1]
     *
     * @return int[]
     */
    public function getOptionsArray(): array
    {
        return array_reverse(array_map('intval', str_split(decbin($this->getOptions()))));
    }

    /**
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @param int $options
     */
    public function setOptions(int $options)
    {
        $this->options = $options;
    }
}
