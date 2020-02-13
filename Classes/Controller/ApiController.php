<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Helper\NotificationManager;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use ReflectionClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ApiController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var array
     */
    protected $configuration = [
        'newEntry' => [
            '_exclude' => ['token', 'confirmed'],
            '_descend' => [
                'timeslot' => [],
                'calendar' => [],
                'endDate' => [],
                'startDate' => [],
                'displayStartDate' => [],
                'displayEndDate' => [],
            ],
        ],
        'user' => [
            '_exclude' => ['password']
        ]
    ];

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     */
    protected $calendarRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     */
    protected $entryRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var \Blueways\BwBookingmanager\Service\AccessControlService
     */
    protected $accessControlService;

    public function calendarListAction()
    {
        $calendars = $this->calendarRepository->findAllIgnorePid();

        $this->view->assign('calendars', $calendars);
        $this->view->setVariablesToRender(array('calendars'));
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \Exception
     */
    public function calendarShowAction(Calendar $calendar)
    {
        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);

        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        $calendarManager = $this->objectManager->get(CalendarManagerUtility::class, $calendar);
        $configuration = $calendarManager->getConfiguration($dateConf);

        if ($user = $this->accessControlService->getFrontendUserUid()) {
            $user = $this->frontendUserRepository->findByIdentifier($user);
        }

        $this->view->assignMultiple([
            'configuration' => $configuration,
            'calendar' => $calendar,
            'user' => $user,
        ]);

        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(array('configuration', 'calendar', 'user'));
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param int $day
     * @param int $month
     * @param int $year
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function calendarShowDateAction(Calendar $calendar, int $day, int $month, int $year)
    {
        $startDate = \DateTime::createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        $calendarManager = $this->objectManager->get(CalendarManagerUtility::class, $calendar);
        $configuration = $calendarManager->getConfiguration($dateConf);

        if ($user = $this->accessControlService->getFrontendUserUid()) {
            $user = $this->frontendUserRepository->findByIdentifier($user);
        }

        $this->view->assignMultiple([
            'configuration' => $configuration,
            'calendar' => $calendar,
            'user' => $user,
        ]);

        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(array('configuration', 'calendar', 'user'));
    }

    public function initializeEntryCreateAction()
    {
        if (!$this->arguments->hasArgument('newEntry')) {
            $content = ['errors' => ['entry' => 'no data for new entry given']];
            $this->throwStatus(406, 'Validation failed', json_encode($content));
        }

        // allow creation of Entry
        $propertyMappingConfiguration = $this->arguments->getArgument('newEntry')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->setTypeConverterOption(
            PersistentObjectConverter::class,
            PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
            true
        );

        // set Entry class name from calendar constant
        /** @var array $newEntry */
        $newEntry = $this->request->getArgument('newEntry');
        $calendar = $this->calendarRepository->findByIdentifier((int)$newEntry['calendar']);
        if (!$calendar) {
            $content = ['errors' => ['calendar' => 'calendar not found']];
            $this->throwStatus(406, 'Validation failed', json_encode($content));
        }
        $entityClass = $calendar::ENTRY_TYPE_CLASSNAME;
        $propertyMappingConfiguration->setTypeConverterOption(
            PersistentObjectConverter::class,
            PersistentObjectConverter::CONFIGURATION_TARGET_TYPE,
            $entityClass
        );

        // convert timestamps
        $propertyMappingConfiguration->forProperty('startDate')->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'U'
        );
        $propertyMappingConfiguration->forProperty('endDate')->setTypeConverterOption(
            DateTimeConverter::class,
            DateTimeConverter::CONFIGURATION_DATE_FORMAT,
            'U'
        );

        // set allowed properties
        $propertyMappingConfiguration->allowProperties(...$this->getAllowedEntryFields($entityClass));
        $propertyMappingConfiguration->skipUnknownProperties();

        // set validator
        $validatorResolver = $this->objectManager->get(\TYPO3\CMS\Extbase\Validation\ValidatorResolver::class);
        $validatorConjunction = $validatorResolver->getBaseValidatorConjunction($entityClass);
        $entryValidator = $validatorResolver->createValidator('\Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator');
        $validatorConjunction->addValidator($entryValidator);
        $this->arguments->getArgument('newEntry')->setValidator($validatorConjunction);
    }

    private function getAllowedEntryFields($entityClass)
    {
        $reflectionClass = $this->objectManager->get(ReflectionClass::class, $entityClass);
        $entryFields = $reflectionClass->getProperties();
        $entryFields = array_filter($entryFields, function ($obj) {
            $excludeFields = [
                'pid',
                'uid',
                '_localizedUid',
                '_languageUid',
                '_versionedUid',
                'token',
                'confirmed',
                'crdate',
            ];
            return !in_array($obj->name, $excludeFields);
        });

        $entryFields = array_map(function ($field) {
            return $field->name;
        }, $entryFields);

        return $entryFields;
    }

    public function injectAccessControlService(
        \Blueways\BwBookingmanager\Service\AccessControlService $accessControlService
    ) {
        $this->accessControlService = $accessControlService;
    }

    public function injectCalendarRepository(
        \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository $calendarRepository
    ) {
        $this->calendarRepository = $calendarRepository;
    }

    public function injectEntryRepository(\Blueways\BwBookingmanager\Domain\Repository\EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectFrontendUserRepository(
        \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $frontendUserRepository
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    public function injectTimeslotRepository(
        \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository $timeslotRepository
    ) {
        $this->timeslotRepository = $timeslotRepository;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $newEntry
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function entryCreateAction($newEntry)
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
        $notificationManager = $this->objectManager->get(NotificationManager::class, $newEntry);
        $notificationManager->notify();

        $this->view->setConfiguration($this->configuration);
        $this->view->assign('newEntry', $newEntry);
        $this->view->setVariablesToRender(array('newEntry'));
    }

    /**
     * @throws \TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function loginAction()
    {
        $loginData = [
            'uname' => GeneralUtility::_POST('username'),
            'uident_text' => GeneralUtility::_POST('password'),
        ];

        if (!$loginData['uname'] || !$loginData['uident_text']) {
            $this->throwStatus(403, 'Login failed', json_encode([]));
        }

        $GLOBALS['TSFE']->fe_user->checkPid = 0;
        $info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
        /** @var FrontendUserAuthentication $userAuth */
        $userAuth = $this->objectManager->get(FrontendUserAuthentication::class);
        $userAuth->checkPid = false;
        $user = $userAuth->fetchUserRecord($info['db_user'], $loginData['uname']);

        if (!$user) {
            $this->throwStatus(404, 'User not found', json_encode([]));
        }

        $passwordHashFactory = $this->objectManager->get(
            \TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory::class
        );
        $passwordHash = $passwordHashFactory->getDefaultHashInstance('FE');
        $isValidLoginData = $passwordHash->checkPassword($loginData['uident_text'], $user['password']);

        if (!$isValidLoginData) {
            $this->throwStatus(403, 'Login failed', json_encode([]));
        }

        $GLOBALS['TSFE']->fe_user->forceSetCookie = true;
        $GLOBALS['TSFE']->fe_user->dontSetCookie = false;
        $GLOBALS['TSFE']->fe_user->start();
        $GLOBALS['TSFE']->fe_user->createUserSession($user);
        $GLOBALS['TSFE']->fe_user->loginUser = 1;

        $this->view->assign('user', $user);
        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(array('user'));
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function logoutAction()
    {
        $userAuth = $this->objectManager->get(FrontendUserAuthentication::class);
        $userAuth->removeCookie('fe_typo_user');
        $GLOBALS['TSFE']->fe_user->loginUser = 0;

        $this->throwStatus(200, 'Logout successful', json_encode([]));
    }

    /**
     * @return string|void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function errorAction()
    {
        if ($this->request->getControllerActionName() === "entryCreate") {
            $errors = $this->arguments->validate()->forProperty('newEntry')->getFlattenedErrors();

            $errors = array_map(function ($error) {
                return $error[0]->getMessage();
            }, $errors);

            $content = [
                'errors' => $errors
            ];

            $this->throwStatus(406, 'Validation failed', json_encode($content));
        }
    }
}
