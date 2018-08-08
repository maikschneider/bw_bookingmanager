<?php
declare(strict_types=1);

namespace Blueways\BwBookingmanager\Controller\Ajax;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Wizard for rendering timeslot dates picker
 */
class TimeslotWizard extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var StandaloneView
     */
    private $templateView;

    /**
     * CalendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    /**
     * timeslotRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository = null;

    /**
     * @param StandaloneView $templateView
     */
    public function __construct(StandaloneView $templateView = null)
    {
        if (!$templateView) {
            $templateView = GeneralUtility::makeInstance(StandaloneView::class);
            $templateView->setLayoutRootPaths([GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Layouts/')]);
            $templateView->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Partials/')]);
            $templateView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Templates/Administration/TimeslotWizard.html'));
        }
        $this->templateView = $templateView;

        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->timeslotRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\TimeslotRepository');
        $this->calendarRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\CalendarRepository');
    }

    public function getConfiguration(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isSignatureValid($request)) {

            $queryParams = json_decode($request->getQueryParams()['arguments'], true);

            $viewData = [
                'currentCalendar' => $queryParams['calendar'],
                'currentTimeslot' => $queryParams['timeslot'],
                'currentStartDate' => $queryParams['startDate'],
                'currentEndDate' => $queryParams['endDate'],
                'calendars' => $this->getCalendarsConfiguration($queryParams['now']),
                'css' => '/typo3conf/ext/bw_bwg_base/Resources/Public/Css/TimeslotWizard.css'
            ];
            $content = $this->templateView->renderSection('Main', $viewData);
            $response->getBody()->write($content);

            return $response;
        }
        return $response->withStatus(403);

    }

    private function getCalendarsConfiguration($now)
    {
        $calendars = $this->calendarRepository->findAllIgnorePid();

        $calendarsArray = [];

        $startDate = new \DateTime();
        $startDate->setTimestamp($now);
        $startDate->setTime(0, 0, 0);
        $renderConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($startDate);

        foreach($calendars as $key => $calendar){

            $timeslots = $this->timeslotRepository->findInMonth($calendar, $startDate);
            $renderConfiguration->setTimeslots($timeslots);

            $calendarsArray[$key]['calendar'] = $calendar;
            $calendarsArray[$key]['monthView'] = $renderConfiguration->getConfigurationForMonth();
            $calendarsArray[$key]['listView'] = $renderConfiguration->getConfigurationForDays(150);

        }

        return $calendarsArray;
    }

    /**
     * Check if hmac signature is correct
     *
     * @param ServerRequestInterface $request the request with the GET parameters
     * @return bool
     */
    protected function isSignatureValid(ServerRequestInterface $request)
    {
        $token = GeneralUtility::hmac($request->getQueryParams()['arguments'], 'ajax_wizard_timeslots');
        return $token === $request->getQueryParams()['signature'];
    }
}
