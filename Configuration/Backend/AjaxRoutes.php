<?php
return [
    'dashboard_chart1' => [
        'path' => '/dashboard/chart1',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ChartsController::class . '::getChart1'
    ],
    'dashboard_chart2' => [
        'path' => '/dashboard/chart2',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ChartsController::class . '::getChart2'
    ],
    'api_calendar_show' => [
        'path' => '/api/calendar/show',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ApiController::class . '::calendarShowAction'
    ],
    'api_user_setting' => [
        'path' => '/api/user/setting',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\ApiController::class . '::userSettingAction'
    ]
];
