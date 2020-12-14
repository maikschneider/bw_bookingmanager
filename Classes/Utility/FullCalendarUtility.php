<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\CalendarEventInterface;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Lang\LanguageService;

class FullCalendarUtility
{

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var LanguageService
     */
    protected $llService;

    public function getEvents($pid, $start, $end): array
    {
        $events = [];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Blueways\BwBookingmanager\Utility\TimeslotUtility $timeslotUtil */
        $timeslotUtil = $objectManager->get(TimeslotUtility::class);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $calendars = $objectManager->get(ObjectStorage::class);
        $queryResult = $calendarRepository->findAllByPid($pid);
        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $calendars->attach($object);
            }
        }
        $timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);
        $blockslots = $timeslotUtil->getBlockslots();
        $holidays = $timeslotUtil->getHolidays();
        $entries = $timeslotUtil->getEntries();

        /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
        foreach ($timeslots as $timeslot) {
            if ($timeslot->getIsBookable()) {
                $events[] = $this->getEventConfiguration($timeslot);
            }
        }

        foreach ($blockslots as $blockslot) {
            $events[] = $blockslot->getFullCalendarEvent();
        }

        foreach ($holidays as $holiday) {
            $events[] = $holiday->getFullCalendarEvent();
        }

        /** @var \Blueways\BwBookingmanager\Domain\Model\Entry $entry */
        foreach ($entries as $entry) {
            $events[] = $this->getEventConfiguration($entry);
        }

        return $events;
    }

    public function injectLlService(\TYPO3\CMS\Lang\LanguageService $llService)
    {
        $this->llService = $llService;
    }

    public function injectUriBuilder(\TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    private function getEventConfiguration(CalendarEventInterface $entity)
    {
        $event = $entity->getFullCalendarEvent();
        $event['url'] = $this->uriBuilder->buildUriFromRoute('record_edit', $event['backendUrl'])->__toString();
        $event['title'] = $this->llService->sL($event['title']);

        return $event;
    }
}
