<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCEmainHook
{

    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
        if (is_array($pObj->datamap['tx_bwbookingmanager_domain_model_entry'])) {
            foreach ($pObj->datamap['tx_bwbookingmanager_domain_model_entry'] as $entry) {
                if ($entry['calendar']) {
                    $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');
                    $cache->flushByTag('calendar' . $entry['calendar']);
                }
            }
        }
    }

    public function processCmdmap_deleteAction(
        $table,
        $id,
        $recordToDelete,
        $recordWasDeleted = null,
        \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj
    ) {
        $e = $pObj;
    }

}
