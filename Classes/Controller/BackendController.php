<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Dto\AdministrationDemand;
use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Grpc\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\ViewInterface;

class BackendController extends ActionController
{

    protected CalendarRepository $calendarRepository;

    protected ModuleTemplateFactory $moduleTemplateFactory;

    protected ModuleTemplate $moduleTemplate;

    protected string $currentAction = '';

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory, CalendarRepository $calendarRepository)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->calendarRepository = $calendarRepository;
    }

    protected function initializeExtbaseController(string $currentAction, ServerRequest $request = null): void
    {
        $this->currentAction = $currentAction;

        if (!$request) {
            $this->request = $request;
        }

        if (!$this->moduleTemplate) {
            $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        }

        // set typoscript settings
        $typoscript = GeneralUtility::makeInstance(ConfigurationManager::class)->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $tsService = GeneralUtility::makeInstance(TypoScriptService::class);
        $settings = $tsService->convertTypoScriptArrayToPlainArray($typoscript);
        $this->settings = $settings['module']['tx_bwbookingmanager']['settings'];

        // set view
        if (!$this->view) {
            $this->view = GeneralUtility::makeInstance(StandaloneView::class);
            $this->view->setLayoutRootPaths($settings['module']['tx_bwbookingmanager']['view']['layoutRootPaths']);
            $this->view->setTemplateRootPaths($settings['module']['tx_bwbookingmanager']['view']['templateRootPaths']);
            $this->view->setPartialRootPaths($settings['module']['tx_bwbookingmanager']['view']['partialRootPaths']);
            $this->view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Templates/Backend/' . $this->currentAction . '.html'));
        }

        // include javascript
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/AdministrationModule');
        if ((int)$this->settings['showConfirmButton']) {
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Tooltip');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BetterRecordlist');
        }

        // generate navigation components
        $this->generateMenu();
        $this->createButtons();
    }

