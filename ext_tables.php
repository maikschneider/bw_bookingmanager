<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('bw_bookingmanager', 'Configuration/TypoScript', 'Booking Manager');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_calendar', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_calendar.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_calendar');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_timeslot', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_timeslot.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_timeslot');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_entry', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_entry.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_entry');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_blockslot', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_blockslot.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_blockslot');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bwbookingmanager_domain_model_blockslot', 'EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_notification.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bwbookingmanager_domain_model_notification');

        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Blueways.BwBookingmanager',
                'web',
                'tx_bookingmanager_m1',
                '',
                array(
                    'Administration' => 'timeslot, index',
                ),
                array(
                    'access' => 'user,group',
                    'icon' => 'EXT:bw_bookingmanager/Resources/Public/Icons/module_administration.svg',
                    'labels' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_m1.xlf',
                )
            );

            // icons
            $icons = [
                'apps-pagetree-folder-contains-bm' => 'ext-bwbookingmanager-folder-tree.svg',
            ];
            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
            foreach ($icons as $identifier => $path) {
                $iconRegistry->registerIcon(
                    $identifier,
                    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                    ['source' => 'EXT:bw_bookingmanager/Resources/Public/Icons/' . $path]
                );
            }

        }

    }
);
