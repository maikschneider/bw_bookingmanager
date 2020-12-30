<?php

namespace Blueways\BwBookingmanager\Controller;

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

    protected $returnUrl;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $this->returnUrl = $uriBuilder->buildUriFromRoute('bookingmanager_calendar', []);
    }

    public function calendarAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $pid = $this->getCurrentPid();

        $viewState = $GLOBALS['BE_USER']->getModuleData('bwbookingmanager/calendarViewState-' . $pid) ?: [];
        $startDate = $viewState ? $viewState['start'] : '';
        $startView = $viewState ? $viewState['calendarView'] : '';

        $this->initializeView('calendar');

        $calendarRepository = $this->objectManager->get(CalendarRepository::class);
        $language = $this->getLanguageService()->lang;
        $calendars = $calendarRepository->findAllByPid($pid);

        $events = [];
        $events['extraParams'] = [];
        $events['extraParams']['pid'] = $pid;

        $this->view->assign('events', json_encode($events, JSON_THROW_ON_ERROR));
        $this->view->assign('calendars', $calendars);
        $this->view->assign('language', $language);
        $this->view->assign('startDate', $startDate);
        $this->view->assign('startView', $startView);

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    protected function getCurrentPid(): int
    {
        $params = $this->request->getQueryParams();
        $pid = (int)$params['id'];
        return $pid ?: 0;
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

    protected function generateMenu($currentTemplate)
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

    protected function createButtons(string $currentTemplate)
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $currentPid = $this->getCurrentPid();

        // Filter and print Buttons
//        if ($this->request->getControllerActionName() === 'index') {
//            $toggleButton = $buttonBar->makeLinkButton()
//                ->setHref('#')
//                ->setDataAttributes([
//                    'togglelink' => '1',
//                    'toggle' => 'tooltip',
//                    'placement' => 'bottom',
//                ])
//                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.filter.buttonTitle'))
//                ->setIcon($this->iconFactory->getIcon('actions-filter', Icon::SIZE_SMALL));
//
//            $printButton = $buttonBar->makeLinkButton()
//                ->setHref('#')
//                ->setDataAttributes([
//                    'toggle' => 'tooltip',
//                    'placement' => 'bottom',
//                ])
//                ->setOnClick('window.print()')
//                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.print.buttonTitle'))
//                ->setIcon($this->iconFactory->getIcon('actions-file-csv', Icon::SIZE_SMALL));
//
//            $buttonBar->addButton($toggleButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
//            $buttonBar->addButton($printButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
//        }

        // New Entry Button
        $buttons = [
            [
                'table' => 'tx_bwbookingmanager_domain_model_entry',
                'label' => 'flexforms_general.mode.entry_new',
                'icon' => 'ext-bwbookingmanager-type-entry'
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_blockslot',
                'label' => 'flexforms_general.mode.blockslot_new',
                'icon' => 'ext-bwbookingmanager-type-blockslot'
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_holiday',
                'label' => 'flexforms_general.mode.holiday_new',
                'icon' => 'ext-bwbookingmanager-type-holiday'
            ]
        ];
        foreach ($buttons as $key => $tableConfiguration) {

            // check persmissions
            $isAdmin = $this->getBackendUser()->isAdmin();
            $allowedToEditTable = $this->getBackendUser()->check(
                'tables_modify',
                $tableConfiguration['table']
            );

            if (!$isAdmin && !$allowedToEditTable) {
                continue;
            }

            $title = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:' . $tableConfiguration['label']);
            $uri = $uriBuilder->buildUriFromRoute('record_edit', [
                'edit' => [
                    $tableConfiguration['table'] => [
                        $currentPid => 'new'
                    ]
                ],
            ]);

            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($uri)
                ->setDataAttributes([
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                    'title' => $title
                ])
                ->setTitle($title)
                ->setIcon($iconFactory->getIcon(
                    $tableConfiguration['icon'],
                    Icon::SIZE_SMALL,
                    'overlay-new'
                ));
            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
        }

        // Refresh
        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
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

    protected function getReturnUrl(): string
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
//        return (string)$uriBuilder->buildUriFromRoute()
    }
}
