<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Ics;

class IcsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @return string
     */
    public function showAction(Ics $ics)
    {
        return 'hello';
    }
}
