<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Blockslot;
use Blueways\BwBookingmanager\Domain\Model\Ics;

class BlockslotCalendarEvent extends CalendarEvent
{
    public const MODEL = 'Blockslot';

    protected ?string $display = 'background';

    protected string $color = 'rgba(255,0,0,0.5)';

    public static function createFromEntity(Blockslot $blockslot)
    {
        $event = new static();
        $event->setTitle($blockslot->getReason());
        $event->setStart($blockslot->getStartDate());
        $event->setEnd($blockslot->getEndDate());

        return $event;
    }

    public function getIcsTitle(Ics $ics): string
    {
        return $ics->getBlockslotTitle();
    }
}
