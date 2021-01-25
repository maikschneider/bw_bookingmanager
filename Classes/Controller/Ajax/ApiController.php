<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Utility\FullCalendarUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ApiController extends ActionController
{

    public function calendarShowAction(ServerRequestInterface $request, Response $response): Response
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $calendarUtil = $objectManager->get(FullCalendarUtility::class);
        $viewState = BackendCalendarViewState::createFromApiRequest($request);

        $events = $calendarUtil->getEvents($viewState);

        $response->getBody()->write(json_encode($events, JSON_THROW_ON_ERROR));
        return $response;
    }

    public function userSettingAction(ServerRequestInterface $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        if (isset($body['viewState'])) {
            $viewState = new BackendCalendarViewState((int)$body['viewState']['pid']);
            $viewState->overrideFromApiSave($body['viewState']);
            $viewState->persistInUserSettings();
        }

        return $response;
    }

}
