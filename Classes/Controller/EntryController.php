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
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository = NULL;

    public function initializeAction() {
        $this->entryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\EntryRepository::class);

        // convert dateTime from new action
        if ($this->arguments->hasArgument('newEntry')) {
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('startDate')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,'d-m-Y-H:m:s');
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('endDate')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,'d-m-Y-H:m:s');
        }
    }

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
        $start = $this->request->hasArgument('start') ? new \DateTime($this->request->getArgument('start')) : NULL;
        $end = $this->request->hasArgument('end') ? new \DateTime($this->request->getArgument('end')) : NULL;

        $newEntry = $newEntry ? $newEntry : new \Blueways\BwBookingmanager\Domain\Model\Entry($calendar, $timeslot, $start, $end);

        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslot', $timeslot);
        $this->view->assign('newEntry', $newEntry);
    }

    /**
     * action create
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @return void
     */
    public function createAction(\Blueways\BwBookingmanager\Domain\Model\Entry $newEntry)
    {
        $this->initializeAction();
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->entryRepository->add($newEntry);
        $this->redirect('list', 'Calendar');
    }

    public function errorAction() {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->arguments->getValidationResults());
    }

}
