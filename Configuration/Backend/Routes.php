<?php

use Blueways\BwBookingmanager\Controller\BackendController;

return [
    'bookingmanager_calendar' => [
        'path' => '/bookingmanager/calendar',
        'target' => BackendController::class . '::calendarAction'
    ]
];