//    public function indexAction(): ResponseInterface
//    {
//        $pid = $this->getCurrentPid();
//        $selectableRoutes = ['entryList', 'calendar'];
//        $selectedRoute = $GLOBALS['BE_USER']->getModuleData('bwbookingmanager/selectedRoute-' . $pid) ?? 0;
//        $methodName = $selectableRoutes[$selectedRoute];
//        return (new ForwardResponse($methodName));
//    }

    protected function getCurrentPid(): int
    {
        $params = $this->request->getQueryParams();
        $pid = (int)$params['id'];
        return $pid ?: 0;
    }

    public function calendarAction(ServerRequest $request = null): ResponseInterface
    {
        $this->initializeExtbaseController('calendar', $request);

        $pid = $this->getCurrentPid();
        $calendars = $this->calendarRepository->findAllByPid($pid);

        $viewState = $this->getCalendarViewState();
        $viewState->addCalendars($calendars);
        $viewState->addTypoScriptOptionOverrides();

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendCalendarContextMenuActions');

        $this->view->assign('calendars', $calendars);
        $this->view->assign('viewState', json_encode($viewState, JSON_THROW_ON_ERROR));

        // save selected route
        $moduleDataIdentifier = 'bwbookingmanager/selectedRoute-' . $this->getCurrentPid($request);
        $GLOBALS['BE_USER']->pushModuleData($moduleDataIdentifier, 1);

        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    protected function generateMenu(): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('bw_bookingmanager');
        $llPrefix = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:module.';

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $actions = [
            ['action' => 'entryList', 'label' => 'entryListing', 'route' => 'bookingmanager_entry_list'],
            ['action' => 'calendar', 'label' => 'calendar', 'route' => 'bookingmanager_calendar'],
        ];

        foreach ($actions as $action) {

            $isActive = $this->currentAction === $action['action'];

            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL($llPrefix . $action['label']))
                ->setHref((string)$uriBuilder->buildUriFromRoute($action['route'], ['id' => $this->getCurrentPid()]))
                ->setActive($isActive);
            $menu->addMenuItem($item);
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function createButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $currentPid = $this->getCurrentPid();

        // New Entry Button
        $buttons = [
            [
                'table' => 'tx_bwbookingmanager_domain_model_entry',
                'label' => 'flexforms_general.mode.entry_new',
                'icon' => 'ext-bwbookingmanager-type-entry',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2,
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_blockslot',
                'label' => 'flexforms_general.mode.blockslot_new',
                'icon' => 'ext-bwbookingmanager-type-blockslot',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2,
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_holiday',
                'label' => 'flexforms_general.mode.holiday_new',
                'icon' => 'ext-bwbookingmanager-type-holiday',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2,
            ],
        ];

        if ($this->currentAction === 'entryList') {
            $returnUrl = (string)$uriBuilder->buildUriFromRoute(
                'bookingmanager_entry_list',
                ['id' => $this->getCurrentPid()]
            );

            $buttons = array_merge($buttons, [
                [
                    'label' => 'administration.filter.buttonTitle',
                    'icon' => 'actions-filter',
                    'position' => ButtonBar::BUTTON_POSITION_LEFT,
                    'group' => 3,
                    'data-attrs' => [
                        'togglelink' => '1',
                        'toggle' => 'tooltip',
                    ],
                    'classes' => '',
                ],
                [
                    'label' => 'administration.print.buttonTitle',
                    'icon' => 'actions-file-csv',
                    'position' => ButtonBar::BUTTON_POSITION_LEFT,
                    'group' => 3,
                    'data-attrs' => [
                        'toggle' => 'tooltip',
                    ],
                    'classes' => 'print',
                ],
            ]);
        }

        if ($this->currentAction === 'calendar') {
            $viewState = $this->getCalendarViewState();
            $returnUrl = (string)$uriBuilder->buildUriFromRoute(
                'bookingmanager_calendar',
                ['id' => $this->getCurrentPid()]
            );
            $buttons = array_merge($buttons, [
                [
                    'label' => 'flexforms_general.mode.show_past_timeslots',
                    'icon' => 'ext-bwbookingmanager-type-timeslot',
                    'overlay' => 'overlay-endtime',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'pastTimeslots',
                    ],
                    'classes' => $viewState->pastTimeslots ? 'active' : '',
                ],
                [
                    'label' => 'flexforms_general.mode.show_not_bookable_timeslots',
                    'icon' => 'ext-bwbookingmanager-type-timeslot',
                    'overlay' => 'overlay-readonly',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'notBookableTimeslots',
                    ],
                    'classes' => $viewState->notBookableTimeslots ? 'active' : '',
                ],
                [
                    'label' => 'flexforms_general.mode.show_past_entries',
                    'icon' => 'ext-bwbookingmanager-type-entry',
                    'overlay' => 'overlay-endtime',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'pastEntries',
                    ],
                    'classes' => $viewState->pastEntries ? 'active' : '',
                ],
            ]);
        }

        foreach ($buttons as $tableConfiguration) {
            $uri = '#';
            $title = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:' . $tableConfiguration['label']);
            $dataAttrs = $tableConfiguration['data-attrs'] ?? [];
            $dataAttrs['toggle'] = 'tooltip';
            $dataAttrs['placement'] = 'bottom';
            $dataAttrs['title'] = $title;

            if (isset($tableConfiguration['table'])) {

                // check persmissions
                $isAdmin = $this->getBackendUser()->isAdmin();
                $allowedToEditTable = $this->getBackendUser()->check(
                    'tables_modify',
                    $tableConfiguration['table']
                );

                if (!$isAdmin && !$allowedToEditTable) {
                    continue;
                }

                $uri = $uriBuilder->buildUriFromRoute('record_edit', [
                    'edit' => [
                        $tableConfiguration['table'] => [
                            $currentPid => 'new',
                        ],
                    ],
                    'returnUrl' => $returnUrl,
                ]);
            }

            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($uri)
                ->setDataAttributes($dataAttrs)
                ->setTitle($title)
                ->setClasses($tableConfiguration['classes'] ?? '')
                ->setIcon($iconFactory->getIcon(
                    $tableConfiguration['icon'],
                    Icon::SIZE_SMALL,
                    $tableConfiguration['overlay']
                ));
            $buttonBar->addButton($viewButton, $tableConfiguration['position'], $tableConfiguration['group']);
        }

        // Refresh
        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT, 4);
    }

    protected function getCalendarViewState(): BackendCalendarViewState
    {
        $pid = $this->getCurrentPid();
        return BackendCalendarViewState::getFromUserSettings($pid);
    }

    private function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    public function entryListAction(ServerRequest $request = null): ResponseInterface
    {
        $this->initializeExtbaseController('entryList', $request);

        $hideForm = true;
        $queryParams = $this->request->getQueryParams();
        $demand = GeneralUtility::makeInstance(AdministrationDemand::class);
        // override default demand values with values from GET request
        if (is_array($queryParams['demand'])) {
            $hideForm = false;
            foreach ($queryParams['demand'] as $key => $value) {
                if (property_exists(AdministrationDemand::class, $key)) {
                    $getter = 'set' . ucfirst($key);
                    $demand->$getter($value);
                }
            }
        }

        $calendars = $this->calendarRepository->findAll();
        $calendar = $calendars && $calendars->count() ? $calendars->getFirst() : [];

        // save selected route
        $moduleDataIdentifier = 'bwbookingmanager/selectedRoute-' . $this->getCurrentPid();
        $GLOBALS['BE_USER']->pushModuleData($moduleDataIdentifier, 0);

        $this->view->assign('hideForm', $hideForm);
        $this->view->assign('page', $this->getCurrentPid());
        $this->view->assign('demand', $demand);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('calendars', $calendars);

        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }
}
