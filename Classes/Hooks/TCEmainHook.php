<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TCEmainHook
{
    public function processDatamap_beforeStart(DataHandler &$pObj)
    {
        if (is_array($pObj->datamap['tx_bwbookingmanager_domain_model_entry'])) {
            foreach ($pObj->datamap['tx_bwbookingmanager_domain_model_entry'] as &$entry) {
                if ($entry['calendar']) {
                    $calendar = BackendUtility::getRecord(
                        'tx_bwbookingmanager_domain_model_calendar',
                        (int)$entry['calendar']
                    );
                    if ($calendar['direct_booking'] && ($calendar['default_start_time'] || $calendar['default_end_time'])) {
                        $entryStartDate = new \DateTime($entry['start_date']);
                        $entryEndDate = new \DateTime($entry['end_date']);

                        $calendarStartTime = $calendar['default_start_time'];
                        $calendarStartTime = new \DateTime("@$calendarStartTime");
                        $calendarEndTime = $calendar['default_end_time'];
                        $calendarEndTime = new \DateTime("@$calendarEndTime");

                        $entryStartDate->setTime($calendarStartTime->format('H'), $calendarStartTime->format('i'));
                        $entryEndDate->setTime($calendarEndTime->format('H'), $calendarEndTime->format('i'));

                        $entry['start_date'] = $entryStartDate->format('c');
                        $entry['end_date'] = $entryEndDate->format('c');
                    }
                }
            }
        }
    }

    public function processDatamap_afterAllOperations(DataHandler &$pObj)
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('bwbookingmanager_calendar');

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
