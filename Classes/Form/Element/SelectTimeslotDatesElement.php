<?php

namespace Blueways\BwBookingmanager\Form\Element;

use DateTime;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
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
        $resultArray['requireJsModules'][] = 'TYPO3/CMS/BwBookingmanager/BackendCalendar';

        $savedData = $this->getSavedData();
        $language = $this->getLanguageService()->lang;
        $start = new \DateTime();
        $start->setTimestamp($savedData['startDate']);
        $viewState = [
            'pid' => $savedData['pid'],
            'language' => $language,
            'start' => $start->format(DateTime::ATOM),
            'notBookableTimeslots' => 'true',
            'futureEntries' => 'false'
        ];

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
            'calendar' => !empty($row['calendar']) ? $row['calendar'][0] : null,
            'timeslot' => !empty($row['timeslot']) ? $row['timeslot'] : null,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pid' => $pid
        ];

        return $savedData;
    }

}
