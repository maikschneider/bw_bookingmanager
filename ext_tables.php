<?php

defined('TYPO3') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'bw_bookingmanager',
            'Configuration/TypoScript',
            'Booking Manager'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_bwbookingmanager_domain_model_calendar',
            'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_calendar.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_calendar');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_bwbookingmanager_domain_model_timeslot',
            'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_timeslot.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_timeslot');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_bwbookingmanager_domain_model_entry',
            'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_entry.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_entry');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_bwbookingmanager_domain_model_blockslot',
            'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_blockslot.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_blockslot');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_bwbookingmanager_domain_model_notification',
            'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_notification.xlf'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_notification');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
            'web',
            'bookingmanager',
            'bottom',
            'bookingmanager',
            [
                'routeTarget' => \Blueways\BwBookingmanager\Controller\Backend\EntryListModuleController::class,
                'access' => 'user,group',
                'name' => 'web_bookingmanager',
                'iconIdentifier' => 'backend-module',
                'labels' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_m1.xlf',
                'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
                'inheritNavigationComponentFromMainModule' => false,
            ]
        );

        // register create Entries Function Wizard
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::insertModuleFunction(
            'web_func',
            \Blueways\BwBookingmanager\Controller\CreateTimeslotsWizardModuleFunctionController::class,
            null,
            'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:wiz_crMany'
        );

        // register backend stylesheet
        $GLOBALS['TBE_STYLES']['skins']['bw_bookingmanager'] = [];
        $GLOBALS['TBE_STYLES']['skins']['bw_bookingmanager']['name'] = 'Booking Manager Styles';
        $GLOBALS['TBE_STYLES']['skins']['bw_bookingmanager']['stylesheetDirectories'] = [
            'visual' => 'EXT:bw_bookingmanager/Resources/Public/Css/backend',
        ];
    }
);
