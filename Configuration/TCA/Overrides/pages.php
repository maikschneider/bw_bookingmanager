<?php
defined('TYPO3_MODE') or die();

// Override news icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:bwbookingmanager-folder',
    1 => 'bm',
    2 => 'apps-pagetree-folder-contains-bm',
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-bm'] = 'apps-pagetree-folder-contains-bm';

// Register PageTS config file
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'bw_bookingmanager',
    'Configuration/TSconfig/Page.tsconfig',
    'Bookingmanager-PageTS'
);
