<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Http\HtmlResponse;
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
    protected $moduleTemplate;

    /**
     * @var ViewInterface
     */
    protected $view;

    public function calendarAction(\Psr\Http\Message\ServerRequestInterface $request): ResponseInterface
    {
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->initializeView('calendar');

        $params = $request->getQueryParams();
        $pid = (int)$params['id'];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $calendars = $calendarRepository->findAllByPid($pid);

        $events = [];
        $events['extraParams'] = [];
        $events['extraParams']['pid'] = $pid;

        $this->view->assign('events', json_encode($events, JSON_THROW_ON_ERROR));
        $this->view->assign('calendars', $calendars);

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * Initializes the view by setting the templateName
     *
     * @param string $templateName
     */
    protected function initializeView(string $templateName)
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:bw_bookingmanager/Resources/Private/Templates/Backend']);
        $this->view->setPartialRootPaths(['EXT:bw_bookingmanager/Resources/Private/Partials/Backend']);
        $this->view->setLayoutRootPaths(['EXT:bw_bookingmanager/Resources/Private/Layouts/Backend']);
    }
}
