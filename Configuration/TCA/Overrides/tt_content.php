<?php
defined('TYPO3_MODE') or die();

/***************
 * Plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Blueways.BwBookingmanager',
    'Pi1',
    'Booking Manager',
    'apps-pagetree-folder-contains-bm'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['bwbookingmanager_pi1'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['bwbookingmanager_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'bwbookingmanager_pi1',
    'FILE:EXT:bw_bookingmanager/Configuration/FlexForms/flexform_bwbookingmanager.xml'
);
