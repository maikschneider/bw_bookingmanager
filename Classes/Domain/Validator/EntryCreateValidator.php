<?php

namespace Blueways\BwBookingmanager\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class EntryCreateValidator
 *
 * @package Blueways\BwBookingmanager\Domain\Validator
 */
class EntryCreateValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
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

    /**
     * @var \DateTime
     */
    protected $timeslot_startDate;

    /**
     * @var \DateTime
     */
    protected $timeslot_endDate;

    /**
     * @var \DateTime
     */
    protected $entry_startDate;

    /**
     * @var \DateTime
     */
    protected $entry_endDate;

    /**
     * @param mixed $entry
     * @return bool
     */
    public function isValid($entry)
    {
        $this->entry = clone $entry;

        if (!$this->entry->getTimeslot() && !$this->entry->getCalendar()->isDirectBooking()) {
            $this->addError('Direct booking is not allowed', 1526170536);
        }

        $this->validateDirectBooking();
        $this->validateTimeslotBooking();

        if (sizeof($this->result->getErrors())) {
            return false;
        }
        return true;
    }

    /**
     * Validate if calendar is bookable in the specified time
     */
    private function validateDirectBooking()
    {
        // skip if direct booking is not enabled
        if (!$this->entry->getCalendar()->isDirectBooking()) {
            return;
        }

        $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $typoscript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        $minLength = (int)$typoscript['plugin.']['tx_bwbookingmanager.']['settings.']['directBooking.']['minLength'];
        $minLength = $minLength >= 0 ? $minLength : 0;

        if ($minLength > 0 && $this->entry->getStartDate()->diff($this->entry->getEndDate())->m < $minLength) {
            $this->addError('Duration of booking is too short', 1526170537);
        }

        $timeOffset = (int)$typoscript['plugin.']['tx_bwbookingmanager.']['settings.']['directBooking.']['timeBetween'];
        $timeOffset = $timeOffset >= 0 ? $timeOffset : 0;

        $startTime = clone $this->entry->getStartDate();
        $startTime->modify('-' . $timeOffset . ' minutes');
        $endTime = clone $this->entry->getEndDate();
        $endTime->modify('-' . $timeOffset . ' minutes');

        foreach ($this->entry->getCalendar()->getEntries() as $entry) {
            if ($entry->getEndDate() > $startTime && $entry->getStartDate() < $endTime) {
                $this->addError('Selected time is not bookable due to overlapping', 1526170536);
                break;
            }
        }
    }

    private function validateTimeslotBooking()
    {
        // skip if no timeslot is attached
        if (!$this->entry->getTimeslot()) {
            return;
        }

        $this->timeslot = clone $this->entry->getTimeslot();

        // timezone fix
        $this->timeslot_startDate = $this->timeslot->getStartDate()->setTimezone(new \DateTimeZone('UTC'));
        $this->timeslot_endDate = $this->timeslot->getEndDate()->setTimezone(new \DateTimeZone('UTC'));

        $this->entry_startDate = $this->entry->getStartDate()->setTimezone(new \DateTimeZone('UTC'));
        $this->entry_endDate = $this->entry->getEndDate()->setTimezone(new \DateTimeZone('UTC'));

        // DST fix
        $timezone = new \DateTimeZone('Europe/Berlin');
        $transitions = $timezone->getTransitions($this->timeslot_startDate->getTimestamp(),
            $this->entry_startDate->getTimestamp());
        $lastTransitionIndex = sizeof($transitions) - 1;
        if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
            $this->timeslot_startDate->modify('+1 hour');
            $this->timeslot_endDate->modify('+1 hour');
        }
        if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
            $this->timeslot_startDate->modify('-1 hour');
            $this->timeslot_endDate->modify('-1 hour');
        }

        $this->validateDates();
        $this->validateWeight();

        // @Todo: strange bug: If i do not decrease the date by one hour after
        // validation, the skater timeslot of 12 o'clock (not the 10 o'clock!?) gets updated to the +1 hour format
        // -> so i decrease the hour again...
        if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
            $this->timeslot_startDate->modify('-1 hour');
            $this->timeslot_endDate->modify('-1 hour');
        }
        if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
            $this->timeslot_startDate->modify('+1 hour');
            $this->timeslot_endDate->modify('+1 hour');
        }
    }

    private function validateDates()
    {
        $this->validateFuture();
        $this->validateTimes();

        switch ($this->timeslot->getRepeatType()) {
            case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY:
                $this->validateWeeklyRepeatDates();
                break;
            case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_MONTHLY:
                $this->validateMonthlyRepeatDates();
                break;
        }
    }

    private function validateFuture()
    {
        if ($this->timeslot_startDate > $this->entry_startDate) {
            $this->addError('Selected start date is in past', 1526170536);
        }
        if ($this->timeslot_endDate > $this->entry_endDate) {
            $this->addError('Selected end date is in past', 1526170536);
        }
    }

    /**
     * start time and end time always have to match, no matter what kind of repeat
     */
    private function validateTimes()
    {
        // start time
        if ($this->timeslot_startDate->format('H:i:s') != $this->entry_startDate->format('H:i:s')) {
            $this->addError('Start time is not possible', 1526170536);
        }
        // end time
        if ($this->timeslot_endDate->format('H:i:s') != $this->entry_endDate->format('H:i:s')) {
            $this->addError('End time is not possible', 1526170536);
        }
    }

    private function validateWeeklyRepeatDates()
    {
        if ($this->timeslot_startDate->format('w') != $this->entry_startDate->format('w')) {
            $this->addError('Selected start date is not the correct day of week', 1526170536);
        }
        if ($this->timeslot_endDate->format('w') != $this->entry_endDate->format('w')) {
            $this->addError('Selected end date is not the correct day of week', 1526170536);
        }
    }

    private function validateMonthlyRepeatDates()
    {
        if ($this->timeslot_startDate->format('j') != $this->entry_startDate->format('j')) {
            $this->addError('Selected start date is not the correct day of month', 1526170536);
        }
        if ($this->timeslot_endDate->format('j') != $this->entry_endDate->format('j')) {
            $this->addError('Selected end date is not the correct day of month', 1526170536);
        }
    }

    private function validateWeight()
    {
        $bookedWeight = 0;
        foreach ($this->timeslot->getEntries() as $entry) {
            if ($entry->getStartDate() == $this->entry_startDate && $entry->getEndDate() == $this->entry_endDate) {
                $bookedWeight += $entry->getWeight();
            }
        }
        if ($this->timeslot->getMaxWeight() < ($bookedWeight + $this->entry->getWeight())) {
            $this->addError('Selected timeslot has not enough free space or is booked out', 1526170536);
        }
    }
}
