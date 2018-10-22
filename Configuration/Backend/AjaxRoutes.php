<?php
return [
    'wizard_timeslots' => [
        'path' => '/timeslotdates/get-render-configuration',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\TimeslotWizard::class . '::getConfiguration'
    ],
    'dashboard_chart1' => [
        'path' => '/dashboard/chart1',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ChartsController::class . '::getChart1'
    ],
    'dashboard_chart2' => [
        'path' => '/dashboard/chart2',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ChartsController::class . '::getChart2'
    ]
];