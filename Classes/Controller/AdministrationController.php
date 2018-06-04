<?php
namespace Blueways\BwBookingmanager\Controller;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Lang\LanguageService;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

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
            $dateFormat = ($GLOBALS['TYPO3_CONF_VARS']['SYS']['USdateFormat'] ? ['MM-DD-YYYY', 'HH:mm MM-DD-YYYY'] : ['DD-MM-YYYY', 'HH:mm DD-MM-YYYY']);
            $pageRenderer->addInlineSetting('DateTimePicker', 'DateFormat', $dateFormat);
            
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/Modal');
        }

        $this->createMenu();
        // $this->createButtons();
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
            ['action' => 'timeslot', 'label' => 'timeslotListing'],
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

    public function indexAction(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar = null
    ) {
        $calendars = $this->calendarRepository->findAll();

        // use first calendar as default
        if (!$calendar && count($calendars)) {
            $calendar = $calendars->getFirst();
        }

        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;

        $endDay = $this->request->hasArgument('endDay') ? $this->request->getArgument('endDay') : null;
        $endMonth = $this->request->hasArgument('endMonth') ? $this->request->getArgument('endMonth') : null;
        $endYear = $this->request->hasArgument('endYear') ? $this->request->getArgument('endYear') : null;

        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        }

        $endDate = clone $startDate;
        $endDate->modify('+1 month');
        $endDate->setTime(23, 59, 59);
        if ($endDay && $endMonth && $endYear) {
            $endDate = $endDate->createFromFormat('j-n-Y H:i:s', $endDay . '-' . $endMonth . '-' . $endYear . ' 00:00:00');
        }
        
        $this->view->assign('calendar', $calendar);
        $this->view->assign('calendars', $calendars);
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
