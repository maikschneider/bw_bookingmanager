<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use Blueways\BwBookingmanager\Utility\FullCalendarUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ApiController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     */
    protected $feUserRepository;

    /**
     * @var array
     */
    protected $configuration = [
        'newEntry' => [
            '_exclude' => ['token', 'confirmed'],
            '_descend' => [
                'timeslot' => [],
                'calendar' => [],
                'endDate' => [],
                'startDate' => [],
                'displayStartDate' => [],
                'displayEndDate' => [],
            ],
        ],
        'feUser' => [
            '_exclude' => ['password']
        ]
    ];

    public function calendarShowAction(ServerRequestInterface $request, \TYPO3\CMS\Core\Http\Response $response)
    {
        $params = $request->getQueryParams();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $calendarUtil = $objectManager->get(FullCalendarUtility::class);
        $events = $calendarUtil->getEvents($params['pid'], $params['start'], $params['end']);

        $response->getBody()->write(json_encode($events, JSON_THROW_ON_ERROR));
        return $response;
    }

    public function injectCalendarRepository(
        \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository $calendarRepository
    ) {
        $this->calendarRepository = $calendarRepository;
    }

    public function injectFeUserRepository(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository $feUserRepository
    ) {
        $this->feUserRepository = $feUserRepository;
    }

    /**
     * @param $feUser
     */
    public function getFeUser(ServerRequestInterface $request, ResponseInterface $response)
    {
        $queryParameters = $request->getParsedBody();
        $feUserId = (int)$queryParameters['feUserId'];

        if ($feUserId) {
            $feUser = $this->feUserRepository->findByIdentifier($feUserId);
            $this->view->assign('feUser', $feUser);
        }

        $this->view->setConfiguration($this->configuration);
    }

    protected function isSignatureValid(ServerRequestInterface $request, $routeName)
    {
        $token = GeneralUtility::hmac($request->getQueryParams()['arguments'], $routeName);
        return $token === $request->getQueryParams()['signature'];
    }
}
