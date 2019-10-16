<?php
namespace Blueways\BwBookingmanager\Domain\Repository;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
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
}
