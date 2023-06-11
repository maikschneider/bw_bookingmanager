<?php

namespace Blueways\BwBookingmanager\Task;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class RecycleEntriesTask extends AbstractTask
{
    /**
     * @var int Number of days before deleting old entries
     */
    public $numberOfDays = 0;

    /**
     * @return bool
     */
    public function execute()
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionForTable('tx_bwbookingmanager_domain_model_entry');

        try {
            $queryBuilder = $connection->createQueryBuilder();

            $deleteDate = new \DateTime();
            $deleteDate->modify('-' . $this->numberOfDays . 'days');

            $result = $queryBuilder
                ->delete('tx_bwbookingmanager_domain_model_entry')
                ->where(
                    $queryBuilder->expr()->lte('end_date', $deleteDate->getTimestamp())
                )
                ->execute();
        } catch (DBALException $e) {
            throw new \RuntimeException(
                self::class . ' failed: ' .
                $e->getPrevious()->getMessage(),
                1441390263
            );
        }

        return true;
    }

    public function getAdditionalInformation()
    {
        return sprintf(
            $GLOBALS['LANG']->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.additionalInformation'),
            $this->numberOfDays
        );
    }
}
