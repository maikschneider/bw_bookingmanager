<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use DateTime;
use TYPO3\CMS\Backend\Routing\UriBuilder;

class EntryCalendarEvent extends CalendarEvent
{

    public const MODEL = 'Entry';

    public bool $editable = false;

    protected string $color = '';

    protected string $prename = '';

    protected string $name = '';

    protected bool $isSavedEntry = false;

    public static function createFromEntity(Entry $entry): EntryCalendarEvent
    {
        $title = $entry->getPrename() . ' ' . $entry->getName();

        $event = new self();
        $event->pid = (int)$entry->getPid();
        $event->setUid($entry->getUid());
        $event->setTitle($title);
        $event->setStart($entry->getStartDate());
        $event->setEnd($entry->getEndDate());
        $event->prename = $entry->getPrename();
        $event->name = $entry->getName();

        return $event;
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

    public function addBackendModuleToolTip()
    {
        $this->tooltip = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:showEntry') . ' →';
    }

    public function addBackendModalIsSelectedEntryTimeslot($entryUid): void
    {

    }

    public function getExtendedProps(): array
    {
        $props = parent::getExtendedProps();

        $props['isSavedEntry'] = $this->isSavedEntry;

        return $props;
    }

    public function getFullCalendarOutput(): array
    {
        $output = parent::getFullCalendarOutput();
        $output['editable'] = $this->editable;
        return $output;
    }

    public function addBackendModalSettings($uriBuilder, $entryUid, $entryStart, $entryEnd)
    {
        // editable flag
        if ($this->uid === (int)$entryUid || (string)$this->uid === (string)$entryUid) {
            $this->editable = true;
            $this->isSavedEntry = true;

            // adjust start/end in case of edited in modal
            if ($entryStart && $entryEnd) {
                $this->start = $entryStart;
                $this->end = $entryEnd;
            }
        }
    }
}
