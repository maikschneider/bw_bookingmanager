<?php
namespace Blueways\BwBookingmanager\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
class Notification extends AbstractEntity
{
    const EVENT_CREATION = 0;
    const EVENT_DELETION = 1;

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
     * @return int
     */
    public function getEvent(): int
    {
        return $this->event;
    }

    /**
     * @param int $event
     */
    public function setEvent(int $event)
    {
        $this->event = $event;
    }

    /**
     * @var int
     */
    protected $event = self::EVENT_CREATION;

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
     * @var ObjectStorage<Calendar>
     * @Extbase\ORM\Lazy
     */
    protected $calendars = null;

    /**
     * Adds a Calendar
     *
     * @param Calendar $calendar
     * @return void
     */
    public function addCalendar(Calendar $calendar)
    {
        $this->calendars->attach($calendar);
    }

    /**
     * Removes a Calendar
     *
     * @param Calendar $calendarToRemove The Calendar to be removed
     * @return void
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
     * @return void
     */
    public function setCalendars(ObjectStorage $calendars)
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

    /**
     * checks if a hook is set and not default (=NONE)
     * @return boolean
     */
    public function hasHook()
    {
        return ($this->hook && $this->hook!='' && $this->hook!='0');
    }
}
