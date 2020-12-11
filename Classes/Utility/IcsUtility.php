<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Blockslot;
use Blueways\BwBookingmanager\Domain\Model\Holiday;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Domain\Model\Timeslot;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\HolidayRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ClassSchema;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

class IcsUtility
{

    public static function compileTemplate(string $templateString, AbstractEntity $object, ClassSchema $classSchema)
    {
        // look for FIELD:point
        // @TODO: look for relations
        preg_match_all('/(FIELD:)(\w+)((?:\.)(\w+))?/', $templateString, $fieldStatements);

        if (sizeof($fieldStatements[0])) {
            foreach ($fieldStatements[0] as $key => $fieldStatement) {
                $propertyName = $fieldStatements[2][$key];
                if ($object->_hasProperty($propertyName)) {
                    $replaceWith = $object->_getProperty($propertyName);
                    $templateString = (string)str_replace($fieldStatement, $replaceWith, $templateString);
                }
            }
        }

        preg_match_all('/(FUNC:)(\w+)((?:\.)(\w+))?/', $templateString, $fieldStatements);

        // look for FUNC:getBookedWeight
        if (sizeof($fieldStatements[0])) {
            foreach ($fieldStatements[0] as $key => $fieldStatement) {
                $functionName = $fieldStatements[2][$key];
                if ($classSchema->hasMethod($functionName)) {
                    $replaceWith = (string)$object->$functionName();
                    $templateString = (string)str_replace($fieldStatement, $replaceWith, $templateString);
                }
            }
        }

        return utf8_decode($templateString);
    }

    public static function getIcsDates(\DateTime $startDate, \DateTime $endDate): string
    {
        if (self::isFullDay($startDate, $endDate)) {
            return "DTSTART;VALUE=DATE:" . $startDate->format('Ymd') . "
                    DTEND;VALUE=DATE:" . $endDate->format('Ymd') . "";
        }

        return "DTSTART:" . $startDate->format('Ymd\THis') . "
                DTEND:" . $endDate->format('Ymd\THis') . "";
    }

    public static function isFullDay(\DateTime $startDate, \DateTime $endDate): bool
    {
        return $startDate->format('H') === '00' && (int)$endDate->format('H') >= 23;
    }

    public function getFromIcs(Ics $ics): string
    {
        $feed = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nMETHOD:PUBLISH\r\nPRODID:-//Maik Schneider//BwBookingManager Events//EN\r\n";

        $feed .= $this->getIcsFeed($ics);

        $feed .= "END:VCALENDAR";
        $feed = str_replace('  ', '', $feed);
        $feed = preg_replace('~\R~u', "\r\n", $feed);

        return $feed;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @return string
     */
    public function getIcsFeed(Ics $ics): string
    {
        $options = $ics->getOptionsArray();
        $calendars = $ics->getCalendars();
        $calendarUids = CalendarRepository::getUidsFromObjectStorage($calendars);
        $startDate = $ics->getStartDate();
        $endDate = $ics->getEndDate();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $reflectionService = $objectManager->get(ReflectionService::class);

        $feed = '';

        // Ics for Timeslots
        if ($options[0] || $options[1]) {
            $timeslotUtil = $objectManager->get(TimeslotUtility::class);
            $timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);
            $classSchema = $reflectionService->getClassSchema(Timeslot::class);

            /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
            foreach ($timeslots as $timeslot) {
                if (($options[0] && $timeslot->getIsBookable()) || ($options[1] && !$timeslot->getIsBookable())) {
                    $feed .= $timeslot->getIcsOutput($ics, $classSchema);
                }
            }
        }

        // Ics for Entries
        if ($options[2] || $options[3]) {
            $entryRepository = $objectManager->get(EntryRepository::class);
            $entries = $entryRepository->findInCalendars($calendarUids, $startDate, $endDate)->toArray();
            $classSchema = $reflectionService->getClassSchema(Blockslot::class);

            /** @var \Blueways\BwBookingmanager\Domain\Model\Entry $entry */
            foreach ($entries as $entry) {
                $feed .= $entry->IcsOutput($ics, $classSchema);
            }
        }

        // Ics for Blockslots
        if ($options[4]) {
            $blockslotRepository = $objectManager->get(BlockslotRepository::class);
            $blockslots = $blockslotRepository->findAllInRange($calendarUids, $startDate, $endDate)->toArray();
            $classSchema = $reflectionService->getClassSchema(Blockslot::class);

            /** @var \Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslot */
            foreach ($blockslots as $blockslot) {
                $feed .= $blockslot->getIcsOutput($ics, $classSchema);
            }
        }

        // Ics for Holidays
        if ($options[5]) {
            $holidayRepository = $objectManager->get(HolidayRepository::class);
            $holidays = $holidayRepository->findInCalendars($calendarUids, $startDate, $endDate)->toArray();
            $classSchema = $reflectionService->getClassSchema(Holiday::class);

            /** @var Holiday $holiday */
            foreach ($holidays as $holiday) {
                $feed .= $holiday->getIcsOutput($ics, $classSchema);
            }
        }

        return $feed;
    }
}
