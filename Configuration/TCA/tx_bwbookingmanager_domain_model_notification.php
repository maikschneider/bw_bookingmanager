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
        '1' => ['showitem' => 'hidden, event, name, email, email_subject, template, hook, calendars'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
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
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
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
                        '0'
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.event.1',
                        '1'
                    ],
                ],
            ],
        ],
        'hook' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.hook',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.hook.none', '0'],
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
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.template.default', 'default']
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
                'default' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.email_subject.default'
            ],
        ],
        'calendars' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.calendars',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_calendar',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'MM' => 'tx_bwbookingmanager_calendar_notification_mm',
                'size' => 10,
                'maxitems' => 9999,
            ],
        ],

    ],
];
