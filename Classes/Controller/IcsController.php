<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Utility\IcsUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class IcsController extends ActionController
{
    protected IcsUtility $icsUtility;

    public function __construct(IcsUtility $icsUtility)
    {
        $this->icsUtility = $icsUtility;
    }

    public function showAction(Ics $ics, string $secret = ''): ResponseInterface
    {
        if ($ics->getSecret() !== $secret) {
            $this->throwStatus(403, 'Incorrect secret');
        }

        return $this->htmlResponse($this->icsUtility->getFromIcs($ics));
    }
}
