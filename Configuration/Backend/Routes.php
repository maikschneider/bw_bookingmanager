<?php

use Blueways\BwBookingmanager\Controller\Backend;

return [
    'bookingmanager_calendar' => [
        'path' => '/bookingmanager/calendar',
        'target' => Backend\CalendarModuleController::class,
    ],
    'bookingmanager_entry_list' => [
        'path' => '/bookingmanager/entry/list',
        'target' => Backend\EntryListModuleController::class,
    ],
];
