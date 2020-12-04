<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IcsUtility
{
    public static function compileTemplate(string $templateString, AbstractEntity $object)
    {
        return utf8_decode($templateString);
    }

    public static function getIcsDates(\DateTime $startDate, \DateTime $endDate): string
    {
        if (self::isFullDay($startDate, $endDate)) {
            return "DTSTART;VALUE=DATE:" . $startDate->format('Ymd') . "
                    DTEND;VALUE=DATE:" . $endDate->format('Ymd') . "";
        }

        return "DTSTART:" . $startDate->format('Ymd\THis\Z') . "
                DTEND:" . $endDate->format('Ymd\THis\Z') . "";
    }

    public static function isFullDay(\DateTime $startDate, \DateTime $endDate): bool
    {
        return $startDate->format('H') === '00' && (int)$endDate->format('H') >= 23;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @return string
     */
    public function getIcsFile(Ics $ics): string
    {
        $options = $ics->getOptionsArray();
        $calendars = $ics->getCalendars();
        $calendarUids = CalendarRepository::getUidsFromObjectStorage($calendars);
        $startDate = $ics->getStartDate();
        $endDate = $ics->getEndDate();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $feed = '';

        if ($options[0] || $options[1]) {
            $timeslotUtil = $objectManager->get(TimeslotUtility::class);
            $timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);

            /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
            foreach ($timeslots as $timeslot) {
                if (($options[0] && $timeslot->getIsBookable()) || ($options[1] && !$timeslot->getIsBookable())) {
                    $feed .= $timeslot->getIcsOutput($ics);
                }
            }
        }

        // Ics for Blockslots
        if ($options[4]) {

            $blockslotRepository = $objectManager->get(BlockslotRepository::class);
            $blockslots = $blockslotRepository->findAllInRange($calendarUids, $startDate, $endDate)->toArray();

            foreach ($blockslots as $blockslot) {
                $feed .= $blockslot->getIcsOutput($ics);
            }
        }

        return $feed;
    }

}
