<?php

use Blueways\BwBookingmanager\Helper\Tca;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot',
        'label' => 'start_date',
        'label_userFunc' => Tca::class . '->getTimeslotLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'start_date,end_date,repeat_type,holiday_setting,repeat_end,max_weight,entries,is_bookable_hooks,calendars',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_timeslot.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'hidden,--palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.timeslot.palettes.dates;datesPalette,max_weight,is_bookable_hooks,calendars,calendar', ],
    ],
    'palettes' => [
        'datesPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.timeslot.palettes.dates',
            'showitem' => 'start_date, end_date, --linebreak--, repeat_type, repeat_end, --linebreak--, repeat_days, --linebreak--, holiday_setting',
        ],
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
        'start_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.start_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int,required',
            ],
        ],
        'end_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.end_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int,required',
            ],
        ],
        'repeat_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.no', 0],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.daily', 1],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.monthly', 3],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.weeklyMultiple', 4],
                ],
            ],
        ],
        'repeat_days' => [
            'exclude' => false,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_days',
            'config' => [
                'type' => 'check',
                'cols' => 'inline',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.0', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.1', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.2', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.3', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.4', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.5', ''],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.6', ''],
                ],
            ],
            'displayCond' => 'FIELD:repeat_type:=:4',
        ],
        'holiday_setting' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting.no_effect',
                        0,
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting.not_during',
                        1,
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting.only_during',
                        2,
                    ],
                ],
            ],
        ],
        'max_weight' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.max_weight',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 1,
            ],
        ],
        'is_bookable_hooks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.is_bookable_hooks',
            'config' => [
                'type' => 'check',
                'items' => [
                ],
            ],
        ],
        'entries' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.entries',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_entry',
                'foreign_field' => 'timeslot',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                ],

            ],
        ],
        'calendar' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.calendar',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.noCalendar', 0],
                ],
            ],
        ],
        'repeat_end' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_end',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
            ],
        ],

    ],
];
