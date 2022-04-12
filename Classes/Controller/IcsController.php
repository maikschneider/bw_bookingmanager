<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

class IcsController extends ActionController
{
    /**
     * @param Ics $ics
     * @param string $secret
     * @return string
     * @throws StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function showAction(Ics $ics, string $secret = ''): ResponseInterface
    {
        if ($ics->getSecret() !== $secret) {
            $this->throwStatus(403, 'Incorrect secret');
        }

        $icsUtil = $this->objectManager->get(IcsUtility::class);
        return $this->htmlResponse($icsUtil->getFromIcs($ics));
    }
}
