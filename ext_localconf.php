<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Blueways.BwBookingmanager',
            'Pi1',
            [
                'Calendar' => 'list, show',
                'Timeslot' => 'list, show',
                'Entry' => 'new, create, show, delete',
            ],
            // non-cacheable actions
            [
                'Calendar' => '',
                'Timeslot' => '',
                'Entry' => 'create',
            ]
        );

        // notification hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'][] = 'Blueways\BwBookingmanager\Hooks\NotificationSpecial1IsCheckedHook';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'][] = 'Blueways\BwBookingmanager\Hooks\NotificationSpecial2IsCheckedHook';
        // entry validation hook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/timeslot']['isBookable'][] = 'Blueways\BwBookingmanager\Hooks\TimeslotIsBookableHook';
        // table list hook
        // entry validation hook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList']['buildQueryParameters'][] = 'Blueways\BwBookingmanager\Hooks\TableListHook';


        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    bookingmanager {
                        icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('bw_bookingmanager') . 'Resources/Public/Icons/Extension.svg
                        title = LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bw_bookingmanager_domain_model_bookingmanager
                        description = LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bw_bookingmanager_domain_model_bookingmanager.description
                        tt_content_defValues {
                            CType = list
                            list_type = bwbookingmanager_pi1
                        }
                    }
                }
                show = *
            }
       }
       <INCLUDE_TYPOSCRIPT: source="FILE:EXT:bw_bookingmanager/Configuration/TSconfig/TCEFORM.txt">
       <INCLUDE_TYPOSCRIPT: source="FILE:EXT:bw_bookingmanager/Configuration/TSconfig/mod.txt">'
        );

        // icons
        if (TYPO3_MODE === 'BE') {
            $icons = [
                'apps-pagetree-folder-contains-bookingmanager' => 'ext-bwbookingmanager-folder-tree.svg',
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
