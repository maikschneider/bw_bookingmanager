<?php

namespace Blueways\BwBookingmanager\Controller;

/**
 * Administration controller for TYPO3 Backend module
 * PHP version 7.2
 *
 * @package  BwBookingManager
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http://www.blueways.de
 */

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\AdministrationDemand;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Lang\LanguageService;

/**
 * AdministrationController
 */
class AdministrationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * entryRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     */
    protected $entryRepository = null;

    /**
     * calendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     */
    protected $calendarRepository = null;

    /**
     * Page uid
     *
     * @var int
     */
    protected $pageUid = 0;

    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @param ViewInterface $view
     */
    public function initializeAction()
    {
        $this->entryRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\EntryRepository::class);
        $this->calendarRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\CalendarRepository::class);
        $this->pageUid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id');

        parent::initializeAction();
    }

    public function indexAction()
    {
        $hideForm = true;
        $demandVars = GeneralUtility::_GET('tx_bwbookingmanager_web_bwbookingmanagertxbookingmanagerm1');
        $demand = GeneralUtility::makeInstance(AdministrationDemand::class);
        // override default demand values with values from GET request
        if (is_array($demandVars['demand'])) {
            $hideForm = false;
            foreach ($demandVars['demand'] as $key => $value) {
                if (property_exists(AdministrationDemand::class, $key)) {
                    $getter = 'set' . ucfirst($key);
                    $demand->$getter($value);
                }
            }
        }

        $calendars = $this->calendarRepository->findAll();

        // use first calendar as default
        if (!$calendars && count($calendars)) {
            $calendar = $calendars->getFirst();
        }

        $this->view->assign('hideForm', $hideForm);
        $this->view->assign('page', $this->pageUid);
        $this->view->assign('demand', $demand);
        $this->view->assign('moduleToken', $this->getToken(true));
        $this->view->assign('calendar', $calendar);
        $this->view->assign('calendars', $calendars);
    }

    /**
     * Get a CSRF token
     *
     * @param bool $tokenOnly Set it to TRUE to get only the token, otherwise including the &moduleToken= as prefix
     * @return string
     */
    protected function getToken($tokenOnly = false)
    {
        if (self::is9up()) {
            $tokenParameterName = 'token';
            $token = FormProtectionFactory::get('backend')->generateToken(
                'route',
                'web_BwBookingmanagerTxBookingmanagerM1'
            );
        } else {
            $tokenParameterName = 'moduleToken';
            $token = FormProtectionFactory::get()->generateToken(
                'moduleCall',
                'web_BwBookingmanagerTxBookingmanagerM1'
            );
        }

        if ($tokenOnly) {
            return $token;
        }

        return '&' . $tokenParameterName . '=' . $token;
    }

    /**
     * @return bool
     */
    private static function is9up(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000;
    }

    public function blockslotAction()
    {
        $hideForm = true;

        $calendars = $this->calendarRepository->findAll();

        $this->view->assign('hideForm', $hideForm);
        $this->view->assign('page', $this->pageUid);
        $this->view->assign('moduleToken', $this->getToken(true));
        $this->view->assign('calendars', $calendars);
    }

    public function dashboardAction()
    {
        $calendars = $this->calendarRepository->findAllIgnorePid();

        $startDate = new \DateTime('now');
        $startDate = $startDate->format('d-m-Y');

        $chart1UriYear = \Blueways\BwBookingmanager\Helper\DashboardCharts::getStaticDashboardChartUri(
            'ajax_dashboard_chart1',
            ['view' => 'year', 'startDate' => $startDate]
        );
        $chart1UriMonth = \Blueways\BwBookingmanager\Helper\DashboardCharts::getStaticDashboardChartUri(
            'ajax_dashboard_chart1',
            ['view' => 'month', 'startDate' => $startDate]
        );
        $chart1UriWeek = \Blueways\BwBookingmanager\Helper\DashboardCharts::getStaticDashboardChartUri(
            'ajax_dashboard_chart1',
            ['view' => 'week', 'startDate' => $startDate]
        );

        $this->view->assign('calendars', $calendars);
        $this->view->assign('chart1UriYear', $chart1UriYear);
        $this->view->assign('chart1UriMonth', $chart1UriMonth);
        $this->view->assign('chart1UriWeek', $chart1UriWeek);
    }

    public function timeslotAction()
    {
        $calendars = $this->calendarRepository->findAll();
        $this->view->assign('calendars', $calendars);
    }

    public function shiftAction()
    {
        $calendars = $this->calendarRepository->findAll();

        $events = [];
        $events['extraParams'] = [];
        $events['extraParams']['pid'] = $this->pageUid;

        $this->view->assign('events', json_encode($events, JSON_THROW_ON_ERROR));
        $this->view->assign('calendars', $calendars);
    }

    /**
     * Creates the URI for a backend action
     *
     * @param string $controller
     * @param string $action
     * @param array $parameters
     * @return string
     */
    protected function getHref($controller, $action, $parameters = [])
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);
        return $uriBuilder->reset()->uriFor($action, $parameters, $controller);
    }

    /**
     * Redirect to form to create a new Entry record
     */
    public function newEntryAction()
    {
        $defaults = [];
        $params = GeneralUtility::_GET('tx_bwbookingmanager_web_bwbookingmanagertxbookingmanagerm1');

        if ($params['calendar'] && $params['timeslot'] && $params['startDate'] && $params['endDate']) {
            $defaults = [
                'defVals[tx_bwbookingmanager_domain_model_entry][calendar]' => $params['calendar'],
                'defVals[tx_bwbookingmanager_domain_model_entry][timeslot]' => $params['timeslot'],
                'defVals[tx_bwbookingmanager_domain_model_entry][startDate]' => $params['startDate'],
                'defVals[tx_bwbookingmanager_domain_model_entry][endDate]' => $params['endDate']
            ];
        }

        $this->redirectToCreateNewRecord('tx_bwbookingmanager_domain_model_entry', $defaults);
    }

    /**
     * Redirect to tceform creating a new record
     *
     * @param string $table table name
     * @param array $defValues
     */
    private function redirectToCreateNewRecord($table, $defValues = [])
    {
        $pid = $this->pageUid;
        if ($pid === 0 && isset($this->tsConfiguration['defaultPid.'])
            && is_array($this->tsConfiguration['defaultPid.'])
            && isset($this->tsConfiguration['defaultPid.'][$table])
        ) {
            $pid = (int)$this->tsConfiguration['defaultPid.'][$table];
        }

        if (self::is9up()) {
            $returnUrl = 'index.php?route=/web/BwBookingmanagerTxBookingmanagerM1/';
        } else {
            $returnUrl = 'index.php?M=web_BwBookingmanagerTxBookingmanagerM1';
        }
        $returnUrl .= '&id=' . $this->pageUid . $this->getToken();

        $params = array_merge([
            'edit[' . $table . '][' . $pid . ']' => 'new',
            'returnUrl' => $returnUrl
        ], $defValues);

        $url = BackendUtilityCore::getModuleUrl('record_edit', $params);
        HttpUtility::redirect($url);
    }

    /**
     * Redirect to form to create a new Blockslot record
     */
    public function newBlockslotAction()
    {
        $this->redirectToCreateNewRecord('tx_bwbookingmanager_domain_model_blockslot');
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

    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        parent::initializeView($view);

        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        if ($view instanceof BackendTemplateView) {
            $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/DateTimePicker');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/AdministrationModule');
        }

        $this->createMenu();
        $this->createButtons();
        $view->assign('is9up', self::is9up());
    }

    /**
     * Create menu
     */
    protected function createMenu()
    {
        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('bw_bookingmanager');

        $actions = [
            ['action' => 'index', 'label' => 'entryListing'],
            ['action' => 'blockslot', 'label' => 'blockslotListing'],
            ['action' => 'dashboard', 'label' => 'dashboard'],
            ['action' => 'shift', 'label' => 'shift'],
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:module.' . $action['label']))
                ->setHref($this->getHref('Administration', $action['action']))
                ->setActive($this->request->getControllerActionName() === $action['action']);
            $menu->addMenuItem($item);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        if (is_array($this->pageInformation)) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation($this->pageInformation);
        }
    }

    /**
     * Returns the LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Create the panel of buttons
     */
    protected function createButtons()
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        // Filter and print Buttons
        if ($this->request->getControllerActionName() === 'index') {
            $toggleButton = $buttonBar->makeLinkButton()
                ->setHref('#')
                ->setDataAttributes([
                    'togglelink' => '1',
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                ])
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.filter.buttonTitle'))
                ->setIcon($this->iconFactory->getIcon('actions-filter', Icon::SIZE_SMALL));

            $printButton = $buttonBar->makeLinkButton()
                ->setHref('#')
                ->setDataAttributes([
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                ])
                ->setOnClick('window.print()')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.print.buttonTitle'))
                ->setIcon($this->iconFactory->getIcon('actions-file-csv', Icon::SIZE_SMALL));

            $buttonBar->addButton($toggleButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
            $buttonBar->addButton($printButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
        }

        // New Entry Button
        $buttons = [
            [
                'table' => 'tx_bwbookingmanager_domain_model_entry',
                'label' => 'flexforms_general.mode.entry_new',
                'action' => 'newEntry',
                'icon' => 'ext-bwbookingmanager-type-entry'
            ],
            [
                'table' => 'tx_bwbookingmanager_domain_model_blockslot',
                'label' => 'flexforms_general.mode.blockslot_new',
                'action' => 'newBlockslot',
                'icon' => 'ext-bwbookingmanager-type-blockslot'
            ]
        ];
        foreach ($buttons as $key => $tableConfiguration) {
            if ($this->getBackendUser()->isAdmin() || GeneralUtility::inList(
                    $this->getBackendUser()->groupData['tables_modify'],
                    $tableConfiguration['table']
                )
            ) {
                // @TODO repair translation
                //$title = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:' . $tableConfiguration['label']);
                $title = $this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:' . $tableConfiguration['label']);
                $viewButton = $buttonBar->makeLinkButton()
                    ->setHref($uriBuilder->reset()->setRequest($this->request)->uriFor(
                        $tableConfiguration['action'],
                        [],
                        'Administration'
                    ))
                    ->setDataAttributes([
                        'toggle' => 'tooltip',
                        'placement' => 'bottom',
                        'title' => $title
                    ])
                    ->setTitle($title)
                    ->setIcon($this->iconFactory->getIcon(
                        $tableConfiguration['icon'],
                        Icon::SIZE_SMALL,
                        'overlay-new'
                    ));
                $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
            }
        }

        // Refresh
        $path = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= VersionNumberUtility::convertVersionNumberToInteger('8.6') ? 'Resources/Private/Language/' : '';
        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/' . $path . 'locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    /**
     * Get backend user
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
