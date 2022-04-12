<?php

namespace Blueways\BwBookingmanager\Utility;

class DateUtility
{
    /**
     * @param \DateTime $startDate
     * @param int $viewRange is defined in settings.dateRange
     * @return \DateTime
     */
    public static function calculateEndDateForView($startDate, $viewRange = 0)
    {
        // view one week
        if ($viewRange === 1) {
            $weekStart = clone $startDate;
            $weekStart->modify('tomorrow');
            $weekStart->modify('last monday');
            $weekStart->setTime(0, 0, 0);

            $weekEnd = clone $weekStart;
            $weekEnd->modify('+7 days');
            $weekEnd->setTime(23, 59, 59);

            return $weekEnd;
        }

        // view range of days
        if ($viewRange === 2) {
            // @TODO make day $dayCount flexible
            $dayCount = 150;
            $dayStart = clone $startDate;
            $dayStart->setTime(0, 0, 0);

            $dayEnd = clone $startDate;
            $dayEnd->modify('+' . $dayCount . ' days');
            $dayEnd->setTime(23, 59, 59);

            return $dayEnd;
        }

        // default view one month
        $monthStart = clone $startDate;
        $monthStart->modify('first day of this month');
        $monthStart->modify('last monday');
        $monthStart->setTime(0, 0, 0);

        $monthEnd = clone $startDate;
        $monthEnd->modify('last day of this month');
        $monthEnd->modify('next sunday');
        $monthEnd->setTime(23, 59, 59);

        return $monthEnd;
    }
}
