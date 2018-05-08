<?php
namespace Blueways\BwBookingmanager\Domain\Model;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * Timeslot
 */
class Timeslot extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    const REPEAT_NO = 0;
    const REPEAT_DAILY = 1;
    const REPEAT_WEEKLY = 2;
    const REPEAT_MONTHLY = 3;

    /**
     * startDate
     *
     * @var \DateTime
     */
    protected $startDate = null;

    /**
     * endDate
     *
     * @var \DateTime
     */
    protected $endDate = null;

    /**
     * repeatType
     *
     * @var int
     */
    protected $repeatType = Timeslot::REPEAT_NO;

    /**
     * maxWeight
     *
     * @var int
     */
    protected $maxWeight = 0;

    /**
     * entries
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry>
     * @cascade remove
     */
    protected $entries = null;

    /**
     * calendars
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar>
     * @lazy
     */
    protected $calendars = null;

    /**
     * Returns the startDate
     *
     * @return \DateTime $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param \DateTime $startDate
     * @return void
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the endDate
     *
     * @return \DateTime $endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the endDate
     *
     * @param \DateTime $endDate
     * @return void
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Returns the repeatType
     *
     * @return int $repeatType
     */
    public function getRepeatType()
    {
        return $this->repeatType;
    }

    /**
     * Sets the repeatType
     *
     * @param int $repeatType
     * @return void
     */
    public function setRepeatType($repeatType)
    {
        $this->repeatType = $repeatType;
    }

    /**
     * Returns the maxWeight
     *
     * @return int $maxWeight
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Sets the maxWeight
     *
     * @param int $maxWeight
     * @return void
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->entries = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->calendars = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Entry
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @return void
     */
    public function addEntry(\Blueways\BwBookingmanager\Domain\Model\Entry $entry)
    {
        $this->entries->attach($entry);
    }

    /**
     * Removes a Entry
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entryToRemove The Entry to be removed
     * @return void
     */
    public function removeEntry(\Blueways\BwBookingmanager\Domain\Model\Entry $entryToRemove)
    {
        $this->entries->detach($entryToRemove);
    }

    /**
     * Returns the entries
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry> $entries
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Sets the entries
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry> $entries
     * @return void
     */
    public function setEntries(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Adds a Calendar
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @return void
     */
    public function addCalendar(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar)
    {
        $this->calendars->attach($calendar);
    }

    /**
     * Removes a Calendar
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendarToRemove The Calendar to be removed
     * @return void
     */
    public function removeCalendar(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendarToRemove)
    {
        $this->calendars->detach($calendarToRemove);
    }

    /**
     * Returns the calendars
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * Sets the calendars
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     * @return void
     */
    public function setCalendars(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $calendars)
    {
        $this->calendars = $calendars;
    }
}
