<?php

namespace Blueways\BwBookingmanager\Controller\Backend;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\AbstractTemplateView;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AbstractModuleController
{

    protected CalendarRepository $calendarRepository;

    protected ModuleTemplateFactory $moduleTemplateFactory;

    protected ?ModuleTemplate $moduleTemplate = null;

    protected ResponseFactory $responseFactory;

    protected ?AbstractTemplateView $view = null;

    protected ?ServerRequestInterface $request = null;

    protected string $currentAction = '';

    protected int $pid = 0;

    protected array $settings = [];

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory, CalendarRepository $calendarRepository, ResponseFactory $responseFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->calendarRepository = $calendarRepository;
        $this->responseFactory = $responseFactory;
    }

    protected function initializeExtbaseController(string $currentAction, ServerRequestInterface $request = null): void
    {
        $this->currentAction = $currentAction;

        if (!$this->request) {
            $this->request = $request;
        }

        if (!$this->moduleTemplate) {
            $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        }

        // set pid
        $params = $this->request->getQueryParams();
        $this->pid = (int)$params['id'];

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
            $this->view->setTemplate($this->currentAction);
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

    protected function createButtons(): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $currentPid = $this->pid;
        $returnUrl = (string)$uriBuilder->buildUriFromRoute(
            'bookingmanager_entry_list',
            ['id' => $this->pid]
        );

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
            $viewState = BackendCalendarViewState::getFromUserSettings($this->pid);
            $returnUrl = (string)$uriBuilder->buildUriFromRoute(
                'bookingmanager_calendar',
                ['id' => $this->pid]
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
            $title = $GLOBALS['LANG']->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:' . $tableConfiguration['label']);
            $dataAttrs = $tableConfiguration['data-attrs'] ?? [];
            $dataAttrs['toggle'] = 'tooltip';
            $dataAttrs['placement'] = 'bottom';
            $dataAttrs['title'] = $title;

            if (isset($tableConfiguration['table'])) {

                // check persmissions
                $isAdmin = $GLOBALS['BE_USER']->isAdmin();
                $allowedToEditTable = $GLOBALS['BE_USER']->check(
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
            ->setTitle($GLOBALS['LANG']->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT, 4);
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
                ->setTitle($GLOBALS['LANG']->sL($llPrefix . $action['label']))
                ->setHref((string)$uriBuilder->buildUriFromRoute($action['route'], ['id' => $this->pid]))
                ->setActive($isActive);
            $menu->addMenuItem($item);
        }
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }
}
