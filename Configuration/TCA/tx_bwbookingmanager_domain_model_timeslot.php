<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot',
        'label' => 'start_date',
        'label_userFunc' => \Blueways\BwBookingmanager\Helper\Tca::class . '->getTimeslotLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'start_date,end_date,repeat_type,holiday_setting,repeat_end,max_weight,entries,is_bookable_hooks,calendars',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_timeslot.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, start_date, end_date, repeat_type, holiday_setting, repeat_end, max_weight, entries, is_bookable_hooks, calendars',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, 
            --palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.timeslot.palettes.dates;datesPalette, max_weight, is_bookable_hooks, calendars'],
    ],
    'palettes' => [
        'datesPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.timeslot.palettes.dates',
            'showitem' => 'start_date, end_date, --linebreak--, repeat_type, repeat_end, --linebreak--, repeat_days, --linebreak--, holiday_setting'
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_table_where' => 'AND tx_bwbookingmanager_domain_model_timeslot.pid=###CURRENT_PID### AND tx_bwbookingmanager_domain_model_timeslot.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],

        'start_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.start_date',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime,required',
            ],
        ],
        'end_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.end_date',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime,required',
            ],
        ],
        'repeat_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.no', 0],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.daily', 1],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.weekly', 2],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.monthly', 3],
                    ['LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_type.weeklyMultiple', 4],
                ],
            ]
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
                ]
            ]
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
                        0
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting.not_during',
                        1
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.holiday_setting.only_during',
                        2
                    ],
                ],
            ]
        ],
        'max_weight' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.max_weight',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 1
            ]
        ],
        'is_bookable_hooks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.is_bookable_hooks',
            'config' => [
                'type' => 'check',
                'items' => [
                ],
            ]
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
                    'showAllLocalizationLink' => 1
                ],


            ],
        ],
        'calendars' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.calendars',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_calendar',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'MM' => 'tx_bwbookingmanager_calendar_timeslot_mm',
                'size' => 10,
                'minitems' => 1,
                'maxitems' => 9999,
            ],
        ],
        'repeat_end' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.repeat_end',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
                'default' => '0'
            ],
        ],

    ],
];
