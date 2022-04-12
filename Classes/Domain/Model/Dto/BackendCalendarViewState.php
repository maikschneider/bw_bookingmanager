<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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

    public int $timeslot = 0;

    public ?int $calendar;

    public $entryUid;

    public $entryStart;

    public $entryEnd;

    public string $buttonSaveText = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:modal.save';

    public string $buttonCancelText = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:modal.cancel';

    public string $warningTitle = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:modal.warningTitle';

    public string $warningText = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:modal.warningText';

    public string $warningButton = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:modal.warningButton';

    public array $calendarOptions = [];

    public function __construct(int $pid)
    {
        $this->pid = $pid;
        $this->language = $this->getLanguageService()->lang;
        $this->buttonSaveText = $this->getLanguageService()->sL($this->buttonSaveText);
        $this->buttonCancelText = $this->getLanguageService()->sL($this->buttonCancelText);
        $this->warningTitle = $this->getLanguageService()->sL($this->warningTitle);
        $this->warningText = $this->getLanguageService()->sL($this->warningText);
        $this->warningButton = $this->getLanguageService()->sL($this->warningButton);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    public static function getFromUserSettings(int $pid): BackendCalendarViewState
    {
        $saveState = $GLOBALS['BE_USER']->getModuleData('bwbookingmanager/calendarViewState-' . $pid) ?? '';
        return $saveState !== '' ? unserialize(
            $saveState,
            ['allowed_classes' => [self::class]]
        ) : new BackendCalendarViewState($pid);
    }

    public static function createFromApiRequest(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();

        $state = new self((int)$params['pid']);
        $state->entryUid = $params['entryUid'] !== 'null' ? $params['entryUid'] : null;
        $state->entryStart = $params['entryStart'] !== 'null' ? $params['entryStart'] : null;
        $state->entryEnd = $params['entryEnd'] !== 'null' ? $params['entryEnd'] : null;
        $state->start = $params['start'];
        $state->end = $params['end'];
        $state->calendar = $params['calendar'] !== 'null' ? (int)$params['calendar'] : 0;
        $state->timeslot = $params['timeslot'] !== 'null' ? (int)$params['timeslot'] : 0;

        return $state;
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
        /** @var Calendar $calendar */
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

    public function hasDirectBookingCalendar(): bool
    {
        return $this->getFirstDirectBookingCalendar() !== null;
    }

    public function getFirstDirectBookingCalendar()
    {
        foreach ($this->currentCalendars ?? [] as $calendar) {
            if ($calendar['directBooking']) {
                return $calendar;
            }
        }
        return null;
    }

    public function getStartDate()
    {
        return new \DateTime($this->start);
    }

    public function getEndDate()
    {
        return new \DateTime($this->end);
    }

    public function getEntryStartDate()
    {
        if (!$this->entryStart) {
            return null;
        }

        return (new \DateTime())->setTimestamp((int)$this->entryStart);
    }

    public function getEntryEndDate()
    {
        if (!$this->entryEnd) {
            return null;
        }

        return (new \DateTime())->setTimestamp((int)$this->entryEnd);
    }

    public function isNewModalView(): bool
    {
        return \str_starts_with((string)$this->entryUid, 'NEW');
    }

    public function addTypoScriptOptionOverrides(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $tsService = $objectManager->get(TypoScriptService::class);
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $settings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        if (isset($settings['module.']['tx_bwbookingmanager.']['settings.']['calendarOptions.'])) {
            $settings = $settings['module.']['tx_bwbookingmanager.']['settings.']['calendarOptions.'];
            $this->calendarOptions = $tsService->convertTypoScriptArrayToPlainArray($settings);
        }
    }
}
