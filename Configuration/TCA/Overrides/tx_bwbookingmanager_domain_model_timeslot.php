<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

// Add HOOKs to TCA selection by using HOOK_ID and HOOK_LABEL from class
foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/timeslot']['isBookable'] ?? [] as $className) {
    $_procObj = GeneralUtility::makeInstance($className);
    $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['is_bookable_hooks']['config']['items'][] = [$_procObj::HOOK_LABEL, ''];
}

$validationHookItems = $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['is_bookable_hooks']['config']['items'];
if (!count($validationHookItems)) {
    $validationHookItems = $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['is_bookable_hooks']['config']['type'] = 'passthrough';
}
