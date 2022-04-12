<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Calendars
 */
class CalendarRepository extends Repository
{
    public function findAllIgnorePid()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    /**
     * @param int $pid
     * @return array|QueryResultInterface
     */
    public function findAllByPid(int $pid)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages');
        $queryBuilder = $connection->createQueryBuilder();
        $query = $queryBuilder
            ->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('pages.uid', $pid),
                    $queryBuilder->expr()->eq('pages.pid', $pid)
                )
            );
        $rows = $query->execute()->fetchAll();

        $calendarUids = [];
        foreach ($rows ?? [] as $row) {
            $calendarUids[] = $row['uid'];
        }

        if (empty($calendarUids)) {
            return [];
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->in('pid', $calendarUids),
        );
        return $query->execute();
    }

    /**
     * @param ObjectStorage<Calendar> $calendars
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
