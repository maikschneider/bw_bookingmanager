<?php

namespace Blueways\BwBookingmanager\Controller;

use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class IcsController extends ActionController
{

    /**
     * @param Ics $ics
     * @param string $secret
     * @return string
     * @throws StopActionException
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
