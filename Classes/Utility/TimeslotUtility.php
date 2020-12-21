<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Timeslot;
use Blueways\BwBookingmanager\Domain\Repository\HolidayRepository;
use DateTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class TimeslotUtility
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     */
    protected \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository
     */
    protected $blockslotRepositorty;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     */
    protected $entryRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\HolidayRepository
     */
    protected HolidayRepository $holidayRepository;

    /**
     * @var ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Blockslot>
     */
    protected $blockslots;

    /**
     * @var ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Holiday>
     */
    protected $holidays;

    /**
     * @var ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar>
     */
    protected $calendars;

    /**
     * @var ObjectStorage<Timeslot>
     */
    protected $timeslots;

    /**
     * @var ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry>
     */
    protected $entries;

    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var array
     */
    protected $filterCriteria;

    protected $objectManager;

    /**
     * TimeslotUtility constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->timeslots = $this->objectManager->get(ObjectStorage::class);
        $this->blockslots = $this->objectManager->get(ObjectStorage::class);
        $this->calendars = $this->objectManager->get(ObjectStorage::class);
        $this->holidays = $this->objectManager->get(ObjectStorage::class);
        $this->entries = $this->objectManager->get(ObjectStorage::class);
    }

    public function injectTimeslotRepository(
        \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository $timeslotRepository
    ) {
        $this->timeslotRepository = $timeslotRepository;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getBlockslots()
    {
        return $this->blockslots;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getTimeslots(ObjectStorage $calendars, DateTime $startDate, DateTime $endDate): ObjectStorage
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calendars = $calendars;

        $this->setTimeslotsFromRepository();
        $this->setBlockslotsFromRepository();
        $this->setHolidaysFromRepository();
        $this->setEntriesFromRepository();

        $this->repeatTimeslots();
        $this->createFilterCritera();
        $this->filterTimeslots();
        $this->filterEntries();

        return $this->timeslots;
    }

    protected function setTimeslotsFromRepository()
    {
        $this->timeslots = $this->objectManager->get(ObjectStorage::class);
        $calendarUids = $this->getCalendarUids();

        $queryResult = $this->timeslotRepository->findAllPossibleByDateRange(
            $calendarUids,
            $this->startDate,
            $this->endDate
        );

        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $this->timeslots->attach($object);
            }
        }
    }

    protected function getCalendarUids()
    {
        $calendarUids = [];

        if (!$this->calendars->count()) {
            return $calendarUids;
        }

        foreach ($this->calendars as $calendar) {
            $calendarUids[] = $calendar->getUid();
        }

        return $calendarUids;
    }

    protected function setBlockslotsFromRepository()
    {
        $calendarUids = $this->getCalendarUids();

        $queryResult = $this->blockslotRepositorty->findAllInRange(
            $calendarUids,
            $this->startDate,
            $this->endDate
        );

        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $this->blockslots->attach($object);
            }
        }
    }

    protected function setHolidaysFromRepository()
    {
        $calendarUids = $this->getCalendarUids();

        $queryResult = $this->holidayRepository->findInCalendars(
            $calendarUids,
            $this->startDate,
            $this->endDate
        );

        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $this->holidays->attach($object);
            }
        }
    }

    protected function setEntriesFromRepository()
    {
        $calendarUids = $this->getCalendarUids();

        $queryResult = $this->entryRepository->findInCalendars(
            $calendarUids,
            $this->startDate,
            $this->endDate
        );

        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $this->entries->attach($object);
            }
        }
    }

    protected function repeatTimeslots()
    {
        $newTimeslots = $this->objectManager->get(ObjectStorage::class);
        /** @var Timeslot $timeslot */
        foreach ($this->timeslots as $timeslot) {
            $repeatType = $timeslot->getRepeatType();
            if ($repeatType === Timeslot::REPEAT_DAILY) {
                $newTimeslots->addAll($this->repeatDailyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_WEEKLY) {
                $newTimeslots->addAll($this->repeatWeeklyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_MONTHLY) {
                $newTimeslots->addAll($this->repeatMonthlyTimeslot($timeslot));
            }
            if ($repeatType === Timeslot::REPEAT_MULTIPLE_WEEKLY) {
                $newTimeslots->addAll($this->repeatMultipleWeeklyTimeslot($timeslot));
            }
        }

        $this->timeslots->addAll($newTimeslots);
    }

    /**
     * duplicates daily timeslot for whole date range
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return ObjectStorage
     */
    private function repeatDailyTimeslot($timeslot): ObjectStorage
    {
        $newTimeslots = $this->objectManager->get(ObjectStorage::class);

        // default fill the whole date range with that timeslot
        $daysToFillTimeslots = $this->endDate->diff($this->startDate)->days + 1;
        $dateToStartFilling = clone $timeslot->getStartDate();
        $dateToStartFilling->setDate(
            $this->startDate->format('Y'),
            $this->startDate->format('m'),
            $this->startDate->format('d')
        );
        $dateStartEndDiff = $timeslot->getStartDate()->diff($timeslot->getEndDate());

        $timezone = new \DateTimeZone("Europe/Berlin");

        // create new timeslots and modify start and end date
        for ($i = 0; $i < $daysToFillTimeslots; $i++) {
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+' . $i . ' days');

            // DST fix
            $transitions = $timezone->getTransitions(
                $timeslot->getStartDate()->getTimestamp(),
                $newStartDate->getTimestamp()
            );
            $lastTransitionIndex = sizeof($transitions) - 1;
            if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('+1 hour');
            }
            if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('-1 hour');
            }

            $newEndDate = clone $newStartDate;
            $newEndDate->add($dateStartEndDiff);

            // dont add new timeslot if placed before actual timeslot or even at same time
            if ($newStartDate <= $timeslot->getStartDate()) {
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if ($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate) {
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);
            $newTimeslots->attach($newTimeslot);
        }

        return $newTimeslots;
    }

    /**
     * duplicates weekly timeslots in date range
     *
     * @param Timeslot $timeslot
     * @return ObjectStorage
     */
    private function repeatWeeklyTimeslot($timeslot)
    {
        $newTimeslots = $this->objectManager->get(ObjectStorage::class);

        // default fill the all mondays (or tuesdays..) of date range
        $daysToFillTimeslots = self::dayCount(
            $this->startDate,
            $this->endDate,
            $timeslot->getStartDate()->format('w')
        );

        $dateToStartFilling = clone $timeslot->getStartDate();
        $dateToStartFilling->setDate(
            $this->startDate->format('Y'),
            $this->startDate->format('m'),
            $this->startDate->format('d')
        );
        $dateToStartFilling->modify('-1 days');
        $dateToStartFilling->modify('next ' . $timeslot->getStartDate()->format('l H:i:s'));
        $dateStartEndDiff = $timeslot->getStartDate()->diff($timeslot->getEndDate());

        $timezone = new \DateTimeZone('Europe/Berlin');

        for ($i = 0; $i < $daysToFillTimeslots; $i++) {
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+' . $i . ' weeks');

            // DST fix
            $transitions = $timezone->getTransitions(
                $timeslot->getStartDate()->getTimestamp(),
                $newStartDate->getTimestamp()
            );
            $lastTransitionIndex = sizeof($transitions) - 1;
            if ($transitions[0]['isdst'] && !$transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('+1 hour');
            }
            if (!$transitions[0]['isdst'] && $transitions[$lastTransitionIndex]['isdst']) {
                $newStartDate->modify('-1 hour');
            }

            $newEndDate = clone $newStartDate;
            $newEndDate->add($dateStartEndDiff);

            // dont add new timeslot if placed before actual timeslot or even at same time
            if ($newStartDate <= $timeslot->getStartDate()) {
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if ($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate) {
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);

            $newTimeslots->attach($newTimeslot);
        }

        return $newTimeslots;
    }

    /**
     * Counts the occurences of a day of week in a date range
     * credits to this crazy motherfucker: https://stackoverflow.com/questions/20068975/count-the-no-of-fridays-or-any-between-two-specific-dates/20071461#20071461
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $dayOfWeek
     * @return bool|float
     */
    public static function dayCount($from, $to, $day)
    {
        $wF = $from->format('w');
        $wT = $to->format('w');
        if ($wF < $wT) {
            $isExtraDay = $day >= $wF && $day <= $wT;
        } elseif ($wF == $wT) {
            $isExtraDay = $wF == $day;
        } else {
            $isExtraDay = $day >= $wF || $day <= $wT;
        }

        return floor($from->diff($to)->days / 7) + $isExtraDay;
    }

    private function repeatMonthlyTimeslot($timeslot): ObjectStorage
    {
        return $this->objectManager->get(ObjectStorage::class);
    }

    /**
     * @param Timeslot $timeslot
     * @return ObjectStorage
     */
    private function repeatMultipleWeeklyTimeslot($timeslot)
    {
        $timeslots = $this->objectManager->get(ObjectStorage::class);
        $repeatDays = $timeslot->getRepeatDaysSelectedWeekDays();
        $startWeekDay = (int)$timeslot->getStartDate()->format('w');
        $endDate = $timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() < $this->endDate ? $timeslot->getRepeatEnd() : $this->endDate;
        $daysToCrawl = $endDate->diff($timeslot->getStartDate())->days;

        $isDst = $timeslot->getStartDate()->format('I');

        for ($i = 1; $i <= $daysToCrawl; $i++) {
            $currentWeekDay = ($i + $startWeekDay) % 7;
            if (in_array($currentWeekDay, $repeatDays)) {
                $startDate = clone $timeslot->getStartDate();
                $endDate = clone $timeslot->getEndDate();

                // DST fix
                if ($isDst && !$startDate->format('I')) {
                    $startDate->modify('+1 hour');
                }
                if (!$isDst && $endDate->format('I')) {
                    $endDate->modify('-1 hour');
                }

                $newTimeslot = $this->copyTimeslot($timeslot);
                $newTimeslot->setStartDate($startDate->modify('+ ' . $i . ' days'));
                $newTimeslot->setEndDate($endDate->modify('+ ' . $i . ' days'));

                $timeslots->attach($newTimeslot);
            }
        }

        return $timeslots;
    }

    public function copyTimeslot(Timeslot $object): Timeslot
    {
        /** @var Timeslot $clone */
        $clone = $this->objectManager->get(Timeslot::class);
        $properties = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettableProperties($object);
        foreach ($properties as $propertyName => $propertyValue) {
            if ($propertyValue instanceof ObjectStorage) {

                // new empty storage
                $value = $this->objectManager->get(ObjectStorage::class);
                
            } else {
                $value = $propertyValue;
            }
            if ($value !== null) {
                \TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($clone, $propertyName, $value);
            }
        }
        return $clone;
    }

    /**
     * create array of critera that timeslots need to pass
     */
    private function createFilterCritera()
    {
        $filterCriteria = array_flip($this->getCalendarUids());

        $filterCriteria = array_map(function () {
            return [
                'in' => [
                    [$this->startDate, $this->endDate]
                ],
                'inAny' => [

                ],
                'notIn' => [

                ],
                'holidays' => [

                ]
            ];
        }, $filterCriteria);

        // criteria for Blockslots
        foreach ($this->blockslots as $blockslot) {
            foreach ($blockslot->getCalendars() as $calendar) {
                $filterCriteria[$calendar->getUid()]['notIn'][] = [
                    $blockslot->getStartDate(),
                    $blockslot->getEndDate()
                ];
            }
        }

        $this->filterCriteria = $filterCriteria;

        // start+end dates for Holidays
        foreach ($this->holidays as $holiday) {
            foreach ($holiday->getCalendars() as $calendar) {
                $holiStartDate = $holiday->getStartDate()->setTime(0, 0, 0);
                $holiEndDate = $holiday->getEndDate()->setTime(23, 59, 59);

                // check if block is inside date range
                // so add its dates to filterCriteria
                if (!($holiEndDate < $this->startDate || $holiStartDate > $this->endDate)) {
                    $this->filterCriteria[$calendar->getUid()]['holidays'][] = [$holiStartDate, $holiEndDate];
                }
            }
        }
    }

    /**
     * removes timeslots that do not pass filterCriteria
     */
    private function filterTimeslots()
    {
        /** @var Timeslot $timeslot */
        foreach (clone $this->timeslots as $timeslot) {

            // @TODO: switch from m:n of timeslot <-> calendar to 1:n
            // we are using the first calendar in timeslot, in hope there are not any more
            $calendarUid = $timeslot->getCalendar()->getUid();

            // check timeslot if holidays should move to 'in' or 'out' critera array
            if ($timeslot->getHolidaySetting() === Timeslot::HOLIDAY_NOT_DURING) {
                foreach ($this->filterCriteria[$calendarUid]['holidays'] as $range) {
                    if ($timeslot->getEndDate() < $range[0] || $timeslot->getStartDate() > $range[1]) {
                        continue;
                    }
                    $this->timeslots->detach($timeslot);
                    continue 2;
                }
            }

            // filter timeslots for holiday setting to be within
            if ($timeslot->getHolidaySetting() === Timeslot::HOLIDAY_ONLY_DURING) {
                $notInAny = true;
                foreach ($this->filterCriteria[$calendarUid]['holidays'] as $range) {
                    if ($timeslot->getStartDate() >= $range[0] && $timeslot->getEndDate() <= $range[1]) {
                        $notInAny = false;
                    }
                }
                if ($notInAny) {
                    $this->timeslots->detach($timeslot);
                    continue;
                }
            }

            // check for date range to be within
            // it is allowed that events start in the past, as long as they end in the given range or even alter
            foreach ($this->filterCriteria[$calendarUid]['in'] as $range) {
                if ($timeslot->getEndDate() < $range[0] || $timeslot->getStartDate() > $range[1]) {
                    $this->timeslots->detach($timeslot);
                    continue 2;
                }
            }

            // check for date range to be not within
            // only this is valid: [slot] |blocked| [slot]
            // this is not valid   [ slot |] blocked [| slot ]
            foreach ($this->filterCriteria[$calendarUid]['notIn'] as $range) {
                if ($timeslot->getEndDate() < $range[0] || $timeslot->getStartDate() > $range[1]) {
                    continue;
                }
                $this->timeslots->detach($timeslot);
                continue 2;
            }
        }
    }

    protected function filterEntries()
    {
        /** @var Timeslot $timeslot */
        foreach ($this->timeslots as $timeslot) {
            /** @var \Blueways\BwBookingmanager\Domain\Model\Entry $entry */
            foreach ($this->entries as $entry) {
                if ($timeslot->getStartDate()->getTimestamp() === $entry->getStartDate()->getTimestamp() && $timeslot->getEndDate()->getTimestamp() === $entry->getEndDate()->getTimestamp()) {
                    $timeslot->addEntry($entry);
                } else {
                    $timeslot->removeEntry($entry);
                }
            }
        }
    }

    public function injectBlockslotRepositorty(
        \Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository $blockslotRepositorty
    ) {
        $this->blockslotRepositorty = $blockslotRepositorty;
    }

    public function injectEntryRepository(
        \Blueways\BwBookingmanager\Domain\Repository\EntryRepository $entryRepository
    ) {
        $this->entryRepository = $entryRepository;
    }

    public function injectHolidayRepository(
        \Blueways\BwBookingmanager\Domain\Repository\HolidayRepository $holidayRepository
    ) {
        $this->holidayRepository = $holidayRepository;
    }

}
