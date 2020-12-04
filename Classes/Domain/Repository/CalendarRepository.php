<?php
namespace Blueways\BwBookingmanager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Calendars
 */
class CalendarRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findAllIgnorePid()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    /**
     * @param int $pid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllByPid(int $pid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->equals('pid', $pid)
        );
        return $query->execute();
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     * @return array
     */
    public static function getUidsFromObjectStorage(ObjectStorage $calendars): array
    {
        $calendarUids = [];

        if (!$calendars->count()) {
            return $calendarUids;
        }

        foreach ($calendars as $calendar) {
            $calendarUids[] = $calendar->getUid();
        }

        return $calendarUids;
    }
}
