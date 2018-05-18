<?php
defined('TYPO3_MODE') or die();

// Override news icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:bwbookingmanager-folder',
    1 => 'bw_bookingmanager',
    2 => 'apps-pagetree-folder-contains-bookingmanager',
];