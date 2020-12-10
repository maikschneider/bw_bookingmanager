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
    public function showAction(Ics $ics, string $secret = '')
    {
        if ($ics->getSecret() !== $secret) {
            $this->throwStatus(403, 'Incorrect secret');
        }

        $icsUtil = $this->objectManager->get(IcsUtility::class);

        $feed = "BEGIN:VCALENDAR
        VERSION:2.0
        METHOD:PUBLISH
        PRODID:-//blueways//BwBookingManager Events//EN\n";

        $feed .= $icsUtil->getIcsFile($ics);

        $feed .= "END:VCALENDAR";
        $feed = str_replace('  ', '', $feed);

        return $feed;
    }
}
