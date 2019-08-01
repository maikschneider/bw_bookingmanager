<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * PHP version 7.2
 *
 * @package BwBookingManager
 * @author  Maik Schneider <m.schneider@blueways.de>
 * @license MIT https://opensource.org/licenses/MIT
 * @version GIT: <git_id />
 * @link    http://www.blueways.de
 */
class EntryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \Blueways\BwBookingmanager\Service\AccessControlService
     * @inject
     */
    protected $accessControlService;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository;

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot|null $timeslot
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry|null $newEntry
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \Exception
     */
    public function newAction(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot = null,
        \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry = null
    ) {
        if (!$timeslot && !$calendar->isDirectBooking()) {
            $this->throwStatus(403, 'Direct booking is not allowed');
        }

        $start = new \DateTime();
        $end = null;
        $feUser = false;

        if ($this->request->hasArgument('start')) {
            $start->setTimestamp($this->request->getArgument('start'));
        }

        if ($this->request->hasArgument('end')) {
            $end = new \DateTime();
            $end->setTimestamp($this->request->getArgument('end'));
        }

        $newEntry = $newEntry ?: \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            $calendar::ENTRY_TYPE_CLASSNAME,
            $calendar,
            $timeslot,
            $start,
            $end
        );

        if ($this->accessControlService->hasLoggedInFrontendUser()) {
            $feUser = $this->frontendUserRepository->findByIdentifier($this->accessControlService->getFrontendUserUid());
            $newEntry->mergeWithFeUser($feUser);
        }

        $dateConf = new DateConf((int)$this->settings['dateRange'], $start);
        $calendarManager = $this->objectManager->get(CalendarManagerUtility::class, $calendar);
        $configuration = $calendarManager->getConfiguration($dateConf);

        $this->view->setTemplate($this->settings['template']['entry']['new']);
        $this->getControllerContext()->getRequest()->setControllerActionName('new');

        $this->view->assignMultiple([
            'calendar' => $calendar,
            'timeslot' => $timeslot,
            'newEntry' => $newEntry,
            'feUser' => $feUser,
            'configuration' => $configuration
        ]);
    }

    /**
     * action create
     *
     * @param  \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @TYPO3\CMS\Extbase\Annotation\Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function createAction(\Blueways\BwBookingmanager\Domain\Model\Entry $newEntry)
    {
        $newEntry->generateToken();
        // override PID (just in case the storage PID differs from current calendar)
        $newEntry->setPid($newEntry->getCalendar()->getPid());
        $this->entryRepository->add($newEntry);

        // persist by hand to get uid field and make redirect possible
        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

        // delete calendar cache
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');
        $cache->flushByTag('calendar' . $newEntry->getCalendar()->getUid());

        // send mails
        $notificationManager = new \Blueways\BwBookingmanager\Helper\NotificationManager($newEntry);
        $notificationManager->setSettings($this->settings);
        $notificationManager->notify();

        $this->addFlashMessage(
            $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.booking.success.message'),
            $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.booking.success.title'),
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );

        $this->redirect('show', null, null, array('entry' => $newEntry, 'token' => $newEntry->getToken()));
    }

    /**
     * @return mixed|\TYPO3\CMS\Lang\LanguageService
     */
    private function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    public function initializeAction()
    {
        $this->entryRepository = $this->objectManager->get(EntryRepository::class);
        $this->calendarRepository = $this->objectManager->get(CalendarRepository::class);
        $this->timeslotRepository = $this->objectManager->get(TimeslotRepository::class);

        // in newAction and createAction
        if ($this->arguments->hasArgument('newEntry')) {
            // convert dateTime from new action

            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('startDate')->setTypeConverterOption(
                'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('endDate')->setTypeConverterOption(
                'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );

            // override entity class in case of inheritance
            $arguments = $this->request->getArguments();
            $calendarUid = isset($arguments['calendar']) ? $arguments['calendar'] : $arguments['newEntry']['calendar']['__identity'];
            /** @var \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar */
            $calendar = $this->calendarRepository->findByIdentifier($calendarUid);
            $entityClass = $calendar::ENTRY_TYPE_CLASSNAME;

            if ($entityClass !== \Blueways\BwBookingmanager\Domain\Model\Calendar::ENTRY_TYPE_CLASSNAME) {
                $newEntry = $this->arguments['newEntry'];
                $newEntry->setDataType($calendar::ENTRY_TYPE_CLASSNAME);
            }
        }
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @param string $token
     * @return void
     */
    public function showAction(\Blueways\BwBookingmanager\Domain\Model\Entry $entry, $token = null)
    {
        $deleteable = $entry->isValidToken($token);

        $this->view->assign('deleteable', $deleteable);
        $this->view->assign('entry', $entry);
    }

    // public function errorAction() {
    //     \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->arguments->getValidationResults());
    // }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function deleteAction(\Blueways\BwBookingmanager\Domain\Model\Entry $entry)
    {
        // check token und delete
        if ($this->request->hasArgument('entry') && $this->request->getArgument('entry')['token'] && $entry->isValidToken($this->request->getArgument('entry')['token'])) {

            // delete calendar cache
            $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');
            $cache->flushByTag('calendar' . $entry->getCalendar()->getUid());

            // delete entry
            $this->entryRepository->remove($entry);

            $this->addFlashMessage(
                $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.success.message'),
                $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.success.title'),
                \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
            );
        } else {
            $this->addFlashMessage(
                $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.error.message'),
                $this->getLanguageService()->sL('EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.error.title'),
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
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

    /**
     * @return string|void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function errorAction()
    {
        if ($this->request->getControllerActionName() === "create") {

            /** @var \TYPO3\CMS\Extbase\Mvc\Request $referringRequest */
            $referringRequest = $this->request->getReferringRequest();

            if ($referringRequest !== null) {
                $originalRequest = clone $this->request;
                $this->request->setOriginalRequest($originalRequest);
                $this->request->setOriginalRequestMappingResults($this->arguments->getValidationResults());
                $this->forward(
                    $referringRequest->getControllerActionName(),
                    $referringRequest->getControllerName(),
                    $referringRequest->getControllerExtensionName(),
                    $referringRequest->getArguments()
                );
            }
        }
    }
}
