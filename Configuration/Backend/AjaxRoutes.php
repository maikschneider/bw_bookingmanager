<?php
return [
    'wizard_timeslots' => [
        'path' => '/timeslotdates/get-render-configuration',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\TimeslotWizard::class . '::getConfiguration'
    ],
    'dashboard_charts' => [
        'path' => '/dashboard/charts',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\Chart1Controller::class . '::getConfiguration'
    ]
];
