<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/
/**
 * Blockslot
 */
class Blockslot extends AbstractEntity
{
    /**
     * startDate
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * endDate
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * reason
     *
     * @var string
     */
    protected $reason = '';

    /**
     * calendars
     *
     * @var ObjectStorage<Calendar>
     * @Extbase\ORM\Lazy
     */
    protected $calendars;

    /**
     * Returns the reason
     *
     * @return string $reason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets the reason
     *
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Adds a Calendar
     *
     * @param Calendar $calendar
     */
    public function addCalendar(Calendar $calendar)
    {
        $this->calendars->attach($calendar);
    }

    /**
     * Removes a Calendar
     *
     * @param Calendar $calendarToRemove The Calendar to be removed
     */
    public function removeCalendar(Calendar $calendarToRemove)
    {
        $this->calendars->detach($calendarToRemove);
    }

    /**
     * Returns the calendars
     *
     * @return ObjectStorage<Calendar> $calendars
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * Sets the calendars
     *
     * @param ObjectStorage<Calendar> $calendars
     */
    public function setCalendars(ObjectStorage $calendars)
    {
        $this->calendars = $calendars;
    }

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
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }
}
