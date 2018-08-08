<?php
return [
    'wizard_timeslots' => [
        'path' => '/timeslotdates/get-render-configuration',
        'target' => \Blueways\BwBookingmanager\Controller\Ajax\TimeslotWizard::class . '::getConfiguration'
    ],
];
