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
    ],
    'wizard_sendbookingmail' => [
        'path' => '/sendmail/get-render-configuration',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\SendmailWizard::class . '::modalContentAction'
    ],
    'sendbookingmail' => [
        'path' => '/sendmail/send',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\SendmailWizard::class . '::sendMailAction'
    ],
    'emailpreview' => [
        'path' => '/sendmail/get-email-preview',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\SendmailWizard::class . '::emailPreviewAction'
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
