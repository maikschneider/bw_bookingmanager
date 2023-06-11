<?php

declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Backend;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Utility\FullCalendarUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ApiController extends ActionController
{
    public function calendarShowAction(ServerRequestInterface $request): ResponseInterface
    {
        $calendarUtil = GeneralUtility::makeInstance(FullCalendarUtility::class);
        $viewState = BackendCalendarViewState::createFromApiRequest($request);

        $events = $calendarUtil->getEventsForBackend($viewState);

        return $this->jsonResponse(json_encode($events, JSON_THROW_ON_ERROR));
    }

    public function userSettingAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        if (isset($body['viewState'])) {
            $viewState = new BackendCalendarViewState((int)$body['viewState']['pid']);
            $viewState->overrideFromApiSave($body['viewState']);
            $viewState->persistInUserSettings();
        }

        return $this->jsonResponse('{}');
    }
}
