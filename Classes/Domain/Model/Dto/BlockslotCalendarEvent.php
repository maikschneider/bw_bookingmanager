<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Blockslot;

class BlockslotCalendarEvent extends CalendarEvent
{
    protected ?string $display = 'background';

    protected string $color = 'rgba(255,0,0,0.5)';

    public static function createFromEntity(Blockslot $blockslot)
    {
        $event = new self();
        $event->setTitle($blockslot->getReason());
        $event->setStart($blockslot->getStartDate());
        $event->setEnd($blockslot->getEndDate());

        return $event;
    }
}
