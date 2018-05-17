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
     * hook
     *
     * @var string
     */
    protected $hook = '';

    /**
     * template
     *
     * @var string
     */
    protected $template = '';

    /**
     * emailSubject
     *
     * @var string
     */
    protected $emailSubject = '';

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
     * Returns the emailSubject
     *
     * @return string $emailSubject
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * Sets the emailSubject
     *
     * @param string $emailSubject
     * @return void
     */
    public function setEmailSubject($emailSubject)
    {
        $this->emailSubject = $emailSubject;
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
     * Returns the hook
     *
     * @return int $hook
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Sets the hook
     *
     * @param int $hook
     * @return void
     */
    public function setHook($hook)
    {
        $this->hook = $hook;
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

    /**
     * Returns the template
     *
     * @return string $template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the template
     *
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

}