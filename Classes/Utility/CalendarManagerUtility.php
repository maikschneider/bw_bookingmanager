<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;

class CalendarManagerUtility
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar;

    /**
     * CalendarManagerUtility constructor.
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    public function __construct(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array
     */
    public function getConfiguration(DateConf $dateConf): array
    {

    }
}
