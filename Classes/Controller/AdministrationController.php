<?php
namespace Blueways\BwBookingmanager\Controller;

/**
 * Administration controller for TYPO3 Backend module
 *
 * PHP version 7.2
 *
 * @package  BwBookingManager
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https://opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http://www.blueways.de
 */

use Blueways\BwBookingmanager\Domain\Model\Dto\AdministrationDemand;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @inject
     */
    protected $entryRepository = null;

    /**
     * calendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
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
        $this->pageUid = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id');

        parent::initializeAction();
    }

    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        parent::initializeView($view);

        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        if ($view instanceof BackendTemplateView) {
            $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/DateTimePicker');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/News/AdministrationModule');
        }

        $this->createMenu();
        $this->createButtons();
    }

    /**
     * Create menu
     *
     */
    protected function createMenu()
    {
        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('bw_bookingmanager');

        $actions = [
            ['action' => 'index', 'label' => 'entryListing'],
            // ['action' => 'timeslot', 'label' => 'timeslotListing'],
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:module.' . $action['label']))
                ->setHref($this->getHref('Administration', $action))
                ->setActive($this->request->getControllerActionName() === $action['action']);
            $menu->addMenuItem($item);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        if (is_array($this->pageInformation)) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation($this->pageInformation);
        }
    }

    /**
     * Create the panel of buttons
     *
     */
    protected function createButtons()
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        if ($this->request->getControllerActionName() === 'index') {
            $toggleButton = $buttonBar->makeLinkButton()
                ->setHref('#')
                ->setDataAttributes([
                    'togglelink' => '1',
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                ])
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:administration.toggleForm'))
                ->setIcon($this->iconFactory->getIcon('actions-filter', Icon::SIZE_SMALL));
            $buttonBar->addButton($toggleButton, ButtonBar::BUTTON_POSITION_LEFT, 1);
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
        if (!$calendar && count($calendars)) {
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
        $token = FormProtectionFactory::get()->generateToken('moduleCall', 'web_BwBookingmanagerTxBookingmanagerM1');
        if ($tokenOnly) {
            return $token;
        } else {
            return '&moduleToken=' . $token;
        }
    }

    public function timeslotAction()
    {
        $calendars = $this->calendarRepository->findAll();
        $this->view->assign('calendars', $calendars);
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
}
