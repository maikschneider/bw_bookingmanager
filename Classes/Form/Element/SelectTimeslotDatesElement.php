<?php

namespace Blueways\BwBookingmanager\Form\Element;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use DateTime;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

class SelectTimeslotDatesElement extends AbstractFormElement
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setLayoutRootPaths(['EXT:bw_bookingmanager/Resources/Private/Layouts/Backend']);
        $this->templateView->setPartialRootPaths(['EXT:bw_bookingmanager/Resources/Private/Partials/Backend']);
        $this->templateView->setTemplatePathAndFilename('EXT:bw_bookingmanager/Resources/Private/Templates/Backend/TimeslotDatesElement.html');
    }

    /**
     * This will render the date and timeslot picker
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = [];
        $resultArray['requireJsModules'][] = 'TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement';

        $savedData = $this->getSavedData();

        $start = new DateTime();
        if ($this->data['defaultValues'] && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['startDate'])) {
            $start->setTimestamp($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['startDate']);
        }
        if ($this->data['databaseRow']['start_date']) {
            $start->setTimestamp($this->data['databaseRow']['start_date']);
        }
        $start = $start->format('Y-m-d\TH:i:s.v\Z');

        $entryEnd = null;
        if ($this->data['defaultValues'] && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['endDate'])) {
            $entryEnd = new DateTime();
            $entryEnd->setTimestamp($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['endDate']);
            $entryEnd = $entryEnd->format('Y-m-d\TH:i:s.v\Z');
        }
        if ($this->data['databaseRow']['end_date']) {
            $entryEnd = new DateTime();
            $entryEnd->setTimestamp($this->data['databaseRow']['end_date']);
            $entryEnd = $entryEnd->format('Y-m-d\TH:i:s.v\Z');
        }
        $timeslot = 0;
        if ($this->data['defaultValues'] && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['timeslot'])) {
            $timeslot = $this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['timeslot'];
        }
        if ($this->data['databaseRow']['timeslot']) {
            $timeslot = $this->data['databaseRow']['timeslot'];
        }
        $calendar = $this->data['databaseRow']['calendar'][0];
        $entryUid = $this->data['databaseRow']['uid'];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $calendarRepo = $objectManager->get(CalendarRepository::class);
        $calendars = $calendarRepo->findAllByPid($savedData['pid']);

        $viewState = new BackendCalendarViewState($savedData['pid']);
        $viewState->addCalendars($calendars);
        $viewState->start = $start;
        $viewState->entryStart = $start;
        $viewState->entryEnd = $entryEnd;
        $viewState->notBookableTimeslots = true;
        $viewState->futureEntries = $viewState->hasDirectBookingCalendar();
        $viewState->pastEntries = $viewState->hasDirectBookingCalendar();
        $viewState->timeslot = $timeslot;
        $viewState->calendar = $calendar;
        $viewState->entryUid = $entryUid;
        $viewState->addTypoScriptOptionOverrides();

        $this->templateView->assign('savedData', $savedData);
        $this->templateView->assign('viewState', json_encode($viewState));

        $resultArray['html'] = $this->templateView->render();

        return $resultArray;
    }

    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    private function getSavedData()
    {
        $row = $this->data['databaseRow'];

        $startDate = null;
        $endDate = null;
        $pid = 0;

        if ($this->data['defaultValues'] && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']) && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['startDate']) && isset($this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['endDate'])) {
            $startDate = $this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['startDate'];
            $endDate = $this->data['defaultValues']['tx_bwbookingmanager_domain_model_entry']['endDate'];
        }

        if ($row['start_date']) {
            $startDate = $row['start_date'];
        }
        if ($row['end_date']) {
            $endDate = $row['end_date'];
        }

        if (isset($row['pid']) && $row['pid']) {
            $pid = $row['pid'];
        }

        $savedData = [
            'entryUid' => $row['uid'],
            'calendar' => !empty($row['calendar']) ? (int)$row['calendar'][0] : null,
            'timeslot' => !empty($row['timeslot']) ? $row['timeslot'] : null,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pid' => $pid,
        ];

        return $savedData;
    }
}
