<?php

namespace Blueways\BwBookingmanager\Domain\Model;

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
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @var \DateTime
     */
    protected $endDate;

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
     * e.g. 73 => [1,0,0,1,0,0,1] => [0,3,6] (Su, We, Sa)
     *
     * @return array
     */
    public function getOptionsArray()
    {
        $selectedDays = [];

        foreach (array_reverse(str_split(decbin($this->getOptions()))) as $key => $value) {
            if ($value === '1') {
                $selectedDays[] = $key;
            }
        }

        return $selectedDays;
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
