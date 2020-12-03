<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:ics',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'name,calendars',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_ics.svg'
    ],
    'palettes' => [
        'general' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.general',
            'showitem' => 'name,hidden,--linebreak--,options,--linebreak--,calendars,--linebreak--,start_date, end_date'
        ],
        'entry' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.entry',
            'showitem' => 'entry_title,--linebreak--,entry_location,--linebreak--,entry_description'
        ],
        'timeslot' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.timeslot',
            'showitem' => 'timeslot_title,--linebreak--,timeslot_location,--linebreak--,timeslot_description'
        ],
        'blockslot' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.blockslot',
            'showitem' => 'blockslot_title, --linebreak--,blockslot_location, --linebreak--,blockslot_description'
        ],
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, name, calendars',
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.general,
                    --palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.general;general,
                --div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:templates,
                    --palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.entry;entry,
                    --palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.timeslot;timeslot,
                    --palette--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:palette.blockslot;blockslot
            '
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'calendars' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:calendars',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bwbookingmanager_domain_model_calendar',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'MM' => 'tx_bwbookingmanager_calendar_ics_mm',
                'size' => 5,
                'maxitems' => 9999,
            ],
        ],
        'options' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options',
            'config' => [
                'type' => 'check',
                'cols' => 1,
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options.0',
                        ''
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options.1',
                        ''
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options.2',
                        ''
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options.3',
                        ''
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:options.4',
                        ''
                    ],
                ],
                'default' => 31
            ]
        ],
        'entry_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => 'FIELD:prename FIELD:name'
            ],
        ],
        'entry_location' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.location',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => 'FIELD:calendar.name'
            ],
        ],
        'entry_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.description',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'max' => 254,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'timeslot_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => 'FUNC:bookedWeight/FIELD:max_weight'
            ],
        ],
        'timeslot_location' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.location',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'timeslot_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.description',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'max' => 254,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'blockslot_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => 'FIELD:reason'
            ],
        ],
        'blockslot_location' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.location',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'blockslot_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:template.description',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'max' => 254,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'start_date' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:startDate',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:startDate.0',
                        0
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:startDate.1',
                        1
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:startDate.2',
                        2
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:startDate.3',
                        3,
                    ]
                ],
                'default' => 0,
            ]
        ],
        'end_date' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:endDate',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:endDate.0',
                        0
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:endDate.1',
                        1,
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_csh_tx_bwbookingmanager_domain_model_ics.xlf:endDate.2',
                        2,
                    ]
                ],
                'default' => 0,
            ]
        ],
    ]
];
