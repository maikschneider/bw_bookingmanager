<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

class DateConf
{

    CONST DEFAULT_DAYS_LENGTH = 150;

    /**
     * @var \DateTime
     */
    public $start;

    /**
     * @var \DateTime
     */
    public $end;

    /**
     * @var \DateTime
     */
    public $next;

    /**
     * @var \DateTime
     */
    public $prev;

    /**
     * @var integer
     */
    public $viewType;

    /**
     * DateConf constructor.
     *
     * @param \DateTime $date
     * @param integer $viewType
     */
    public function __construct($viewType, $date)
    {
        $this->viewType = $viewType;

        $this->configureSelf($date);
    }

    private function configureSelf($date)
    {
        if ($this->viewType === 0) {
            $this->start = clone self::getMonthStart($date);
            $this->end = clone self::getMonthEnd($date);
            $this->next = clone self::getNextMonth($date);
            $this->prev = clone self::getPrevMonth($date);
        }

        if ($this->viewType === 1) {
            $this->start = clone self::getWeekStart($date);
            $this->end = clone self::getWeekEnd($date);
            $this->next = clone self::getNextWeek($date);
            $this->prev = clone self::getPrevWeek($date);
        }

        if ($this->viewType === 2) {
            $this->start = clone self::getDayStart($date);
            $this->end = clone self::getDayEnd($date);
            $this->next = clone self::getNextDay($date);
            $this->prev = clone self::getPrevDay($date);
        }
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getMonthStart($date)
    {
        $date->modify('first day of this month');
        $date->modify('last monday');
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getMonthEnd($date)
    {
        $date->modify('last day of this month');
        $date->modify('next sunday');
        $date->setTime(23, 59, 59);
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextMonth($date)
    {
        $date->modify('first day of next month');
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevMonth($date)
    {
        $date->modify('first day of last month');
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getWeekStart($date)
    {
        $date->modify('tomorrow');
        $date->modify('last monday');
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getWeekEnd($date)
    {
        $date = clone self::getWeekStart($date);
        $date->modify('+6 days');
        $date->setTime(23, 59, 59);
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextWeek($date)
    {
        $date = clone self::getWeekStart($date);
        $date->modify('+1 week');
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevWeek($date)
    {
        $date = clone self::getWeekStart($date);
        $date->modify('-1 week');
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevDay($date)
    {
        $date = clone self::getDayStart($date);
        $date->modify('-' . self::DEFAULT_DAYS_LENGTH . ' days');
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getDayStart($date)
    {
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextDay($date)
    {
        $date = clone self::getDayEnd($date);
        $date->modify('+1 day');
        return $date;
    }

    /**
     * @param \DateTime
     * @return \DateTime
     */
    public static function getDayEnd($date)
    {
        $date->modify('+' . self::DEFAULT_DAYS_LENGTH . ' days');
        $date->setTime(23, 59, 59);
        return $date;
    }

}
