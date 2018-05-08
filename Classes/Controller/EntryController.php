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
 * EntryController
 */
class EntryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * action new
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @return string HTML of form
     * @dontvalidate $newEntry
     */
    public function newAction(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar, \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot, \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry = NULL)
    {
        $newEntry = $newEntry ? $newEntry : new \Blueways\BwBookingmanager\Domain\Model\Entry();
        $newEntry->setStartDate($timeslot->getStartDate());
        $newEntry->setEndDate($timeslot->getEndDate());

        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslot', $timeslot);
        $this->view->assign('newEntry', $newEntry);
    }

    /**
     * action create
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return void
     */
    public function createAction(\Blueways\BwBookingmanager\Domain\Model\Entry $newEntry, \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar, \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot)
    {
        $newEntry->setCalendar($calendar);
        $newEntry->setTimeslot($timeslot);
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->entryRepository->add($newEntry);
        $this->redirect('list');
    }

    public function errorAction() {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->arguments->getValidationResults());
    }

}
