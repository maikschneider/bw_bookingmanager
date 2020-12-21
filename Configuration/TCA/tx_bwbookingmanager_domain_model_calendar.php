<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'type' => 'record_type',
        'searchFields' => 'name,timeslots,blockslots,holidays,notifications',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_calendar.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, record_type, name, timeslots, direct_booking, blockslots, holidays, notifications',
    ],
    'types' => [
        'Blueways\BwBookingmanager\Domain\Model\Calendar' => ['showitem' => 'hidden, record_type, name, timeslots, direct_booking, blockslots, holidays, notifications'],
    ],
    'columns' => [
        'record_type' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.record_type.1', 'Blueways\BwBookingmanager\Domain\Model\Calendar'],
                ],
                'default' => 'Blueways\BwBookingmanager\Domain\Model\Calendar',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],

        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'timeslots' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.timeslots',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_field' => 'calendar',
                'size' => 10,
                'maxitems' => 9999,
            ],
            'displayCond' => 'FIELD:direct_booking:REQ:false'
        ],
        'blockslots' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.blockslots',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_blockslot',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_blockslot',
                'MM' => 'tx_bwbookingmanager_calendar_blockslot_mm',
                'MM_opposite_field' => 'calendars',
                'size' => 10,
                'maxitems' => 9999,
            ],
        ],
        'holidays' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.holidays',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_holiday',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_holiday',
                'MM' => 'tx_bwbookingmanager_calendar_holiday_mm',
                'MM_opposite_field' => 'calendars',
                'size' => 10,
                'maxitems' => 9999,
            ],
        ],
        'notifications' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.notifications',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_notification',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_notification',
                'MM' => 'tx_bwbookingmanager_calendar_notification_mm',
                'MM_opposite_field' => 'calendars',
                'size' => 10,
                'maxitems' => 9999,
            ],
        ],
        'entries' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.entries',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_entry',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_entry',
                'foreign_field' => 'calendar',
                'size' => 10,
                'maxitems' => 9999,
            ],
            'displayCond' => 'FIELD:direct_booking:REQ:true'
        ],
        'direct_booking' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.direct_booking',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],

    ],
];
