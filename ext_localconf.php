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
                'Calendar' => 'show',
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
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] = 'Blueways\BwBookingmanager\Hooks\EntryRecordListHook';
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

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Extbase\\Mvc\\Controller\\Argument'] = array(
            'className' => 'Blueways\\BwBookingmanager\\Xclass\\Extbase\\Mvc\\Controller\\Argument',
        );

        // register custom TCA node field
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1533721566] = [
            'nodeName' => 'selectTimeslotDates',
            'priority' => '70',
            'class' => \Blueways\BwBookingmanager\Form\Element\SelectTimeslotDatesElement::class,
        ];
        // register custom TCA node field
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1533721567] = [
            'nodeName' => 'sendMailButton',
            'priority' => '70',
            'class' => \Blueways\BwBookingmanager\Form\Element\SendMailButtonElement::class,
        ];

        // register recycler task
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Blueways\BwBookingmanager\Task\RecycleEntriesTask::class] = [
            'extension' => 'bw_bookingmanager',
            'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.name',
            'description' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.description',
            'additionalFields' => \Blueways\BwBookingmanager\Task\RecycleEntriesAdditionalFieldProvider::class

        ];
    }
);
