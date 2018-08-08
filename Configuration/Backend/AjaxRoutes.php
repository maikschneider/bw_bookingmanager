<?php
return [
    'wizard_timeslots' => [
        'path' => '/timeslotdates/get-render-configuration',
        'target' => \Blueways\BookingManager\Controller\Ajax\TimeslotWizard::class . '::getConfiguration'
    ],
];
