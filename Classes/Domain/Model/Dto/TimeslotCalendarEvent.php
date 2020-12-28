<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Ics;

class TimeslotCalendarEvent extends CalendarEvent
{
    protected int $maxWeight = 0;

    protected int $bookedWeight = 0;

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

    /**
     * @param int $maxWeight
     */
    public function setMaxWeight(int $maxWeight): void
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * @param int $bookedWeight
     */
    public function setBookedWeight(int $bookedWeight): void
    {
        $this->bookedWeight = $bookedWeight;
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
