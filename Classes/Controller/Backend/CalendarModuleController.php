<?php

declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Backend;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CalendarModuleController extends AbstractModuleController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->initializeExtbaseController('calendar', $request);

        $calendars = $this->calendarRepository->findAllByPid($this->pid);

        $viewState = BackendCalendarViewState::getFromUserSettings($this->pid);
        $viewState->addCalendars($calendars);
        $viewState->addTypoScriptOptionOverrides();

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Backend/ContextMenu');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/BwBookingmanager/BackendCalendarContextMenuActions');

        $this->view->assign('calendars', $calendars);
        $this->view->assign('viewState', json_encode($viewState, JSON_THROW_ON_ERROR));

        // save selected route
        $moduleDataIdentifier = 'bwbookingmanager/selectedRoute-' . $this->pid;
        $GLOBALS['BE_USER']->pushModuleData($moduleDataIdentifier, 1);

        $this->moduleTemplate->setContent($this->view->render());
        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($this->moduleTemplate->renderContent());
        return $response;
    }
}
