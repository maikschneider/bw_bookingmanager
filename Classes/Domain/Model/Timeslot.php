<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use Blueways\BwBookingmanager\Utility\IcsUtility;
use DateTime;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * Timeslot
 */
class Timeslot extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    const REPEAT_NO = 0;
    const REPEAT_DAILY = 1;
    const REPEAT_WEEKLY = 2;
    const REPEAT_MONTHLY = 3;
    const REPEAT_MULTIPLE_WEEKLY = 4;

    const HOLIDAY_NO_EFFECT = 0;
    const HOLIDAY_NOT_DURING = 1;
    const HOLIDAY_ONLY_DURING = 2;

    /**
     * startDate
     *
     * @var \DateTime
     */
    protected $startDate;

    /**
     * endDate
     *
     * @var \DateTime
     */
    protected $endDate;

    /**
     * repeatType
     *
     * @var int
     */
    protected $repeatType = Timeslot::REPEAT_NO;

    /**
     * holidaySetting
     *
     * @var int
     */
    protected $holidaySetting = Timeslot::HOLIDAY_NO_EFFECT;

    /**
     * maxWeight
     *
     * @var int
     */
    protected $maxWeight = 0;

    /**
     * entries
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry>
     * @lazy
     */
    protected $entries = null;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar;

    /**
     * @return \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    public function setCalendar(Calendar $calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * repeatEnd
     *
     * @var \DateTime
     */
    protected $repeatEnd;

    /**
     * isBookableHooks
     *
     * @var int
     */
    protected $isBookableHooks = 0;

    /**
     * @var int
     */
    protected $repeatDays = 0;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->entries = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return int
     */
    public function getHolidaySetting(): int
    {
        return $this->holidaySetting;
    }

    /**
     * @param int $holidaySetting
     */
    public function setHolidaySetting(int $holidaySetting): void
    {
        $this->holidaySetting = $holidaySetting;
    }

    /**
     * Returns the repeatType
     *
     * @return int $repeatType
     */
    public function getRepeatType()
    {
        return $this->repeatType;
    }

    /**
     * Sets the repeatType
     *
     * @param int $repeatType
     * @return void
     */
    public function setRepeatType($repeatType)
    {
        $this->repeatType = $repeatType;
    }

    /**
     * Returns the maxWeight
     *
     * @return int $maxWeight
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Sets the maxWeight
     *
     * @param int $maxWeight
     * @return void
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * e.g. 73 => [1,0,0,1,0,0,1] => [0,3,6] (Su, We, Sa)
     *
     * @return array
     */
    public function getRepeatDaysSelectedWeekDays()
    {
        $selectedDays = [];

        foreach (array_reverse(str_split(decbin($this->getRepeatDays()))) as $key => $value) {
            if ($value === '1') {
                $selectedDays[] = $key;
            }
        }

        return $selectedDays;
    }

    /**
     * @return int
     */
    public function getRepeatDays(): int
    {
        return $this->repeatDays;
    }

    /**
     * @param int $repeatDays
     */
    public function setRepeatDays(int $repeatDays)
    {
        $this->repeatDays = $repeatDays;
    }

    /**
     * Adds a Entry
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @return void
     */
    public function addEntry(\Blueways\BwBookingmanager\Domain\Model\Entry $entry)
    {
        $this->entries->attach($entry);
    }

    /**
     * Removes a Entry
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entryToRemove The Entry to be removed
     * @return void
     */
    public function removeEntry(\Blueways\BwBookingmanager\Domain\Model\Entry $entryToRemove)
    {
        $this->entries->detach($entryToRemove);
    }

    /**
     * Returns the repeatEnd
     *
     * @return \DateTime $repeatEnd
     */
    public function getRepeatEnd()
    {
        return $this->repeatEnd;
    }

    /**
     * Sets the repeatEnd
     *
     * @param \DateTime $repeatEnd
     * @return void
     */
    public function setRepeatEnd(\DateTime $repeatEnd)
    {
        $this->repeatEnd = $repeatEnd;
    }

    public function getStartEndTimestamp()
    {
        return $this->getStartDate()->getTimestamp() . '' . $this->getEndDate()->getTimestamp();
    }

    /**
     * Returns the startDate
     *
     * @return \DateTime $startDate
     */
    public function getStartDate()
    {
        $now = new \DateTime();
        $this->startDate->setTimezone($now->getTimezone());
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param \DateTime $startDate
     * @return void
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the endDate
     *
     * @return \DateTime $endDate
     */
    public function getEndDate()
    {
        $now = new \DateTime();
        $this->endDate->setTimezone($now->getTimezone());
        return $this->endDate;
    }

    /**
     * Sets the endDate
     *
     * @param \DateTime $endDate
     * @return void
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function getApiOutput()
    {
        $feUsers = [];
        $entries = [];

        foreach ($this->getEntries() as $entry) {
            if ($entry->getFeUser()) {
                $feUsers[] = $entry->getFeUser()->getUid();
            }

            $entries[] = $entry->getUid();
        }

        return [
            'uid' => $this->uid,
            'startDate' => $this->startDate->getTimestamp(),
            'displayStartDate' => $this->getDisplayStartDate(),
            'endDate' => $this->endDate->getTimestamp(),
            'displayEndDate' => $this->getDisplayEndDate(),
            'maxWeight' => $this->maxWeight,
            'isBookable' => $this->getIsBookable(),
            'freeWeight' => $this->getFreeWeight(),
            'bookedWeight' => $this->getBookedWeight(),
            'feUsers' => $feUsers,
            'entries' => $entries
        ];
    }

    /**
     * Returns the entries
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry> $entries
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Sets the entries
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry> $entries
     * @return void
     */
    public function setEntries(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $entries)
    {
        $this->entries = $entries;
    }

    public function getDisplayStartDate()
    {
        $date = $this->startDate;
        if ($date) {
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }
        return $date;
    }

    public function getDisplayEndDate()
    {
        $date = $this->endDate;
        if ($date) {
            $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }
        return $date;
    }

    /**
     * It's important to call this function only on timeslot objects, that
     * have been processed by the TimeslotManager
     */
    public function getIsBookable()
    {
        // check date (only if in future)
        $now = new \DateTime('now');
        if ($this->getStartDate() < $now) {
            return false;
        }

        // check weight
        if ($this->getBookedWeight() >= $this->maxWeight) {
            return false;
        }

        // check activated hooks hooks
        if (!$this->getIsBookableByHooks()) {
            return false;
        }

        return true;
    }

    /**
     * It's important to call this function only on timeslot objects, that
     * have been processed by the TimeslotManager
     */
    public function getBookedWeight()
    {
        $weight = 0;
        foreach ($this->entries as $entry) {
            $weight += $entry->getWeight();
        }
        return $weight;
    }

    public function getIsBookableByHooks()
    {
        $activeHooks = $this->getIsBookableHooksArray();

        foreach ($activeHooks as $key => $isActiveHook) {
            // dont call hook if not checked via TCA
            if (!$isActiveHook) {
                continue;
            }

            // get the hook from offset of global registed hooks array, make instance and call it
            $hookClassName = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/timeslot']['isBookable'][$key];
            $_procObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($hookClassName);
            if (!$_procObj->isBookable($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * converts number of $isBookableHooks to array of activated hooks
     * e.g. 4 => 100 => [1,0,0] => [0,0,1] => [false,false,true]
     *
     * @return Array
     */
    public function getIsBookableHooksArray()
    {
        return array_map(
            function ($value) {
                return $value === '1';
            },
            array_reverse(str_split(decbin($this->getIsBookableHooks())))
        );
    }

    /**
     * Returns the isBookableHooks
     *
     * @return int $isBookableHooks
     */
    public function getIsBookableHooks()
    {
        return $this->isBookableHooks;
    }

    /**
     * Sets the isBookableHooks
     *
     * @param int $isBookableHooks
     * @return void
     */
    public function setIsBookableHooks($isBookableHooks)
    {
        $this->isBookableHooks = $isBookableHooks;
    }

    /**
     * It's important to call this function only on timeslot objects, that
     * have been processed by the TimeslotManager
     */
    public function getFreeWeight()
    {
        return $this->maxWeight - $this->getBookedWeight();
    }

    public function getIcsOutput(Ics $ics, ClassSchema $classSchema)
    {
        $now = new DateTime();

        $this->startDate->setTimezone($now->getTimezone());
        $this->endDate->setTimezone($now->getTimezone());

        $icsText = "BEGIN:VEVENT
            " . IcsUtility::getIcsDates($this->getStartDate(), $this->getEndDate()) . "
            DTSTAMP:" . $now->format('Ymd\THis\Z') . "
            SUMMARY:" . IcsUtility::compileTemplate($ics->getTimeslotTitle(), $this, $classSchema) . "
            DESCRIPTION:" . IcsUtility::compileTemplate($ics->getTimeslotDescription(), $this, $classSchema) . "
            UID:timeslot-" . $this->getUid() . "-" . random_int(1, 9999999) . "
            STATUS:CONFIRMED
            LAST-MODIFIED:" . $now->format('Ymd\THis\Z') . "
            LOCATION:" . IcsUtility::compileTemplate($ics->getTimeslotLocation(), $this, $classSchema) . "
            END:VEVENT\n";

        return $icsText;
    }

}
