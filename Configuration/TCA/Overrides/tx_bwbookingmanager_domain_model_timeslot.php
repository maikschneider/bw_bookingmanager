<?php
defined('TYPO3_MODE') or die();

// Add HOOKs to TCA selection by using HOOK_ID and HOOK_LABEL from class
foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/entry']['validation'] ?? [] as $className) {
    $_procObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
    $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['validation_hooks']['config']['items'][] = [$_procObj::HOOK_LABEL, ''];
}


$validationHookItems = $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['validation_hooks']['config']['items'];
if(!count($validationHookItems)){
    $validationHookItems = $GLOBALS['TCA']['tx_bwbookingmanager_domain_model_timeslot']['columns']['validation_hooks']['config']['type'] = 'passthrough';
}