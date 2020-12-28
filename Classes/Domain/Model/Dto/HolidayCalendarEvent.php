<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Holiday;

class HolidayCalendarEvent extends CalendarEvent
{

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
}
