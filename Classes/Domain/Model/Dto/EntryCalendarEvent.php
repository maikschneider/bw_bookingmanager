<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use TYPO3\CMS\Backend\Routing\UriBuilder;

class EntryCalendarEvent extends CalendarEvent
{

    public const MODEL = 'Entry';

    protected string $color = '';

    protected string $prename = '';

    protected string $name = '';

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
}
