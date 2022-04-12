<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Timeslot;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Blueways\BwBookingmanager\Helper\NotificationManager;
use Blueways\BwBookingmanager\Service\AccessControlService;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;

/**
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * PHP version 7.2
 *
 * @author  Maik Schneider <m.schneider@blueways.de>
 * @license MIT https://opensource.org/licenses/MIT
 * @version GIT: <git_id />
 * @link    http://www.blueways.de
 */
class EntryController extends ActionController
{
    /**
     * @var AccessControlService
     */
    protected $accessControlService;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @param Calendar $calendar
     * @param Timeslot|null $timeslot
     * @param Entry|null $newEntry
     * @throws NoSuchCacheException
     * @throws InvalidActionNameException
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws InvalidQueryException
     * @throws \Exception
     */
    public function newAction(
        Calendar $calendar,
        Timeslot $timeslot = null,
        Entry $newEntry = null
    ): ResponseInterface {
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

        $newEntry = $newEntry ?: GeneralUtility::makeInstance(
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
            'configuration' => $configuration,
        ]);
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param Entry $newEntry
     * @Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @TYPO3\CMS\Extbase\Annotation\Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @throws InvalidConfigurationTypeException
     * @throws StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     * @throws NoSuchCacheException
     */
    public function createAction(Entry $newEntry)
    {
        $newEntry->generateToken();
        // override PID (just in case the storage PID differs from current calendar)
        $newEntry->setPid($newEntry->getCalendar()->getPid());
        $this->entryRepository->add($newEntry);

        // persist by hand to get uid field and make redirect possible
        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

        // delete calendar cache
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('bwbookingmanager_calendar');
        $cache->flushByTag('calendar' . $newEntry->getCalendar()->getUid());

        // send mails
        $notificationManager = new NotificationManager($newEntry);
        $notificationManager->setSettings($this->settings);
        $notificationManager->notify();

        $this->addFlashMessage(
            $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.booking.success.message'),
            $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.booking.success.title'),
            AbstractMessage::OK
        );

        $this->redirect('show', null, null, ['entry' => $newEntry, 'token' => $newEntry->getToken()]);
    }

    /**
     * @return mixed|LanguageService
     */
    private function getLanguageService()
    {
        return $GLOBALS['TSFE'];
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
                DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );
            $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->forProperty('endDate')->setTypeConverterOption(
                'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
                DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                'U'
            );

            // override entity class in case of inheritance
            $arguments = $this->request->getArguments();
            $calendarUid = isset($arguments['calendar']) ? $arguments['calendar'] : $arguments['newEntry']['calendar']['__identity'];
            /** @var Calendar $calendar */
            $calendar = $this->calendarRepository->findByIdentifier($calendarUid);
            $entityClass = $calendar::ENTRY_TYPE_CLASSNAME;

            if ($entityClass !== Calendar::ENTRY_TYPE_CLASSNAME) {
                $newEntry = $this->arguments['newEntry'];
                $newEntry->setDataType($calendar::ENTRY_TYPE_CLASSNAME);
            }

            // unset feUser if empty
            if ($arguments['newEntry']['feUser'] === '') {
                $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration()->skipProperties('feUser');
            }
        }
    }

    /**
     * @param Entry $entry
     * @param string $token
     */
    public function showAction(Entry $entry, $token = null): ResponseInterface
    {
        $deleteable = $entry->isValidToken($token);

        $this->view->assign('deleteable', $deleteable);
        $this->view->assign('entry', $entry);
        return $this->htmlResponse();
    }

    /**
     * @param Entry $entry
     * @throws NoSuchArgumentException
     * @throws StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     * @throws NoSuchCacheException
     */
    public function deleteAction(Entry $entry)
    {
        $validToken = $this->request->hasArgument('entry') && isset($this->request->getArgument('entry')['token']) && $entry->isValidToken($this->request->getArgument('entry')['token']);
        $validUser = $this->accessControlService->hasLoggedInFrontendUser() && $entry->getFeUser() && $entry->getFeUser()->getUid() === $this->accessControlService->getFrontendUserUid();

        // check token und delete
        if ($validToken || $validUser) {
            $configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
            $typoscript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            $cancelTime = $typoscript['plugin.']['tx_bwbookingmanager.']['settings.']['cancelTime'];

            $cancelDate = new \DateTime();
            $cancelDate->modify('+ ' . $cancelTime . 'minutes');

            if ($entry->getStartDate() > $cancelDate) {

                // send mails
                $notificationManager = new NotificationManager($entry);
                $notificationManager->setSettings($this->settings);
                $notificationManager->notifyDeletion();

                // delete calendar cache
                $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('bwbookingmanager_calendar');
                $cache->flushByTag('calendar' . $entry->getCalendar()->getUid());

                // delete entry
                $this->entryRepository->remove($entry);

                $this->addFlashMessage(
                    $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.success.message'),
                    $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.success.title'),
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.toolate.message'),
                    $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.toolate.title'),
                    AbstractMessage::ERROR
                );
            }
        } else {
            $this->addFlashMessage(
                $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.error.message'),
                $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang.xlf:flashmessage.delete.error.title'),
                AbstractMessage::ERROR
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

    public function listAction(): ResponseInterface
    {
        if ($this->accessControlService->hasLoggedInFrontendUser()) {
            $feUserUid = $this->accessControlService->getFrontendUserUid();
            $feUser = $this->frontendUserRepository->findByIdentifier($feUserUid);
            $entries = $this->entryRepository->getByUserId($feUserUid);
            $cancelDate = new \DateTime();
            $cancelDate->modify('+ ' . $this->settings['cancelTime'] . ' minutes');

            $this->view->assignMultiple([
                'feUser' => $feUser,
                'entries' => $entries,
                'cancelDate' => $cancelDate,
            ]);
        }
        return $this->htmlResponse();
    }

    /**
     * @return string|void
     * @throws StopActionException
     */
    public function errorAction(): ResponseInterface
    {
        if ($this->request->getControllerActionName() === 'create') {

            /** @var Request $referringRequest */
            $referringRequest = $this->request->getReferringRequest();

            if ($referringRequest !== null) {
                $originalRequest = clone $this->request;
                $this->request->setOriginalRequest($originalRequest);
                $this->request->setOriginalRequestMappingResults($this->arguments->validate());
                $this->forward(
                    $referringRequest->getControllerActionName(),
                    $referringRequest->getControllerName(),
                    $referringRequest->getControllerExtensionName(),
                    $referringRequest->getArguments()
                );
            }
        }
        return $this->htmlResponse();
    }

    public function injectAccessControlService(AccessControlService $accessControlService): void
    {
        $this->accessControlService = $accessControlService;
    }

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectTimeslotRepository(TimeslotRepository $timeslotRepository): void
    {
        $this->timeslotRepository = $timeslotRepository;
    }

    public function injectCalendarRepository(CalendarRepository $calendarRepository): void
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository): void
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }
}
