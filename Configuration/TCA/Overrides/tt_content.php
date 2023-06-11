<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();
/**
 * B add calendar field for textmedia
 */
// B1. Define new field
$temporaryColumns = [
    'calendar' => [
        'exclude' => true,
        'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.tt_content.calendar',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
            'items' => [
                ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.tt_content.none', 0],
            ],
            'default' => 0,
        ],
    ],
];

// B2. Register new field
ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumns
);

// B3. Add new palette to textmedia tt_content type
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.tt_content.palette;calendar_selection',
    'textmedia',
    'after:linkToTop'
);

// B4. Add calendar input to new palette
$GLOBALS['TCA']['tt_content']['palettes']['calendar_selection'] = [
    'showitem' => 'calendar',
];
