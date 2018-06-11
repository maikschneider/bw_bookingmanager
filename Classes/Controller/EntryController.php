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
    protected $entryRepository = null;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    public function initializeAction()
    {
        $this->pageUid = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id');
        $this->entryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\EntryRepository::class);
        $this->calendarRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\CalendarRepository::class);

        // override settings, if used as parameter from ajax call
        if ($this->request->hasArgument('settings')) {
            $newSettings = $this->request->getArgument('settings');
            $this->settings = $newSettings;
        }

        // in newAction and createAction
        if ($this->arguments->hasArgument('newEntry')) {
            // convert dateTime from new action
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('startDate')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd-m-Y-H:i:s');
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('endDate')->setTypeConverterOption('TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd-m-Y-H:i:s');

            
            $arguments = $this->request->getArguments();
            $calendarUid = isset($arguments['calendar']) ? $arguments['calendar'] : $arguments['newEntry']['calendar']['__identity'];
            $calendar = $this->calendarRepository->findByIdentifier($calendarUid);
            $entityClass = $calendar->getEntryTypeClassname();

            // override validator and entity class
            if ($entityClass !== \Blueways\BwBookingmanager\Domain\Model\Calendar::ENTRY_TYPE_CLASSNAME) {

                $validatorResolver = $this->objectManager->get(\TYPO3\CMS\Extbase\Validation\ValidatorResolver::class);
                $validatorConjunction = $validatorResolver->getBaseValidatorConjunction($entityClass);
                $entryValidator = $validatorResolver->createValidator('\Blueways\BwBookingmanager\Domain\Validator\EntryValidator');
                $validatorConjunction->addValidator($entryValidator);
                
                $this->arguments->getArgument('newEntry')->setValidator($validatorConjunction);

                /** @var \Blueways\BwBookingmanager\Xclass\Extbase\Mvc\Controller\Argument $user */
                $newEntry = $this->arguments['newEntry'];
                $newEntry->setDataType($entityClass);
            }

        }
    }

    /**
     * action new
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @return string HTML of form
     */
    public function newAction(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar, \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot, \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry = null)
    {
        $start = $this->request->hasArgument('start') ? new \DateTime($this->request->getArgument('start')) : null;
        $end = $this->request->hasArgument('end') ? new \DateTime($this->request->getArgument('end')) : null;

        $newEntry = $newEntry ? $newEntry : \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($calendar->getEntryTypeClassname(), $calendar, $timeslot, $start, $end);

        // set template
        if ($this->settings['templateLayout'] != 'default') {
            $this->view->setTemplate($this->settings['templateLayout']);
        }

        $this->view->assign('page', $this->pageUid);
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
        $newEntry->generateToken();
        $this->entryRepository->add($newEntry);

        // persist by hand to get uid field and make redirect possible
        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

        // send mails
        $notificationManager = new \Blueways\BwBookingmanager\Helper\NotificationManager($newEntry);
        $notificationManager->notify();

        $this->redirect('show', null, null, array('entry' => $newEntry, 'token' => $newEntry->getToken()));
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @param string $token
     * @return void
     */
    public function showAction(\Blueways\BwBookingmanager\Domain\Model\Entry $entry, $token = null)
    {
        $deleteable = $entry->isValidToken($token);

        $this->view->assign('page', $this->pageUid);
        $this->view->assign('deleteable', $deleteable);
        $this->view->assign('entry', $entry);
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @return void
     */
    public function deleteAction(\Blueways\BwBookingmanager\Domain\Model\Entry $entry)
    {
        // check token und delete
        if ($this->request->hasArgument('entry') && $this->request->getArgument('entry')['token'] && $entry->isValidToken($this->request->getArgument('entry')['token'])) {
            $this->entryRepository->remove($entry);
        }

        // redirect to backPid
        if ($this->settings['backPid']) {
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder
                ->setTargetPageUid($this->settings['backPid'])
                ->build();
            $this->redirectToURI($uri, $delay = 0, $statusCode = 303);
        }

    }

    // public function errorAction() {
    //     \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->arguments->getValidationResults());
    // }

}
