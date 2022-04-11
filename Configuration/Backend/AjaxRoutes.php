<?php

use Blueways\BwBookingmanager\Controller\Ajax\ChartsController;
use Blueways\BwBookingmanager\Controller\Ajax\ApiController;
return [
    'dashboard_chart1' => [
        'path' => '/dashboard/chart1',
        'target' => ChartsController::class . '::getChart1'
    ],
    'dashboard_chart2' => [
        'path' => '/dashboard/chart2',
        'target' => ChartsController::class . '::getChart2'
    ],
    'api_calendar_show' => [
        'path' => '/api/calendar/show',
        'target' => ApiController::class . '::calendarShowAction'
    ],
    'api_user_setting' => [
        'path' => '/api/user/setting',
        'target' => ApiController::class . '::userSettingAction'
    ]
];
