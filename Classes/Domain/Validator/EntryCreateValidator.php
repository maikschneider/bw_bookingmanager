<?php

namespace Blueways\BwBookingmanager\Domain\Validator;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Timeslot;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class EntryCreateValidator
 */
class EntryCreateValidator extends AbstractValidator
{
    /**
     * timeslot repository
     *
     * @var TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var Timeslot
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

        $this->validateAttributes();
        $this->validateDirectBooking();
        $this->validateTimeslotBooking();

        if (count($this->result->getErrors())) {
            return false;
        }
        return true;
    }

    private function validateAttributes()
    {
        if (!$this->entry->getName()) {
            $this->addError('No name given', 1526170536);
        }

        if (!$this->entry->getEmail()) {
            $this->addError('No email given', 1526170536);
        }

        if (!$this->entry->getStartDate()) {
            $this->addError('No start date given', 1571761489);
        }

        if (!$this->entry->getEndDate()) {
            $this->addError('No end date given', 1571761514);
        }
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

        // skip if no start or end date
        if (!$this->entry->getStartDate() || !$this->entry->getEndDate()) {
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
        $this->timeslot_startDate = $this->timeslot->getStartDate()->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $this->timeslot_endDate = $this->timeslot->getEndDate()->setTimezone(new \DateTimeZone('Europe/Berlin'));

        $this->entry_startDate = $this->entry->getStartDate()->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $this->entry_endDate = $this->entry->getEndDate()->setTimezone(new \DateTimeZone('Europe/Berlin'));

        $this->validateDates();
        $this->validateWeight();
    }

    private function validateDates()
    {
        $this->validateFuture();
        $this->validateTimes();

        switch ($this->timeslot->getRepeatType()) {
            case Timeslot::REPEAT_WEEKLY:
                $this->validateWeeklyRepeatDates();
                break;
            case Timeslot::REPEAT_MONTHLY:
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

    public function injectTimeslotRepository(
        TimeslotRepository $timeslotRepository
    ) {
        $this->timeslotRepository = $timeslotRepository;
    }
}
