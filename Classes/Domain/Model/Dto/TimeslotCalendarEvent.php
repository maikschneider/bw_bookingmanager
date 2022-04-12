<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TimeslotCalendarEvent extends CalendarEvent
{
    public const MODEL = 'Timeslot';

    protected int $maxWeight = 0;

    protected int $bookedWeight = 0;

    protected int $isBookableHooks = 0;

    protected array $entries = [];

    private bool $isSelectedEntryTimeslot = false;

    public static function createFromRawSql(array $timeslot): TimeslotCalendarEvent
    {
        $event = new static();
        $event->uid = $timeslot['uid'];
        $event->pid = $timeslot['pid'];
        $event->start->setTimestamp($timeslot['t_start_date']);
        $event->end->setTimestamp($timeslot['t_end_date']);
        $event->maxWeight = $timeslot['max_weight'];
        $event->bookedWeight = $timeslot['booked_weight'];
        $event->calendar = $timeslot['calendar'];
        $event->isBookableHooks = $timeslot['is_bookable_hooks'];
        $event->entries = $timeslot['entries'] ? array_map('intval', explode(',', $timeslot['entries'])) : [];

        return $event;
    }

    public function getTitle(): string
    {
        if ($this->title !== '') {
            return $this->title;
        }
        $title = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:';
        if ($this->isInPast() || !$this->getIsBookableByHooks()) {
            $title .= 'notBookable';
        } elseif ($this->isBookedUp()) {
            $title .= 'booked';
        } else {
            $title .= 'free';
        }
        $title = $this->getLanguageService()->sL($title);
        $title .= $this->maxWeight === 1 ? '' : ' ' . $this->bookedWeight . '/' . $this->maxWeight;

        return $title;
    }

    private function getIsBookableByHooks()
    {
        $activeHooks = $this->getIsBookableHooksArray();

        foreach ($activeHooks as $key => $isActiveHook) {
            // dont call hook if not checked via TCA
            if (!$isActiveHook) {
                continue;
            }

            // get the hook from offset of global registed hooks array, make instance and call it
            $hookClassName = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/timeslot']['isBookable'][$key];
            $_procObj = GeneralUtility::makeInstance($hookClassName);
            if (!$_procObj->isBookable($this)) {
                return false;
            }
        }

        return true;
    }

    public function getIsBookableHooksArray()
    {
        $isBookableHooks = $this->isBookableHooks;

        return array_map(
            function ($value) {
                return $value === '1';
            },
            array_reverse(str_split(decbin($isBookableHooks)))
        );
    }

    public function isBookedUp()
    {
        return $this->bookedWeight >= $this->maxWeight;
    }

    public function addBackendEditActionLink(UriBuilder $uriBuilder)
    {
        if (!$this->getIsBookable()) {
            return;
        }

        $urlParams = [
            'edit' => [
                'tx_bwbookingmanager_domain_model_entry' => [
                    $this->pid => 'new',
                ],
            ],
            'defVals' => [
                'tx_bwbookingmanager_domain_model_entry' => [
                    'calendar' => $this->calendar,
                    'timeslot' => $this->uid,
                    'confirmed' => 1,
                    'start_date' => $this->start->getTimestamp(),
                    'end_date' => $this->end->getTimestamp(),
                ],
            ],
            'returnUrl' => $this->getBackendReturnUrl($uriBuilder),
        ];

        $this->url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParams);
    }

    public function getIsBookable(): bool
    {
        // check date (only if in future)
        if ($this->isInPast()) {
            return false;
        }

        // check weight
        if ($this->isBookedUp()) {
            return false;
        }

        // check activated hooks hooks
        if (!$this->getIsBookableByHooks()) {
            return false;
        }

        return true;
    }

    public function getColor(): string
    {
        $now = new \DateTime('now');
        if ($this->start < $now) {
            return '#848484';
        }
        return $this->getIsBookable() ? 'green' : 'red';
    }

    public function getIcsTitle(Ics $ics): string
    {
        return $ics->getTimeslotTitle();
    }

    public function getIcsDescription(Ics $ics): string
    {
        return $ics->getTimeslotDescription();
    }

    public function getIcsLocation(Ics $ics): string
    {
        return $ics->getTimeslotLocation();
    }

    public function getStartDate()
    {
        return $this->start;
    }

    public function addBackendModuleToolTip(): void
    {
        if (!$this->getIsBookable()) {
            return;
        }

        $this->tooltip = '+ ' . $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:flexforms_general.mode.entry_new');
    }

    public function getExtendedProps(): array
    {
        $props = parent::getExtendedProps();

        $props['isBookable'] = $this->getIsBookable();
        $props['entries'] = $this->entries;
        $props['maxWeight'] = $this->maxWeight;
        $props['isSelectedEntryTimeslot'] = $this->isSelectedEntryTimeslot;

        return $props;
    }

    public function addBackendModalSettings($uriBuilder, $viewState): void
    {
        // check if this is the timeslot of the selected entry
        if (in_array((int)$viewState->entryUid, $this->entries, true)) {
            $this->isSelectedEntryTimeslot = true;
        }

        // check if this is the selected timeslot via defVals (not persisted in database)
        if ($viewState->isNewModalView() && $viewState->timeslot && $viewState->timeslot === $this->uid && $viewState->getEntryStartDate()->getTimestamp() === $this->start->getTimestamp() && $viewState->getEntryEndDate()->getTimestamp() === $this->end->getTimestamp()) {
            $this->isSelectedEntryTimeslot = true;
        }

        // make timeslots clickable
        $this->setUrl('#');
    }
}
