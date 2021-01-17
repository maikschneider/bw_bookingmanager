<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\ViewInterface;

class BackendController
{

    /**
     * ModuleTemplate object
     *
     * @var ModuleTemplate
     */
    protected ModuleTemplate $moduleTemplate;

    /**
     * @var ViewInterface
     */
    protected ViewInterface $view;

    protected ServerRequestInterface $request;

    protected ObjectManager $objectManager;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    public function calendarAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $pid = $this->getCurrentPid();

        $this->initializeView('calendar');

        $calendarRepository = $this->objectManager->get(CalendarRepository::class);
        $calendars = $calendarRepository->findAllByPid($pid);

        $viewState = $this->getCalendarViewState();
        $viewState->addCalendars($calendars);

        $pageRenderer = $this->moduleTemplate->getPageRenderer();
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendCalendarContextMenuActions');
        //$pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendModuleCalendar');

        $this->view->assign('calendars', $calendars);
        $this->view->assign('viewState', json_encode($viewState, JSON_THROW_ON_ERROR));

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    protected function getCurrentPid(): int
    {
        $params = $this->request->getQueryParams();
        $pid = (int)$params['id'];
        return $pid ?: 0;
    }

    protected function getCalendarViewState(): BackendCalendarViewState
    {
        $pid = $this->getCurrentPid();
        return BackendCalendarViewState::getFromUserSettings($pid);
    }

    /**
     * Initializes the view by setting the templateName
     *
     * @param string $templateName
     */
    protected function initializeView(string $templateName): void
    {
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:bw_bookingmanager/Resources/Private/Templates/Backend']);
        $this->view->setPartialRootPaths(['EXT:bw_bookingmanager/Resources/Private/Partials/Backend']);
        $this->view->setLayoutRootPaths(['EXT:bw_bookingmanager/Resources/Private/Layouts/Backend']);

        $this->generateMenu($templateName);
        $this->createButtons($templateName);
    }

    protected function generateMenu($currentTemplate): void
    {
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('bw_bookingmanager');
        $llPrefix = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:module.';

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $actions = [
            ['action' => 'calendar', 'label' => 'shift', 'route' => 'bookingmanager_calendar'],
            ['action' => 'entryList', 'label' => 'entryListing', 'route' => 'bookingmanager_entry_list'],
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL($llPrefix . $action['label']))
                ->setHref($uriBuilder->buildUriFromRoute($action['route']))
                ->setActive($currentTemplate === $action['action']);
            $menu->addMenuItem($item);
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function createButtons(string $currentTemplate): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $currentPid = $this->getCurrentPid();
        $returnUrl = (string)$uriBuilder->buildUriFromRoute(
            'bookingmanager_calendar',
            ['id' => $this->getCurrentPid()]
        );

        // New Entry Button
        $buttons = [
            [
                'table' => 'tx_bwbookingmanager_domain_model_entry',
                'label' => 'flexforms_general.mode.entry_new',
                'icon' => 'ext-bwbookingmanager-type-entry',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_blockslot',
                'label' => 'flexforms_general.mode.blockslot_new',
                'icon' => 'ext-bwbookingmanager-type-blockslot',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_holiday',
                'label' => 'flexforms_general.mode.holiday_new',
                'icon' => 'ext-bwbookingmanager-type-holiday',
                'overlay' => 'overlay-new',
                'position' => ButtonBar::BUTTON_POSITION_LEFT,
                'group' => 2
            ]
        ];

        if ($currentTemplate === 'calendar') {
            $viewState = $this->getCalendarViewState();
            $buttons = array_merge($buttons, [
                [
                    'label' => 'flexforms_general.mode.show_past_timeslots',
                    'icon' => 'ext-bwbookingmanager-type-timeslot',
                    'overlay' => 'overlay-endtime',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'pastTimeslots'
                    ],
                    'classes' => $viewState->pastTimeslots ? 'active' : ''
                ],
                [
                    'label' => 'flexforms_general.mode.show_not_bookable_timeslots',
                    'icon' => 'ext-bwbookingmanager-type-timeslot',
                    'overlay' => 'overlay-readonly',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'notBookableTimeslots'
                    ],
                    'classes' => $viewState->notBookableTimeslots ? 'active' : ''
                ],
                [
                    'label' => 'flexforms_general.mode.show_past_entries',
                    'icon' => 'ext-bwbookingmanager-type-entry',
                    'overlay' => 'overlay-endtime',
                    'position' => ButtonBar::BUTTON_POSITION_RIGHT,
                    'group' => 3,
                    'data-attrs' => [
                        'changeViewState' => 'pastEntries'
                    ],
                    'classes' => $viewState->pastEntries ? 'active' : ''
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
                            $currentPid => 'new'
                        ]
                    ],
                    'returnUrl' => $returnUrl
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
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT, 4);
    }

    private function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    public function entryListAction(\Psr\Http\Message\ServerRequestInterface $request): ResponseInterface
    {
        $this->initializeView('entryList');

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }
}
