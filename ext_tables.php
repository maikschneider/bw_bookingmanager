<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Blueways.BwBookingmanager',
            'Bookingmanager',
            'Booking Manager'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('bw_bookingmanager', 'Configuration/TypoScript', 'Booking Manager');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_calendar', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_calendar.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_calendar');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_timeslot', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_timeslot.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_timeslot');

    }
);
