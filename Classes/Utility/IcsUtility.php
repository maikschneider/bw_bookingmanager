<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\CalendarEvent;
use Blueways\BwBookingmanager\Domain\Model\Dto\EntryCalendarEvent;
use Blueways\BwBookingmanager\Domain\Model\Dto\TimeslotCalendarEvent;
use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use DateTime;

class IcsUtility
{
    protected EntryRepository $entryRepository;

    protected TimeslotRepository $timeslotRepository;

    public function __construct(EntryRepository $entryRepository, TimeslotRepository $timeslotRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->timeslotRepository = $timeslotRepository;
    }

    public static function compileTemplate(string $templateString, CalendarEvent $object): string
    {
        // look for FIELD:point
        // @TODO: look for relations
        preg_match_all('/(FIELD:)(\w+)((?:\.)(\w+))?/', $templateString, $fieldStatements);

        if (count($fieldStatements[0])) {
            foreach ($fieldStatements[0] as $key => $fieldStatement) {
                $propertyName = $fieldStatements[2][$key];
                if (property_exists($object, $propertyName)) {
                    $replaceWith = $object->__get($propertyName);
                    $templateString = (string)str_replace($fieldStatement, $replaceWith, $templateString);
                }
            }
        }

        preg_match_all('/(FUNC:)(\w+)((?:\.)(\w+))?/', $templateString, $fieldStatements);

        // look for FUNC:getBookedWeight
        if (count($fieldStatements[0])) {
            foreach ($fieldStatements[0] as $key => $fieldStatement) {
                $functionName = $fieldStatements[2][$key];
                if (method_exists($object, $functionName)) {
                    $replaceWith = (string)$object->$functionName();
                    $templateString = (string)str_replace($fieldStatement, $replaceWith, $templateString);
                }
            }
        }

        return utf8_decode($templateString);
    }

    public static function getIcsDates(DateTime $startDate, DateTime $endDate): string
    {
        if (CalendarEvent::isFullDay($startDate, $endDate)) {
            return 'DTSTART;VALUE=DATE:' . $startDate->format('Ymd') . '
                    DTEND;VALUE=DATE:' . $endDate->format('Ymd') . '';
        }

        return 'DTSTART:' . $startDate->format('Ymd\THis') . '
                DTEND:' . $endDate->format('Ymd\THis') . '';
    }

    public function getFromIcs(Ics $ics): string
    {
        $feed = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nMETHOD:PUBLISH\r\nPRODID:-//Maik Schneider//BwBookingManager Events//EN\r\n";

        $feed .= $this->getIcsFeed($ics);

        $feed .= 'END:VCALENDAR';
        $feed = str_replace('  ', '', $feed);
        $feed = preg_replace('~\R~u', "\r\n", $feed);

        return $feed;
    }

    /**
     * @param Ics $ics
     * @return string
     */
    public function getIcsFeed(Ics $ics): string
    {
        $options = $ics->getOptionsArray();
        $calendars = $ics->getCalendars();
        $startDate = $ics->getStartDate();
        $endDate = $ics->getEndDate();

        $feed = '';

        // Ics for Timeslots
        if ($options[0] || $options[1]) {
            $timeslotEvents = $this->timeslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);

            /** @var TimeslotCalendarEvent $timeslotEvent */
            foreach ($timeslotEvents as $timeslotEvent) {
                if (($options[0] && $timeslotEvent->getIsBookable()) || ($options[1] && !$timeslotEvent->getIsBookable())) {
                    $feed .= $timeslotEvent->getIcsOutput($ics);
                }
            }
        }

        // Ics for Entries
        if ($options[2] || $options[3]) {
            $entryEvents = $this->entryRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);

            /** @var EntryCalendarEvent $entryEvent */
            foreach ($entryEvents as $entryEvent) {
                $feed .= $entryEvent->getIcsOutput($ics);
            }
        }
//
//        // Ics for Blockslots
//        if ($options[4]) {
//            $blockslotRepository = $objectManager->get(BlockslotRepository::class);
//            $blockslots = $blockslotRepository->findAllInRange($calendarUids, $startDate, $endDate)->toArray();
//            $classSchema = $reflectionService->getClassSchema(Blockslot::class);
//
//            /** @var \Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslot */
//            foreach ($blockslots as $blockslot) {
//                $feed .= $blockslot->getIcsOutput($ics, $classSchema);
//            }
//        }
//
//        // Ics for Holidays
//        if ($options[5]) {
//            $holidayRepository = $objectManager->get(HolidayRepository::class);
//            $holidays = $holidayRepository->findInCalendars($calendarUids, $startDate, $endDate)->toArray();
//            $classSchema = $reflectionService->getClassSchema(Holiday::class);
//
//            /** @var Holiday $holiday */
//            foreach ($holidays as $holiday) {
//                $feed .= $holiday->getIcsOutput($ics, $classSchema);
//            }
//        }

        return $feed;
    }
}
