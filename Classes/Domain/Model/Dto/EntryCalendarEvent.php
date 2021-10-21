<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use TYPO3\CMS\Backend\Routing\UriBuilder;

class EntryCalendarEvent extends CalendarEvent
{

    public const MODEL = 'Entry';

    public bool $editable = false;

    protected string $color = '';

    protected string $prename = '';

    protected string $name = '';

    protected bool $isSavedEntry = false;

    protected int $timeslot = 0;

    public static function createFromEntity(Entry $entry): EntryCalendarEvent
    {
        $title = $entry->getPrename() . ' ' . $entry->getName();

        $event = new static();
        $event->pid = (int)$entry->getPid();
        $event->setUid($entry->getUid());
        $event->setTitle($title);
        $event->setStart($entry->getStartDate());
        $event->setEnd($entry->getEndDate());
        $event->prename = $entry->getPrename();
        $event->name = $entry->getName();
        if ($entry->getCalendar() !== null && $entry->getCalendar()->getUid()) {
            $event->calendar = (int)$entry->getCalendar()->getUid();
        }
        if ($entry->getTimeslot() !== null && $entry->getTimeslot()->getUid()) {
            $event->setTimeslot($entry->getTimeslot()->getUid());
        }
        if ($entry->getCalendar()->getColor()) {
            $event->color = $entry->getCalendar()->getColor();
        }

        return $event;
    }

    public function setTimeslot(int $timeslot): void
    {
        $this->timeslot = $timeslot;
    }

    public function getIcsTitle(Ics $ics): string
    {
        return $ics->getEntryTitle();
    }

    public function addBackendEditActionLink(UriBuilder $uriBuilder)
    {
        $urlParams = [
            'edit' => [
                'tx_bwbookingmanager_domain_model_entry' => [
                    $this->uid => 'edit'
                ]
            ],
            'returnUrl' => $this->getBackendReturnUrl($uriBuilder)
        ];

        $this->url = (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParams);
    }

    public function addBackendModuleToolTip(): void
    {
        $this->tooltip = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:showEntry') . ' →';
    }

    public function getExtendedProps(): array
    {
        $props = parent::getExtendedProps();

        $props['isSavedEntry'] = $this->isSavedEntry;
        $props['timeslot'] = $this->timeslot;

        return $props;
    }

    public function getFullCalendarOutput(): array
    {
        $output = parent::getFullCalendarOutput();
        $output['editable'] = $this->editable;
        return $output;
    }

    public function addBackendModalSettings(UriBuilder $uriBuilder, BackendCalendarViewState $viewState): void
    {
        // editable flag
        if ($this->uid === (int)$viewState->entryUid || (string)$this->uid === (string)$viewState->entryUid) {
            $this->editable = true;
            $this->isSavedEntry = true;

            // adjust start/end in case of edited in modal
            if ($viewState->getEntryStartDate() && $viewState->getEntryEndDate()) {
                $this->start = $viewState->getEntryStartDate();
                $this->end = $viewState->getEntryEndDate();
            }
        }
    }
}
