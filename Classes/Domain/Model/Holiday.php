<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use Blueways\BwBookingmanager\Utility\IcsUtility;
use DateTime;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * Blockslot
 */
class Holiday extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements CalendarEventInterface
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
     * name
     *
     * @var string
     */
    protected $name = '';

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
    public function setReason($name)
    {
        $this->name = $name;
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

    public function getIcsOutput(Ics $ics, ClassSchema $classSchema): string
    {
        $now = new DateTime();

        $this->startDate->setTimezone($now->getTimezone());
        $this->endDate->setTimezone($now->getTimezone());

        $icsText = "BEGIN:VEVENT
            " . IcsUtility::getIcsDates($this->startDate, $this->endDate) . "
            DTSTAMP:" . $now->format('Ymd\THis\Z') . "
            SUMMARY:" . IcsUtility::compileTemplate($ics->getHolidayTitle(), $this, $classSchema) . "
            DESCRIPTION:" . IcsUtility::compileTemplate($ics->getHolidayDescription(), $this, $classSchema) . "
            UID:timeslot-" . $this->getUid() . "-" . random_int(1, 9999999) . "
            STATUS:CONFIRMED
            LAST-MODIFIED:" . $now->format('Ymd\THis\Z') . "
            LOCATION:" . IcsUtility::compileTemplate($ics->getHolidayLocation(), $this, $classSchema) . "
            END:VEVENT\n";

        return $icsText;
    }

    public function getFullCalendarEvent(): array
    {
        $now = new DateTime();

        $this->startDate->setTimezone($now->getTimezone());
        $this->endDate->setTimezone($now->getTimezone());

        return [
            'title' => $this->name,
            'start' => $this->startDate->format(DateTime::ATOM),
            'end' => $this->endDate->format(DateTime::ATOM),
            'allDay' => IcsUtility::isFullDay($this->startDate, $this->endDate),
            'display' => 'background',
            'color' => 'rgba(250,129,0,0.9)'
        ];
    }
}
