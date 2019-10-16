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

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Wizard for rendering timeslot dates picker
 */
class TimeslotWizard extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var array
     */
    protected $queryParams = null;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var StandaloneView
     */
    private $templateView;

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
        $this->entryRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\EntryRepository');
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    }

    public function getConfiguration(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->isSignatureValid($request)) {

            $queryParams = json_decode($request->getQueryParams()['arguments'], true);
            $this->queryParams = $queryParams;

            $date = new DateTime();
            $date->setTimestamp($queryParams['now']);

            $viewData = [
                'currentCalendar' => $queryParams['calendar'],
                'currentTimeslot' => $queryParams['timeslot'],
                'currentStartDate' => $queryParams['startDate'],
                'currentEndDate' => $queryParams['endDate'],
                'currentStartEndTimestamp' => $queryParams['startDate'] . $queryParams['endDate'],
                'calendars' => $this->getCalendarsConfiguration($date),
                'css' => '/typo3conf/ext/bw_bookingmanager/Resources/Public/Css/TimeslotWizard.css'
            ];
            $content = $this->templateView->renderSection('Main', $viewData);
            $response->getBody()->write($content);

            return $response;
        }
        return $response->withStatus(403);
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

    private function getCalendarsConfiguration($date)
    {
        $date = $date ?: new \DateTime('now');

        $calendarsArray = [];
        $dateConf = new DateConf(0, $date);
        $calendars = $this->calendarRepository->findAllByPid($this->queryParams['pid']);

        foreach ($calendars as $key => $calendar) {

            $renderConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf, $calendar);
            $timeslots = $this->timeslotRepository->findInRange($calendar, $dateConf);
            $entries = $this->entryRepository->findInRange($calendar, $dateConf, false)->toArray();
            $renderConfiguration->setTimeslots($timeslots);
            $renderConfiguration->setEntries($entries);

            $calendarsArray[$key]['calendar'] = $calendar;
            $calendarsArray[$key]['monthView'] = $renderConfiguration->getRenderConfiguration();
            $calendarsArray[$key]['monthView']['prevMonth'] = $this->getWizardUriForNewDate($dateConf->prev);
            $calendarsArray[$key]['monthView']['nextMonth'] = $this->getWizardUriForNewDate($dateConf->next);
            //$calendarsArray[$key]['listView'] = $renderConfiguration->getConfigurationForDays(150);
        }

        return $calendarsArray;
    }

    private function getWizardUriForNewDate(\DateTime $date): string
    {
        $savedData = $this->queryParams;
        $savedData['now'] = $date->getTimestamp();
        return $this->getWizardUri($savedData);
    }

    /**
     * @param array $focusPoints
     * @param File $image
     * @return string
     */
    protected function getWizardUri(array $savedData): string
    {
        $routeName = 'ajax_wizard_timeslots';
        $uriArguments['arguments'] = json_encode($savedData);
        $uriArguments['signature'] = GeneralUtility::hmac($uriArguments['arguments'], $routeName);
        return (string)$this->uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }
}
