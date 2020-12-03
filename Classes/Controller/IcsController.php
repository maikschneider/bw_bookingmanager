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
        $feed = $icsUtil->getIcsFile($ics);

        return $feed;
    }
}
