<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
defined('TYPO3') or die();

/***************
 * A add Pi1 Plugin
 */
ExtensionUtility::registerPlugin(
    'BwBookingmanager',
    'Pi1',
    'Booking Manager',
    'apps-pagetree-folder-contains-bm'
);
// Add flexform for pi1
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['bwbookingmanager_pi1'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['bwbookingmanager_pi1'] = 'pi_flexform';
ExtensionManagementUtility::addPiFlexFormValue(
    'bwbookingmanager_pi1',
    'FILE:EXT:bw_bookingmanager/Configuration/FlexForms/flexform_bwbookingmanager.xml'
);

/**
 * B add calendar field for textmedia
 */
// B1. Define new field
$temporaryColumns = array(
    'calendar' => [
        'exclude' => true,
        'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.tt_content.calendar',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
            'items' => [
                ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.tt_content.none', 0]
            ],
            'default' => 0
        ]
    ]
);

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
    'showitem' => 'calendar'
];
