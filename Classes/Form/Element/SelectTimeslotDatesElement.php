<?php
namespace Blueways\BwBookingmanager\Form\Element;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class SelectTimeslotDatesElement extends AbstractFormElement
{
    /**
     * @var StandaloneView
     */
    protected $templateView;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
        // Would be great, if we could inject the view here, but since the constructor is in the interface, we can't
        $this->templateView = GeneralUtility::makeInstance(StandaloneView::class);
        $this->templateView->setLayoutRootPaths([GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Layouts/')]);
        $this->templateView->setPartialRootPaths([GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Partials/')]);
        $this->templateView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:bw_bookingmanager/Resources/Private/Templates/Administration/TimeslotDatesElement.html'));
        $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    }

    /**
     * Default field wizards enabled for this element.
     *
     * @var array
     */
    protected $defaultFieldWizard = [
        'localizationStateSelector' => [
            'renderType' => 'localizationStateSelector',
        ],
        'otherLanguageContent' => [
            'renderType' => 'otherLanguageContent',
            'after' => [
                'localizationStateSelector'
            ],
        ],
        'defaultLanguageDifferences' => [
            'renderType' => 'defaultLanguageDifferences',
            'after' => [
                'otherLanguageContent',
            ],
        ],
    ];

    /**
     * This will render the date and timeslot picker
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();

        $savedData = $this->getSavedData();

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        $fieldControlResult = $this->renderFieldControl();
        $fieldControlHtml = $fieldControlResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldControlResult, false);

        $fieldWizardResult = $this->renderFieldWizard();
        $fieldWizardHtml = $fieldWizardResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

        $resultArray['requireJsModules'][] = [
            'TYPO3/CMS/BwBookingmanager/TimeslotDatesSelect' => 'function(TimeslotDatesSelect){top.require(["jquery-ui/draggable", "jquery-ui/resizable"], function() { TimeslotDatesSelect.initializeTrigger(); }); }',
        ];

        $arguments = [
            'fieldInformation' => $fieldInformationHtml,
            'fieldControl' => $fieldControlHtml,
            'fieldWizard' => $fieldWizardHtml,
            'savedData' => $savedData,
            'wizardUri' => $this->getWizardUri($savedData),
        ];

        $this->templateView->assignMultiple($arguments);
        $resultArray['html'] = $this->templateView->render();

        return $resultArray;
    }

    private function getSavedData()
    {
        $row = $this->data['databaseRow'];
        
        $startDate = null;
        if($row['start_date']) {
            $startDate = new \DateTime(date('Y-m-d H:i:sP', $row['start_date']));
            $startDate = $startDate->getTimestamp();
        }
        $endDate = null;
        if ($row['end_date']) {
            $endDate = new \DateTime(date('Y-m-d H:i:sP', $row['end_date']));
            $endDate = $endDate->getTimestamp();
        }

        $now = new \DateTime('now');
        $now = $now->getTimestamp();

        $savedData = [
            'calendar' => !empty($row['calendar']) ? $row['calendar'][0] : null,
            'timeslot' => !empty($row['timeslot']) ? $row['timeslot'] : null,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'now' => $now
        ];

        return $savedData;
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
