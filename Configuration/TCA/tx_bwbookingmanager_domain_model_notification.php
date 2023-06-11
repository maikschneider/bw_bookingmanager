<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification',
        'label' => 'name',
        'label_alt' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'name,email,email_subject,template,hook,calendars',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_notification.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, event, name, email, email_subject, template, conditions, calendars'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled', '1'],
                ],
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email',
            'description' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'placeholder' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email.placeholder',
            ],
        ],
        'event' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.event',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.event.0',
                        '0',
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.event.1',
                        '1',
                    ],
                ],
            ],
        ],
        'conditions' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.conditions',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.conditions.special1IsChecked',
                        \Blueways\BwBookingmanager\Event\NotificationCondition\Special1Condition::class,
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.conditions.special2IsChecked',
                        \Blueways\BwBookingmanager\Event\NotificationCondition\Special2Condition::class,
                    ],
                ],
            ],
        ],
        'template' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.template',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.template.default',
                        'default',
                    ],
                ],
            ],
        ],
        'email_subject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email_subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'placeholder' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email_subject.default',
            ],
        ],
        'calendars' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.calendars',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_bwbookingmanager_domain_model_calendar',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'MM' => 'tx_bwbookingmanager_calendar_notification_mm',
                'size' => 10,
                'maxitems' => 9999,
            ],
        ],

    ],
];
