<?php

use Blueways\BwBookingmanager\Controller\Backend\ApiController;

return [
    'api_calendar_show' => [
        'path' => '/api/calendar/show',
        'target' => ApiController::class . '::calendarShowAction',
    ],
    'api_user_setting' => [
        'path' => '/api/user/setting',
        'target' => ApiController::class . '::userSettingAction',
    ],
];
