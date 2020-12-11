<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class IcsController extends ActionController
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @param string $secret
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function showAction(Ics $ics, string $secret = ''): string
    {
        if ($ics->getSecret() !== $secret) {
            $this->throwStatus(403, 'Incorrect secret');
        }

        $icsUtil = $this->objectManager->get(IcsUtility::class);
        return $icsUtil->getFromIcs($ics);
    }
}
