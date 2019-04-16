<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCEmainHook
{

    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');

        // new / update entry
        if (is_array($pObj->datamap['tx_bwbookingmanager_domain_model_entry'])) {
            foreach ($pObj->datamap['tx_bwbookingmanager_domain_model_entry'] as $entry) {
                if ($entry['calendar']) {
                    $cache->flushByTag('calendar' . $entry['calendar']);
                }
            }
        }

        // new / update timeslot
        if (is_array($pObj->datamap['tx_bwbookingmanager_domain_model_timeslot'])) {
            foreach ($pObj->datamap['tx_bwbookingmanager_domain_model_timeslot'] as $timeslot) {
                if ($timeslot['calendars'] && strlen($timeslot['calendars'])) {
                    // edit form of tx_bwbookingmanager_domain_model_calendar_1,tx_bwbookingmanager_domain_model_calendar_2
                    $calendars = explode(',', $timeslot['calendars']);
                    foreach ($calendars as $calendar) {
                        $calendarId = substr($calendar, 42);
                        $cache->flushByTag('calendar' . $calendarId);
                    }
                }
            }
        }

        // delete Entry
        if (is_array($pObj->cmdmap['tx_bwbookingmanager_domain_model_entry'])) {
            foreach ($pObj->cmdmap['tx_bwbookingmanager_domain_model_entry'] as $id => $conf) {
                if ($conf['delete'] === '1') {
                    $cache->flushByTag('entry' . $id);
                }
            }
        }

        // delete Timeslot
        if (is_array($pObj->cmdmap['tx_bwbookingmanager_domain_model_timeslot'])) {
            foreach ($pObj->cmdmap['tx_bwbookingmanager_domain_model_timeslot'] as $id => $conf) {
                if ($conf['delete'] === '1') {
                    $cache->flushByTag('timeslot' . $id);
                }
            }
        }
    }

}
