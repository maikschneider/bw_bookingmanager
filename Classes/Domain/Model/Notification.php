<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Notification extends AbstractEntity
{
    const EVENT_CREATION = 0;

    const EVENT_DELETION = 1;

    protected int $event = 0;

    protected string $name = '';

    protected string $email = '';

    /**
     * @var string
     * @deprecated
     */
    protected string $hook = '';

    protected string $template = '';

    protected string $emailSubject = '';

    protected string $conditions = '';

    public function setConditions(string $conditions): void
    {
        $this->conditions = $conditions;
    }

    public function getConditions(): string
    {
        return $this->conditions;
    }

    public function getEvent(): int
    {
        return $this->event;
    }

    public function setEvent(int $event): void
    {
        $this->event = $event;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(string $emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @deprecated
     */
    public function getHook(): string
    {
        return $this->hook;
    }

    /**
     * @deprecated
     */
    public function setHook(string $hook): void
    {
        $this->hook = $hook;
    }

    /**
     * @var ObjectStorage<Calendar>
     */
    protected ObjectStorage $calendars;

    public function addCalendar(Calendar $calendar)
    {
        $this->calendars->attach($calendar);
    }

    public function removeCalendar(Calendar $calendarToRemove): void
    {
        $this->calendars->detach($calendarToRemove);
    }

    /**
     * @return ObjectStorage<Calendar> $calendars
     */
    public function getCalendars(): ObjectStorage
    {
        return $this->calendars;
    }

    /**
     * @param ObjectStorage<Calendar> $calendars
     */
    public function setCalendars(ObjectStorage $calendars)
    {
        $this->calendars = $calendars;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * checks if a hook is set and not default (=NONE)
     *
     * @deprecated
     */
    public function hasHook(): bool
    {
        return $this->hook && $this->hook != '' && $this->hook != '0';
    }
}
