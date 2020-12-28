<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Ics;

class TimeslotCalendarEvent extends CalendarEvent
{

    protected int $maxWeight = 0;

    protected int $bookedWeight = 0;

    public static function createFromRawSql(array $timeslot): TimeslotCalendarEvent
    {
        $event = new self();
        $event->uid = $timeslot['uid'];
        $event->pid = $timeslot['pid'];
        $event->start->setTimestamp($timeslot['t_start_date']);
        $event->end->setTimestamp($timeslot['t_end_date']);
        $event->maxWeight = $timeslot['max_weight'];
        $event->bookedWeight = $timeslot['booked_weight'];
        $event->calendar = $timeslot['calendar'];

        return $event;
    }

    public function getTitle(): string
    {
        if ($this->title !== '') {
            return $this->title;
        }
        $title = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_timeslot.xlf:';
        $title .= $this->getIsBookable() ? 'free' : 'booked';
        $title .= $this->maxWeight === 1 ? '' : ' ' . $this->bookedWeight . '/' . $this->maxWeight;

        return $title;
    }

    public function getIsBookable(): bool
    {
        // check date (only if in future)
        $now = new \DateTime('now');
        if ($this->start < $now) {
            return false;
        }

        // check weight
        if ($this->bookedWeight >= $this->maxWeight) {
            return false;
        }

        // @TODO: check Hooks

        return true;
    }

    public function addBackendEditActionLink(\TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder)
    {
        if (!$this->getIsBookable()) {
            return;
        }

        $urlParams = [
            'edit' => [
                'tx_bwbookingmanager_domain_model_entry' => [
                    $this->pid => 'new'
                ]
            ],
            'defVals' => [
                'tx_bwbookingmanager_domain_model_entry' => [
                    'calendar' => $this->calendar,
                    'startDate' => $this->start->getTimestamp(),
                    'endDate' => $this->end->getTimestamp()
                ]
            ],

        ];

        $this->url = $uriBuilder->buildUriFromRoute('record_edit', $urlParams)->__toString();
    }

    public function getColor(): string
    {
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
}
