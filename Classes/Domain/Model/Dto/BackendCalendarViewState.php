<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use TYPO3\CMS\Core\Localization\LanguageService;

class BackendCalendarViewState
{

    public int $pid;

    public string $language;

    public array $currentCalendars;

    public string $start;

    public string $end;

    public string $calendarView = 'dayGridMonth';

    public bool $pastEntries = false;

    public bool $pastTimeslots = false;

    public bool $notBookableTimeslots = false;

    public bool $futureEntries = false;

    public ?int $timeslot;

    public ?int $calendar;

    public $entryUid;

    public function __construct(int $pid)
    {
        $this->pid = $pid;
        $this->language = $this->getLanguageService()->lang;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    public static function getFromUserSettings(int $pid): BackendCalendarViewState
    {
        $saveState = $GLOBALS['BE_USER']->getModuleData('bwbookingmanager/calendarViewState-' . $pid) ?? '';
        return $saveState !== '' ? unserialize($saveState) : new BackendCalendarViewState($pid);
    }

    public function overrideFromApiSave($postData)
    {
        $this->pid = (int)$postData['pid'];
        $this->pastEntries = $postData['pastEntries'] === 'true';
        $this->pastTimeslots = $postData['pastTimeslots'] === 'true';
        $this->notBookableTimeslots = $postData['notBookableTimeslots'] === 'true';
        $this->futureEntries = $postData['futureEntries'] === 'true';
        $this->calendarView = $postData['calendarView'];
        $this->start = $postData['start'];
    }

    public function persistInUserSettings()
    {
        $moduleDataIdentifier = 'bwbookingmanager/calendarViewState-' . $this->pid;
        $saveState = serialize($this);
        $GLOBALS['BE_USER']->pushModuleData($moduleDataIdentifier, $saveState);
    }

    public function addCalendars($calendars)
    {
        if ($calendars) {
            $this->currentCalendars = $this->getCurrentCalendarSettings($calendars);
        }
    }

    protected function getCurrentCalendarSettings($calendars): array
    {
        $currentCalendars = [];
        /** @var \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar */
        foreach ($calendars as $calendar) {
            $currentCal = [];
            $currentCal['uid'] = $calendar->getUid();
            $currentCal['directBooking'] = $calendar->isDirectBooking();
            $currentCal['defaultStartTime'] = $calendar->getDefaultStartTime();
            $currentCal['defaultEndTime'] = $calendar->getDefaultEndTime();
            $currentCal['minLength'] = $calendar->getMinLength();
            $currentCal['minOffset'] = $calendar->getMinOffset();
            $currentCalendars[] = $currentCal;
        }
        return $currentCalendars;
    }

}
