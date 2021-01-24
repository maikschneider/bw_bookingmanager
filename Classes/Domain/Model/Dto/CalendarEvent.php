<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use DateTime;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;

class CalendarEvent
{

    public const MODEL = '';

    protected string $title = '';

    protected DateTime $start;

    protected DateTime $end;

    protected bool $allDay = false;

    protected string $color = '';

    protected ?string $display = '';

    protected int $calendar = 0;

    /**
     * @param int $calendar
     */
    public function setCalendar(int $calendar): void
    {
        $this->calendar = $calendar;
    }

    protected string $url = '';

    protected $uid = 0;

    protected int $pid = 0;

    protected string $tooltip = '';

    public function __construct()
    {
        $this->start = new DateTime();
        $this->end = new DateTime();
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid): void
    {
        $this->uid = $uid;
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function getFullCalendarOutput(): array
    {
        $fullCalendarConfig = [
            'title' => $this->getTitle(),
            'start' => $this->start->format('Y-m-d\TH:i:s.v\Z'),
            'end' => $this->end->format('Y-m-d\TH:i:s.v\Z'),
            'allDay' => static::isFullDay($this->start, $this->end),
            'color' => $this->getColor(),
            'display' => $this->getDisplay(),
            'url' => $this->getUrl(),
            'tooltip' => $this->tooltip,
            //'groupId' => $this->uid,
            'extendedProps' => $this->getExtendedProps(),
            'classNames' => $this->getClassNames()
        ];

        return $fullCalendarConfig;
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
        return $startDate->format('H') === '00' && ((int)$endDate->format('H') >= 23 || (int)$endDate->format('H') === 0);
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getExtendedProps(): array
    {
        return [
            'isInPast' => $this->isInPast(),
            'model' => static::MODEL,
            'uid' => $this->uid,
            'calendar' => $this->calendar,
            'isSelected' => false,
            'uniqueId' => $this->getUniqueId()
        ];
    }

    public function isInPast(): bool
    {
        $now = new \DateTime('now');
        return $this->start < $now;
    }

    public function getUniqueId()
    {
        return md5(static::MODEL . '-' . $this->uid . '-' . $this->start->getTimestamp() . '-' . $this->end->getTimestamp());
    }

    public function getClassNames(): array
    {
        return array_map(static function ($key, $value) {
            if (is_bool($value) && $value) {
                return $key;
            }
            if (is_string($value) && $value !== '') {
                return $key . '-' . $value;
            }
        }, array_keys($this->getExtendedProps()), $this->getExtendedProps());
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

    public function addBackendEditActionLink(\TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder)
    {
    }

    public function getBackendReturnUrl(UriBuilder $uriBuilder): string
    {
        $params = [
            'id' => $this->pid
        ];

        return (string)$uriBuilder->buildUriFromRoute('bookingmanager_calendar', $params);
    }

    public function addBackendModuleToolTip()
    {
    }


    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    public function addBackendModalSettings($uriBuilder, $entryUid, $entryStart, $entryEnd)
    {
    }
}
