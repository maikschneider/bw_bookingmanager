<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Holiday;
use Blueways\BwBookingmanager\Domain\Model\Ics;

class HolidayCalendarEvent extends CalendarEvent
{

    public const MODEL = 'Holiday';

    protected ?string $display = 'background';

    protected string $color = 'rgba(250,129,0,0.9)';

    public static function createFromEntity(Holiday $holiday): HolidayCalendarEvent
    {
        $event = new self();
        $event->setTitle($holiday->getName());
        $event->setStart($holiday->getStartDate());
        $event->setEnd($holiday->getEndDate());

        return $event;
    }

    public function getIcsTitle(Ics $ics): string
    {
        return $ics->getHolidayTitle();
    }
}
