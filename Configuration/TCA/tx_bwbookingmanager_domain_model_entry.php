<?php

use Blueways\BwBookingmanager\Helper\Tca;
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry',
        'label' => 'start_date',
        'label_userFunc' => Tca::class . '->getEntryLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'type' => 'record_type',
        'descriptionColumn' => 'notes',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'start_date,end_date,name,prename,token,street,zip,city,phone,email,newsletter,weight,timeslot,calendar,special1,special2,confirmed,fe_user,gender,notes',
        'iconfile' => 'EXT:bw_bookingmanager/Resources/Public/Icons/tx_bwbookingmanager_domain_model_entry.svg',
    ],
    'types' => [
        'Blueways\BwBookingmanager\Domain\Model\Entry' => [
            'showitem' => '--palette--;;topPalette,--palette--;;generalPalette,--palette--;;contactDataPalette,--palette--;;specialPalette,--div--;LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.tabs.internal,--palette--;;internalPalette'
        ],
    ],
    'palettes' => [
        'topPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.palettes.topPalette',
            'showitem' => 'confirmed, record_type, token, hidden,'
        ],
        'generalPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.palettes.general',
            'showitem' => 'calendar, send_mail_button, timeslot_dates_select, --linebreak--, timeslot, start_date, end_date'
        ],
        'contactDataPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.palettes.contactData',
            'showitem' => 'gender, --linebreak--, name, prename, --linebreak--, street, zip, --linebreak--, city, phone, --linebreak--, email, fe_user'
        ],
        'specialPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.palettes.special',
            'showitem' => 'weight, newsletter, special1, special2'
        ],
        'internalPalette' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:tca.entry.palettes.internal',
            'showitem' => 'notes'
        ]
    ],
    'columns' => [
        'record_type' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.record_type.1',
                        'Blueways\BwBookingmanager\Domain\Model\Entry'
                    ]
                ],
                'default' => 'Blueways\BwBookingmanager\Domain\Model\Entry',
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.confirmed.yes',
                    ],
                ],
            ],
        ],

        'token' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],

        'start_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.start_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'hidden',
                'eval' => 'date,required'
            ],
        ],
        'end_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.end_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'hidden',
                'eval' => 'date,required',
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'prename' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.prename',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required,email',
            ],
        ],
        'newsletter' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.newsletter',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'confirmed' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.confirmed',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.confirmed.yes',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'special1' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.special1',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'special2' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.special2',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'weight' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.weight',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 1
            ],
        ],
        'timeslot' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.timeslot',
            'config' => [
                'type' => 'input',
                'renderType' => 'hidden',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_timeslot',
                'foreign_table_where' => ' AND tx_bwbookingmanager_domain_model_timeslot.pid=###CURRENT_PID###',
                'default' => 0
            ],
        ],
        'calendar' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.calendar',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_bwbookingmanager_domain_model_calendar',
                'foreign_table_where' => ' AND tx_bwbookingmanager_domain_model_calendar.pid=###CURRENT_PID###',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'timeslot_dates_select' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.date',
            'config' => [
                'type' => 'input',
                'renderType' => 'selectTimeslotDates'
            ]
        ],
        'send_mail_button' => [
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.sendMail.button',
            'config' => [
                'renderType' => 'sendMailButton',
                'type' => 'passthrough'
            ],
        ],
        'crdate' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'fe_user' => [
            'exclude' => false,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.fe_user',
            'config' => [
                'type' => 'group',
                'allowed' => 'fe_users',
                'foreign_table' => 'fe_users',
                'foreign_table_field' => 'entries',
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'int',
                'default' => 0,
                'suggestOptions' => [
                    'fe_users' => [
                        'searchWholePhrase' => 1,
                        'additionalSearchFields' => 'name, short_name, first_name, last_name'
                    ]
                ],
                'fieldWizard' => [
                    'recordsOverview' => [
                        'disabled' => true,
                    ],
                ],
            ]
        ],
        'gender' => [
            'exclude' => false,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.gender',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.gender.0',
                        0
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.gender.1',
                        1
                    ],
                    [
                        'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.gender.2',
                        2
                    ]
                ],
                'default' => 0,
            ]
        ],
        'notes' => [
            'exclude' => false,
            'label' => 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_entry.notes',
            'config' => [
                'type' => 'text',
                'rows' => 10,
                'cols' => 60
            ]
        ],
    ],
];
