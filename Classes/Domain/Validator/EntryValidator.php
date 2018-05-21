<?php
namespace Blueways\BwBookingmanager\Domain\Validator;

class EntryValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
    * timeslot repository
    *
    * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
    * @inject
    */
    protected $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Entry
     */
    protected $entry;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot
     */
    protected $timeslot;

    public function isValid($entry)
    {
        $this->entry = $entry;
        $this->timeslot = $entry->getTimeslot();

        $this->validateDates();
        $this->validateWeight();
        $this->validateHooks();
        
        if(sizeof($this->result->getErrors())){
            return FALSE;
        }
        return TRUE;
    }

    private function validateDates()
    {
        $this->validateFuture();
        $this->validateTimes();
        

        switch($this->timeslot->getRepeatType()){
            case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY:
                $this->validateWeeklyRepeatDates();
            break;
            case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY:
                $this->validateMonthlyRepeatDates();
            break;
        }
    }

    /**
     * start time and end time always have to match, no matter what kind of repeat
     */
    private function validateTimes()
    {
        // start time
        if($this->timeslot->getStartDate()->format('H:i:s') != $this->entry->getStartDate()->format('H:i:s'))
        {
            $this->addError('Start time is not possible', 1526170536);
        }
        // end time
        if($this->timeslot->getEndDate()->format('H:i:s') != $this->entry->getEndDate()->format('H:i:s'))
        {
            $this->addError('End time is not possible', 1526170536);
        }
    }

    private function validateFuture()
    {
        if($this->timeslot->getStartDate() > $this->entry->getStartDate()){
            $this->addError('Selected start date is in past', 1526170536);
        }
        if($this->timeslot->getEndDate() > $this->entry->getEndDate()){
            $this->addError('Selected end date is in past', 1526170536);
        }
    }

    private function validateWeeklyRepeatDates()
    {
        if($this->timeslot->getStartDate()->format('w') != $this->entry->getStartDate()->format('w')){
            $this->addError('Selected start date is not the correct day of week', 1526170536);
        }
        if($this->timeslot->getEndDate()->format('w') != $this->entry->getEndDate()->format('w')){
            $this->addError('Selected end date is not the correct day of week', 1526170536);
        }
    }

    private function validateMonthlyRepeatDates()
    {
        if($this->timeslot->getStartDate()->format('j') != $this->entry->getStartDate()->format('j')){
            $this->addError('Selected start date is not the correct day of month', 1526170536);
        }
        if($this->timeslot->getEndDate()->format('j') != $this->entry->getEndDate()->format('j')){
            $this->addError('Selected end date is not the correct day of month', 1526170536);
        }   
    }

    private function validateWeight()
    {
        $bookedWeight = 0;
        foreach ($this->timeslot->getEntries() as $entry) {
            if($entry->getStartDate() == $this->entry->getStartDate() && $entry->getEndDate() == $this->entry->getEndDate()){
                $bookedWeight += $entry->getWeight();
            }
        }
        if($this->timeslot->getMaxWeight() < ($bookedWeight + $this->entry->getWeight())){
            $this->addError('Selected timeslot has not enough free space or is booked out', 1526170536);
        }
    }

    private function validateHooks()
    {
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/entry']['validation'] ?? [] as $className) {
            $_procObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
            $_procObj->executeHook($this);
        }
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function addValidationHookError($message, $timestap)
    {
        $this->addError($message, $timestap);
    }
}