<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Booking Manager',
    'description' => 'A generic bookingmanager',
    'category' => 'plugin',
    'author' => 'Maik Schneider',
    'author_email' => 'm.schneider@blueways.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '8.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.20-9.9.99',
            'bw_email' => '2.1.4-2.9.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
