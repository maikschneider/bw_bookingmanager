<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class Chart1Controller extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * calendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    /**
     * @param ViewInterface $view
     */
    public function __construct(StandaloneView $templateView = null)
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->calendarRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\CalendarRepository');
    }

    public function getConfiguration(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isSignatureValid($request)) {


            $calendars = $this->calendarRepository->findAllIgnorePid();

            $chartHelper = new \Blueways\BwBookingmanager\Helper\DashboardCharts($calendars);
            $charts = $chartHelper->getOnloadCharts();

            $response->getBody()->write(json_encode($charts));

            return $response;
        }

        return $response->withStatus(403);
    }

    protected function isSignatureValid(ServerRequestInterface $request)
    {
        $token = GeneralUtility::hmac($request->getQueryParams()['arguments'], 'ajax_dashboard_charts');
        return $token === $request->getQueryParams()['signature'];
    }
}
