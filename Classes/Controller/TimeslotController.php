<?php
namespace Blueways\BwBookingmanager\Controller;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * TimeslotController
 */
class TimeslotController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $timeslots = $this->timeslotRepository->findAll();
        $this->view->assign('timeslots', $timeslots);
    }

    /**
     * action show
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return void
     */
    public function showAction(\Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot)
    {
        $this->view->assign('timeslot', $timeslot);
    }
}
