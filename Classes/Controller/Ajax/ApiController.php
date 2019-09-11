<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Utility\CalendarManagerUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;

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

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \Exception
     */
    public function calendarShowAction(Calendar $calendar)
    {
        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);

        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        $calendarManager = $this->objectManager->get(CalendarManagerUtility::class, $calendar);
        $configuration = $calendarManager->getConfiguration($dateConf);

        $this->view->assignMultiple([
            'configuration' => $configuration,
            'calendar' => $calendar
        ]);

        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(array('configuration', 'calendar'));
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
