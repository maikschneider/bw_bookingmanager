<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;

class IcsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @return string
     */
    public function showAction(Ics $ics)
    {
        $icsUtil = $this->objectManager->get(IcsUtility::class);

        $feed = "BEGIN:VCALENDAR
        VERSION:2.0
        METHOD:PUBLISH
        PRODID:-//Maik Schneider//BwBookingManager Events//EN\n";

        $feed .= $icsUtil->getIcsFile($ics);

        $feed .= "END:VCALENDAR";

        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

        $feed = str_replace('  ', '', $feed);

        return $feed;
    }
}
