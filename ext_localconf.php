<?php

defined('TYPO3') || die('Access denied.');

call_user_func(
    static function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'BwBookingmanager',
            'Api',
            [
                \Blueways\BwBookingmanager\Controller\ApiController::class => 'entryCreate, login, logout',
                \Blueways\BwBookingmanager\Controller\ApiV2Controller::class => 'calendarShow',
            ],
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'BwBookingmanager',
            'Ics',
            [
                \Blueways\BwBookingmanager\Controller\IcsController::class => 'show',
            ],
            []
        );

        // register TSConfig for User und Page
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import "EXT:bw_bookingmanager/Configuration/TSconfig/Page.tsconfig"');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('@import "EXT:bw_bookingmanager/Configuration/TSconfig/User.tsconfig"');

        // register context menu
        $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1610288958] = \Blueways\BwBookingmanager\ContextMenu\CalendarItemProvider::class;

        // notification hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'][] = 'Blueways\BwBookingmanager\Hooks\NotificationSpecial1IsCheckedHook';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'][] = 'Blueways\BwBookingmanager\Hooks\NotificationSpecial2IsCheckedHook';
        // entry validation hook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/timeslot']['isBookable'][] = 'Blueways\BwBookingmanager\Hooks\TimeslotIsBookableHook';

        // Register hook for EntryList query modification
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class]['modifyQuery'][] = \Blueways\BwBookingmanager\Hooks\TableListHook::class;

        // xclass to make overrides of controller arguments possible in 3rd party extensions
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Extbase\Mvc\Controller\Argument::class] = [
            'className' => \Blueways\BwBookingmanager\Xclass\Extbase\Mvc\Controller\Argument::class,
        ];

        // register custom TCA node field
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1533721566] = [
            'nodeName' => 'selectTimeslotDates',
            'priority' => '70',
            'class' => \Blueways\BwBookingmanager\Form\Element\SelectTimeslot::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1607520481] = [
            'nodeName' => 'icsSecret',
            'priority' => '70',
            'class' => \Blueways\BwBookingmanager\Form\Element\IcsSecret::class,
        ];

        // register recycler task
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Blueways\BwBookingmanager\Task\RecycleEntriesTask::class] = [
            'extension' => 'bw_bookingmanager',
            'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.name',
            'description' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:recyclertask.description',
            'additionalFields' => \Blueways\BwBookingmanager\Task\RecycleEntriesAdditionalFieldProvider::class,
        ];

        // register backend js
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess'][] = \Blueways\BwBookingmanager\Hooks\PreHeaderRenderHook::class . '->addFullCalendarJs';

        // Register Mail Templates
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][107] = 'EXT:bw_bookingmanager/Resources/Private/Templates/Email';
    }
);
