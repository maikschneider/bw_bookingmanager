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
     * @var \DateTime
     */
    public $startOrig;

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
        $this->startOrig = $date;

        $this->configureSelf($date);
    }

    private function configureSelf($date)
    {
        if ($this->viewType === 0) {
            $this->start = self::getMonthStart($date);
            $this->end = self::getMonthEnd($date);
            $this->next = self::getNextMonth($date);
            $this->prev = self::getPrevMonth($date);
        }

        if ($this->viewType === 1) {
            $this->start = self::getWeekStart($date);
            $this->end = self::getWeekEnd($date);
            $this->next = self::getNextWeek($date);
            $this->prev = self::getPrevWeek($date);
        }

        if ($this->viewType === 2) {
            $this->start = self::getDayStart($date);
            $this->end = self::getDayEnd($date);
            $this->next = self::getNextDay($date);
            $this->prev = self::getPrevDay($date);
        }
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getMonthStart($date)
    {
        $newDate = clone $date;
        $newDate->modify('first day of this month');
        $newDate->modify('last monday');
        $newDate->setTime(0, 0, 0);
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getMonthEnd($date)
    {
        $newDate = clone $date;
        $newDate->modify('last day of this month');
        $newDate->modify('next sunday');
        $newDate->setTime(23, 59, 59);
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextMonth($date)
    {
        $newDate = clone $date;
        $newDate->modify('first day of next month');
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevMonth($date)
    {
        $newDate = clone $date;
        $newDate->modify('first day of last month');
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getWeekStart($date)
    {
        $newDate = clone $date;
        $newDate->modify('tomorrow');
        $newDate->modify('last monday');
        $newDate->setTime(0, 0, 0);
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getWeekEnd($date)
    {
        $newDate = clone $date;
        $newDate = self::getWeekStart($newDate);
        $newDate->modify('+6 days');
        $newDate->setTime(23, 59, 59);
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextWeek($date)
    {
        $newDate = clone $date;
        $newDate = self::getWeekStart($newDate);
        $newDate->modify('+1 week');
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevWeek($date)
    {
        $newDate = clone $date;
        $newDate = self::getWeekStart($newDate);
        $newDate->modify('-1 week');
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getPrevDay($date)
    {
        $newDate = clone $date;
        $newDate = self::getDayStart($newDate);
        $newDate->modify('-' . self::DEFAULT_DAYS_LENGTH . ' days');
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getDayStart($date)
    {
        $newDate = clone $date;
        $newDate->setTime(0, 0, 0);
        return $newDate;
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    public static function getNextDay($date)
    {
        $newDate = clone $date;
        $newDate = self::getDayEnd($newDate);
        $newDate->modify('+1 day');
        return $newDate;
    }

    /**
     * @param \DateTime
     * @return \DateTime
     */
    public static function getDayEnd($date)
    {
        $newDate = clone $date;
        $newDate->modify('+' . self::DEFAULT_DAYS_LENGTH . ' days');
        $newDate->setTime(23, 59, 59);
        return $newDate;
    }

}
