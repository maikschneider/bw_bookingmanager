<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Model\Dto\FrontendCalendarViewState;
use Blueways\BwBookingmanager\Utility\FullCalendarUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ApiV2Controller extends ActionController
{
    public function calendarShowAction(Calendar $calendar): ResponseInterface
    {
        $calendarUtil = GeneralUtility::makeInstance(FullCalendarUtility::class);
        $viewState = FrontendCalendarViewState::createFromApiRequest($this->request);
        $viewState->calendar = $calendar->getUid();

        $events = $calendarUtil->getEventsForFrontend($viewState);

        return $this->jsonResponse(json_encode($events, JSON_THROW_ON_ERROR));
    }
}
