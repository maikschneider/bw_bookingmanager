<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Blueways\BwBookingmanager\Service\AccessControlService;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Calendar Controller for list, show view of calendar entries
 * PHP version 7.2
 *
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https: //opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http: //www.blueways.de
 */
class CalendarController extends ActionController
{
    /**
     * CalendarRepository
     *
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * timeslotRepository
     *
     * @var TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var AccessControlService
     */
    protected $accessControlService;

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    public function initializeAction()
    {
        $this->entryRepository = $this->objectManager->get(EntryRepository::class);
        $this->timeslotRepository = $this->objectManager->get(TimeslotRepository::class);
        $this->calendarRepository = $this->objectManager->get(CalendarRepository::class);
    }

    /**
     * @throws StopActionException
     */
    public function listAction(): ResponseInterface
    {
        if ((int)$this->settings['mode'] === 1) {
            return new ForwardResponse('show');
        }

        if ((int)$this->settings['mode'] === 2) {
            return (new ForwardResponse('new'))->withControllerName('Entry');
        }

        if ((int)$this->settings['mode'] === 3) {
            return (new ForwardResponse('list'))->withControllerName('Entry');
        }

        $calendars = $this->calendarRepository->findAll();

        $this->view->setTemplate($this->settings['template']['calendar']['list']);

        $this->view->assign('calendars', $calendars);
        return $this->htmlResponse();
    }

    /**
     * @param Calendar|null $calendar
     * @throws NoSuchArgumentException
     * @throws InvalidQueryException
     */
    public function showAction(
        Calendar $calendar = null
    ): ResponseInterface {
        // check for fe_user
        $feUser = false;
        if ($this->accessControlService->hasLoggedInFrontendUser()) {
            $feUser = $this->frontendUserRepository->findByIdentifier($this->accessControlService->getFrontendUserUid());
        }

        // create date from arguments and configuration
        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);
        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        }
        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        // build render configuration
        /** @var Calendar $calendar */
        $calendar = $calendar ?: $this->calendarRepository->findByUid((int)$this->settings['calendarPid']);
        $calendarManager = $this->objectManager->get(CalendarManagerUtility::class, $calendar);
        $configuration = $calendarManager->getConfiguration($dateConf);

        $this->view->setTemplate($this->settings['template']['calendar']['show']);
        $this->view->assignMultiple([
            'calendar' => $calendar,
            'configuration' => $configuration,
            'feUser' => $feUser,
        ]);
        return $this->htmlResponse();
    }

    public function injectCalendarRepository(CalendarRepository $calendarRepository): void
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectTimeslotRepository(TimeslotRepository $timeslotRepository): void
    {
        $this->timeslotRepository = $timeslotRepository;
    }

    public function injectAccessControlService(AccessControlService $accessControlService): void
    {
        $this->accessControlService = $accessControlService;
    }

    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository): void
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }
}
