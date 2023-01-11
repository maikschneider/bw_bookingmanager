<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Validator\FeUserCreateValidator;
use Blueways\BwBookingmanager\Event\AfterEntryCreationEvent;
use Blueways\BwBookingmanager\Service\AccessControlService;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class ApiController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    protected array $configuration = [
        'newEntry' => [
            '_exclude' => ['token', 'confirmed'],
            '_descend' => [
                'timeslot' => [],
                'calendar' => [],
                'endDate' => [],
                'startDate' => [],
                'displayStartDate' => [],
                'displayEndDate' => [],
                'feUser' => [
                    '_exclude' => ['password'],
                ],
            ],
        ],
        'user' => [
            '_exclude' => ['password'],
        ],
    ];

    protected CalendarRepository $calendarRepository;

    protected EntryRepository $entryRepository;

    protected AccessControlService $accessControlService;

    public function __construct(
        CalendarRepository $calendarRepository,
        EntryRepository $entryRepository,
        AccessControlService $accessControlService
    ) {
        $this->calendarRepository = $calendarRepository;
        $this->entryRepository = $entryRepository;
        $this->accessControlService = $accessControlService;
    }

    /**
     * @throws InvalidPasswordHashException
     * @throws PropagateResponseException
     * @throws \JsonException
     * @throws NoSuchArgumentException
     */
    public function initializeEntryCreateAction(): void
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
            $this->throwStatus(406, 'Validation failed', json_encode($content, JSON_THROW_ON_ERROR));
        }
        $entityClass = $calendar::ENTRY_TYPE_CLASSNAME;
        $propertyMappingConfiguration->setTypeConverterOption(
            PersistentObjectConverter::class,
            PersistentObjectConverter::CONFIGURATION_TARGET_TYPE,
            $entityClass
        );
        $this->arguments->getArgument('newEntry')->setDataType($entityClass);

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

        // add fe_user if logged in
        $userId = $this->accessControlService->getFrontendUserUid();
        if ($userId) {
            $this->arguments->addNewArgument('user', FrontendUser::class);
            $this->request->setArgument('user', (string)$userId);
        }

        // create fe_user from entry
        $doCreateUser = (int)GeneralUtility::_POST('createUserAccount') === 1 || GeneralUtility::_POST('createUserAccount') === 'on';
        if ($doCreateUser && (int)$this->settings['userStoragePid']) {
            $newEntry = $this->request->getArgument('newEntry');

            $feUser = [];
            $feUser['pid'] = (int)$this->settings['userStoragePid'];
            $feUser['username'] = $newEntry['email'];
            $feUser['email'] = $newEntry['email'];
            $feUser['firstName'] = $newEntry['prename'];
            $feUser['lastName'] = $newEntry['name'];
            $feUser['address'] = $newEntry['street'];
            $feUser['zip'] = $newEntry['zip'];
            $feUser['telephone'] = $newEntry['phone'];
            $feUser['city'] = $newEntry['city'];

            // generate hashed password
            $userPassword = GeneralUtility::_POST('createUserAccountPassword');
            if ($userPassword) {
                $passwordHashFactory = $this->objectManager->get(PasswordHashFactory::class);
                $passwordHashInstance = $passwordHashFactory->getDefaultHashInstance('FE');
                $password = $passwordHashInstance->getHashedPassword($userPassword);
                $feUser['password'] = $password;
            }

            // set the user parameter with newly created feUer
            $this->arguments->addNewArgument('user', FrontendUser::class);
            $this->request->setArgument('user', $feUser);

            // allow creation of fe_user
            $propertyMappingConfiguration = $this->arguments->getArgument('user')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->setTypeConverterOption(
                PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                true
            );
            $propertyMappingConfiguration->allowProperties(...$this->getAllowedEntryFields(FrontendUser::class));
            $propertyMappingConfiguration->skipUnknownProperties();

            // set user validator
            $validatorResolver = GeneralUtility::makeInstance(ValidatorResolver::class);
            $validatorConjunction = $validatorResolver->getBaseValidatorConjunction(FrontendUser::class);
            $userValidator = $validatorResolver->createValidator(FeUserCreateValidator::class);
            $validatorConjunction->addValidator($userValidator);
            $this->arguments->getArgument('user')->setValidator($validatorConjunction);
        }
    }

    private function getAllowedEntryFields($entityClass)
    {
        $reflectionClass = GeneralUtility::makeInstance(ReflectionClass::class, $entityClass);
        $entryFields = $reflectionClass->getProperties();
        $entryFields = array_filter($entryFields, function ($obj) {
            $excludeFields = [
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

        return array_map(function ($field) {
            return $field->name;
        }, $entryFields);
    }

    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("Blueways\BwBookingmanager\Domain\Validator\EntryCreateValidator", param="newEntry")
     * @throws IllegalObjectTypeException
     * @throws NoSuchCacheException
     */
    public function entryCreateAction(Entry $newEntry, ?FrontendUser $user = null): ResponseInterface
    {
        $newEntry->generateToken();
        // override PID (just in case the storage PID differs from current calendar)
        $eventTomezoneOffset = (new \DateTime($newEntry->getStartDate()->format('D jS M y'), new \DateTimeZone('Europe/Berlin')))->getOffset();
        $x = $newEntry->getStartDate();
        $newstart = $x->modify('- '.($eventTomezoneOffset.'seconds'));
        $y = $newEntry->getEndDate();
        $newend = $y->modify('- '.($eventTomezoneOffset.'seconds'));
        $newEntry->setStartDate($newstart);
        $newEntry->setEndDate($newend);
        $newEntry->setPid($newEntry->getCalendar()->getPid());
        $this->entryRepository->add($newEntry);

        if ($user) {
            $newEntry->setFeUser($user);
        }

        // persist by hand to get uid field and make redirect possible
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->persistAll();

        // login the newly created user
        if ($user && !$user->getLastlogin()) {
            $GLOBALS['TSFE']->fe_user->checkPid = 0;
            $info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
            $userAuth = GeneralUtility::makeInstance(FrontendUserAuthentication::class);
            $userAuth->checkPid = false;
            $tempUser = $userAuth->fetchUserRecord($info['db_user'], $user->getUsername());

            $GLOBALS['TSFE']->fe_user->forceSetCookie = true;
            $GLOBALS['TSFE']->fe_user->dontSetCookie = false;
            $GLOBALS['TSFE']->fe_user->start();
            $GLOBALS['TSFE']->fe_user->createUserSession($tempUser);
            $GLOBALS['TSFE']->fe_user->loginUser = 1;
        }

        // send mails
        $this->eventDispatcher->dispatch(new AfterEntryCreationEvent($newEntry));

        $this->view->setConfiguration($this->configuration);
        $this->view->assign('newEntry', $newEntry);
        $this->view->setVariablesToRender(['newEntry']);
        return $this->htmlResponse();
    }

    public function loginAction(): ResponseInterface
    {
        if ($this->accessControlService->hasLoggedInFrontendUser()) {
            $this->performLogout();
        }

        $loginData = [
            'uname' => GeneralUtility::_POST('username'),
            'uident_text' => GeneralUtility::_POST('password'),
        ];

        if (!$loginData['uname'] || !$loginData['uident_text']) {
            $this->throwStatus(
                403,
                'Login failed',
                json_encode(['errors' => ['username' => 'no username or password given']])
            );
        }

        $GLOBALS['TSFE']->fe_user->checkPid = 0;
        $info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
        /** @var FrontendUserAuthentication $userAuth */
        $userAuth = $this->objectManager->get(FrontendUserAuthentication::class);
        $userAuth->checkPid = false;
        $user = $userAuth->fetchUserRecord($info['db_user'], $loginData['uname']);

        if (!$user) {
            $this->throwStatus(404, 'User not found', json_encode(['errors' => ['username' => 'user not found']]));
        }

        $passwordHashFactory = $this->objectManager->get(
            PasswordHashFactory::class
        );
        $passwordHash = $passwordHashFactory->getDefaultHashInstance('FE');
        $isValidLoginData = $passwordHash->checkPassword($loginData['uident_text'], $user['password']);

        if (!$isValidLoginData) {
            $this->throwStatus(403, 'Login failed', json_encode(['errors' => ['password' => 'wrong password']]));
        }

        $GLOBALS['TSFE']->fe_user->forceSetCookie = true;
        $GLOBALS['TSFE']->fe_user->dontSetCookie = false;
        $GLOBALS['TSFE']->fe_user->start();
        $GLOBALS['TSFE']->fe_user->createUserSession($user);
        $GLOBALS['TSFE']->fe_user->loginUser = 1;

        $this->view->assign('user', $user);
        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(['user']);
        return $this->htmlResponse();
    }

    protected function performLogout()
    {
        $userAuth = $this->objectManager->get(FrontendUserAuthentication::class);
        $userAuth->removeCookie('fe_typo_user');
        $GLOBALS['TSFE']->fe_user->loginUser = 0;
    }

    public function logoutAction(): ResponseInterface
    {
        $this->performLogout();

        return $this->jsonResponse(json_encode([
            'title' => 'Logout successful',
            'message' => 'You have been successfully logged out.',
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @throws PropagateResponseException
     */
    public function errorAction(): ResponseInterface
    {
        if ($this->request->getControllerActionName() === 'entryCreate') {
            $newEntryErrors = $this->arguments->validate()->forProperty('newEntry')->getErrors();
            $userErrors = $this->arguments->validate()->forProperty('user')->getErrors();
            $allErrors = array_merge($newEntryErrors, $userErrors);

            $allErrors = array_map(function ($error) {
                return $error->getMessage();
            }, $allErrors);

            $content = [
                'errors' => $allErrors,
            ];

            $this->throwStatus(406, 'Validation failed', json_encode($content));
        }
        return $this->htmlResponse();
    }
}
