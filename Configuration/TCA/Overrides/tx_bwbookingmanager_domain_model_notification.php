<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
defined('TYPO3_MODE') or die();

// Add HOOKs to TCA selection by using HOOK_ID and HOOK_LABEL from class
foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'] ?? [] as $className) {
    $_procObj = GeneralUtility::makeInstance($className);
    $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_notification']['columns']['hook']['config']['items'][] = [$_procObj::HOOK_LABEL, $_procObj::HOOK_ID];
}
