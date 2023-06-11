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
    'palettes' => [
        'direct_booking_palette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.direct_booking',
            'showitem' => 'direct_booking, color, --linebreak--, default_start_time, default_end_time, --linebreak--, min_length, min_offset',
        ],
        'general' => [
            'showitem' => 'hidden, --linebreak--, name, record_type, --linebreak--, timeslots, --linebreak--, notifications',
        ],
        'exceptions' => [
            'showitem' => 'blockslots, --linebreak--, holidays',
        ],
    ],
    'types' => [
        'Blueways\BwBookingmanager\Domain\Model\Calendar' => [
            'showitem' => '--div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:general,--palette--;;general,--div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:exceptions,--palette--;;exceptions,--div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:advanced,--palette--;;direct_booking_palette',
        ],
    ],
    'columns' => [
        'record_type' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.record_type.1',
                        'Blueways\BwBookingmanager\Domain\Model\Calendar',
                    ],
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
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
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
                'allowed' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_field' => 'calendar',
                'size' => 10,
                'maxitems' => 9999,
            ],
            'displayCond' => 'FIELD:direct_booking:REQ:false',
        ],
        'blockslots' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.blockslots',
            'config' => [
                'type' => 'group',
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
                'allowed' => 'tx_bwbookingmanager_domain_model_entry',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_entry',
                'foreign_field' => 'calendar',
                'size' => 10,
                'maxitems' => 9999,
            ],
            'displayCond' => 'FIELD:direct_booking:REQ:true',
        ],
        'direct_booking' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.allow_direct_booking',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],
        'default_start_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.default_start_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'time',
                'default' => 0,
            ],
        ],
        'default_end_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.default_end_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'time',
                'default' => 86360,
            ],
        ],
        'min_length' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.min_length',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,int',
                'default' => 1,
                'size' => 10,
            ],
        ],
        'min_offset' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_calendar.min_offset',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'range' => [
                    'lower' => 0,
                    'upper' => 1440,
                ],
                'default' => 0,
                'slider' => [
                    'step' => 1,
                    'width' => 200,
                ],
            ],
        ],
        'color' => [
            'exclude' => false,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:tx_bwbookingmanager_domain_model_calendar.color',
            'config' => [
                'type' => 'input',
                'renderType' => 'colorpicker',
                'size' => 10,
            ],
        ],
    ],
];
