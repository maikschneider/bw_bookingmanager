<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use DateTime;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;

class CalendarEvent
{

    protected string $title = '';

    protected DateTime $start;

    protected DateTime $end;

    protected bool $allDay = false;

    protected string $color = '';

    protected ?string $display = '';

    protected int $calendar = 0;

    public function __construct()
    {
        $this->start = new DateTime();
        $this->end = new DateTime();
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * @return int
     */
    public function getCalendar(): int
    {
        return $this->calendar;
    }

    /**
     * @param int $calendar
     */
    public function setCalendar(int $calendar): void
    {
        $this->calendar = $calendar;
    }

    public function setStartFromUnixTimestamp(int $timestamp): void
    {
        $this->start->setTimestamp($timestamp);
    }

    public function setEndFromUnixTimestamp(int $timestamp): void
    {
        $this->end->setTimestamp($timestamp);
    }

    public function getFullCalendarOutput(): array
    {
        // @TODO: check if necessary
//        $now = new DateTime();
//        $this->start->setTimezone($now->getTimezone());
//        $this->end->setTimezone($now->getTimezone());

        return [
            'title' => $this->getTitle(),
            'start' => $this->start->format(DateTime::ATOM),
            'end' => $this->end->format(DateTime::ATOM),
            'allDay' => static::isFullDay($this->start, $this->end),
            'color' => $this->getColor(),
            'display' => $this->getDisplay()
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public static function isFullDay(\DateTime $startDate, \DateTime $endDate): bool
    {
        return $startDate->format('H') === '00' && (int)$endDate->format('H') >= 23;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function translateTitle(LanguageService $llService): void
    {
        $titleParts = explode(' ', $this->getTitle());
        foreach ($titleParts as &$part) {
            $part = $llService->sL($part);
        }
        unset($part);
        $this->setTitle(implode(' ', $titleParts));
    }

    public function getIcsOutput(Ics $ics)
    {
        $now = new DateTime();

        $this->start->setTimezone($now->getTimezone());
        $this->end->setTimezone($now->getTimezone());

        $icsText = "BEGIN:VEVENT
            " . IcsUtility::getIcsDates($this->start, $this->end) . "
            DTSTAMP:" . $now->format('Ymd\THis') . "
            SUMMARY:" . IcsUtility::compileTemplate($this->getIcsTitle($ics), $this) . "
            DESCRIPTION:" . IcsUtility::compileTemplate($this->getIcsDescription($ics), $this) . "
            UID:randomId-" . random_int(1, 9999999) . "
            STATUS:CONFIRMED
            LAST-MODIFIED:" . $now->format('Ymd\THis') . "
            LOCATION:" . IcsUtility::compileTemplate($this->getIcsLocation($ics), $this) . "
            END:VEVENT\n";

        return $icsText;
    }

    public function getIcsTitle(Ics $ics): string
    {
        return '';
    }

    public function getIcsDescription(Ics $ics): string
    {
        return '';
    }

    public function getIcsLocation(Ics $ics): string
    {
        return '';
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }
}
