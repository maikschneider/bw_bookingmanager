<?php

use Blueways\BwBookingmanager\Controller\BackendController;

return [
    'bookingmanager_calendar' => [
        'path' => '/bookingmanager/calendar',
        'target' => BackendController::class . '::calendarAction',
    ],
    'bookingmanager_entry_list' => [
        'path' => '/bookingmanager/entry/list',
        'target' => BackendController::class . '::entryListAction',
    ],
];
