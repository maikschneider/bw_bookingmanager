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
 * Notification
 */
class Notification extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * condition
     *
     * @var boolean
     */
    protected $condition = FALSE;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the condition
     *
     * @return bool $condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Sets the condition
     *
     * @param bool $condition
     * @return void
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Returns the boolean state of condition
     *
     * @return bool
     */
    public function isCondition()
    {
        return $this->condition;
    }

    /**
     * calendars
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar>
     * @lazy
     */
    protected $calendars = null;

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