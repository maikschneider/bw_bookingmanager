<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class ChartsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * calendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    /**
     * entryRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository = null;

    /**
     * @param ViewInterface $view
     */
    public function __construct(StandaloneView $templateView = null)
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->calendarRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\CalendarRepository');
        $this->entryRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\EntryRepository');
    }

    public function getChart1(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isSignatureValid($request, 'ajax_dashboard_chart1')) {

            $queryParams = json_decode($request->getQueryParams()['arguments'], true);

            // default settings
            $startDate = $queryParams['startDate'] ? date_create_from_format('d-m-Y', $queryParams['startDate']) : new \DateTime('now');
            $view = $queryParams['view'] ? $queryParams['view'] : 'month';


            if ($view === 'year') {
                $startDate->modify('first day of january this year');
                $endDate = clone $startDate;
                $endDate->modify('last day of december this year');
                $prevDate = clone $startDate;
                $prevDate->modify('-1 year');
                $nextDate = clone $endDate;
                $nextDate->modify('+1 day');
            }

            if ($view === 'month') {
                $startDate->modify('first day of this month');
                $endDate = clone $startDate;
                $endDate->modify('last day of this month');
                $prevDate = clone $startDate;
                $prevDate->modify('-1 month');
                $nextDate = clone $endDate;
                $nextDate->modify('+1 days');
            }

            // get data from repos
            $calendars = $this->calendarRepository->findAllIgnorePid();
            $entries = $this->entryRepository->findAllInRange($startDate, $endDate);

            // get ctx configuration for chart
            $chartHelper = new \Blueways\BwBookingmanager\Helper\DashboardCharts($calendars, $entries, $startDate, $view);
            $charts = $chartHelper->getChart1();

            $ajax = [
                'charts' => $charts,
                'prevLink' => $chartHelper->getDashboardChartUri('ajax_dashboard_chart1', ['view' => $view, 'startDate' => $prevDate->format('d-m-Y')]),
                'nextLink' => $chartHelper->getDashboardChartUri('ajax_dashboard_chart1', ['view' => $view, 'startDate' => $nextDate->format('d-m-Y')]),
            ];

            //
            $response->getBody()->write(json_encode($ajax));

            return $response;
        }

        return $response->withStatus(403);
    }

    protected function isSignatureValid(ServerRequestInterface $request, $routeName)
    {
        $token = GeneralUtility::hmac($request->getQueryParams()['arguments'], $routeName);
        return $token === $request->getQueryParams()['signature'];
    }
}
