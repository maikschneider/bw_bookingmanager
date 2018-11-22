<?php

namespace Blueways\BwBookingmanager\Controller;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Creates the "Create Entries" wizard
 */
class CreateTimeslotsWizardModuleFunctionController extends \TYPO3\CMS\Backend\Module\AbstractFunctionModule
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     */
    protected $calendarRepository;

    /**
     * Complete tsConfig
     *
     * @var array
     */
    protected $tsConfig = [];

    /**
     * Part of tsConfig with TCEFORM.pages. settings
     *
     * @var array
     */
    protected $pagesTsConfig = [];

    /**
     * Main function creating the content for the module.
     *
     * @return string HTML content for the module, actually a "section" made through the parent object in $this->pObj
     */
    public function main()
    {
        $assigns = [];
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $this->calendarRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\CalendarRepository');
        $assigns['calendars'] = $this->calendarRepository->findAll();

        $pageRecord = BackendUtility::getRecord('pages', $this->pObj->id, 'uid',
            ' AND ' . $this->getBackendUser()->getPagePermsClause(8));
        if (is_array($pageRecord)) {
            $data = GeneralUtility::_GP('data');
            if (is_array($data['timeslot'])) {

                $timeslots = $this->createTimeslots($data);
                if(is_array($timeslots) && sizeof($timeslots)){

                    $timeslotRepository = $objectManager->get('Blueways\\BwBookingmanager\\Domain\\Repository\\TimeslotRepository');
                    $persistenceManager = $objectManager->get("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
                    foreach($timeslots as $timeslot){
                        $timeslotRepository->add($timeslot);
                    }
                    $persistenceManager->persistAll();

                    $assigns['timeslots'] = $timeslots;
                }
            }

        } else {
            $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, '',
                $this->getLanguageService()->getLL('wiz_crMany_errorMsg1'), FlashMessage::ERROR);
            /** @var $flashMessageService \TYPO3\CMS\Core\Messaging\FlashMessageService */
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            /** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
            $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $defaultFlashMessageQueue->enqueue($flashMessage);
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:bw_bookingmanager/Resources/Private/Templates/Wizard/CreateTimeslots.html'
        ));
        $view->assignMultiple($assigns);
        $out = $view->render();

        return $out;
    }

    protected function createTimeslots($data)
    {
        if(!is_array($data['timeslot']['days']) || !sizeof($data['timeslot']['days'])) return;

        $weekDays = array_keys($data['timeslot']['days']);
        $startDay = $data['timeslot']['startDate'] ? new \DateTime($data['timeslot']['startDate']) : new \DateTime('now');
        $dayOfWeek = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        $times = $data['timeslot']['times'];

        if(!$times) return;
        $times = preg_split('/\r\n|[\r\n]/', $times);
        if (!is_array($times) || !sizeof($times)) return;

        $timeslots = [];

        // init default values
        $length = $data['timeslot']['length'] ? $data['timeslot']['length'] : '10';
        $repeatType = $data['timeslot']['repeatType'] ? (int)$data['timeslot']['repeatType'] : \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY;
        $repeatEnd = $data['timeslot']['repeatEnd'] ? new \DateTime($data['timeslot']['repeatEnd']) : false;
        $maxWeight = $data['timeslot']['maxWeight'] ? (int)$data['timeslot']['maxWeight'] : 1;
        $hollidaySetting = $data['timeslot']['hollidaySetting'] ? (int)$data['timeslot']['hollidaySetting'] : 0;
        $isBookableHook = $data['timeslot']['is_bookable_hooks'] ? 1 : 0;
        $calendar = $this->calendarRepository->findByUid((int)$data['timeslot']['calendar']);
        if(!$calendar) return;
        $pid = $calendar->getPid();

        for($i=0; $i<7; $i++){
            if(in_array($i, $weekDays)){

                $newStartDay = clone $startDay;

                // does the new startday needs to be modified?
                if($startDay->format('N') != $i){
                    $newStartDay->modify('next '.$dayOfWeek[$i]);
                }

                foreach($times as $time){

                    $validTime = preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $time);
                    if(!$validTime) continue;

                    $startDate = new \DateTime($newStartDay->format('Y-m-d').' '.$time.':00');

                    $timeslot = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
                    $timeslot->setStartDate($startDate);
                    $endDate = clone $startDate;
                    $endDate->modify('+' . $length . ' minutes');
                    $timeslot->setEndDate($endDate);
                    $timeslot->addCalendar($calendar);
                    $timeslot->setRepeatType($repeatType);
                    if($repeatEnd){
                        $timeslot->setRepeatEnd($repeatEnd);
                    }
                    $timeslot->setPid($pid);
                    $timeslot->setMaxWeight($maxWeight);
                    $timeslot->setHolidaySetting($hollidaySetting);
                    $timeslot->setIsBookableHooks($isBookableHook);

                    $timeslots[] = $timeslot;
                }


            }
        }

        return $timeslots;

    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
