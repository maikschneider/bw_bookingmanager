<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class CalendarController extends ActionController
{
    protected CalendarRepository $calendarRepository;

    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function listAction(): ResponseInterface
    {
        $calendars = $this->calendarRepository->findAll();

        $this->view->assign('calendars', $calendars);

        return $this->htmlResponse();
    }
}
