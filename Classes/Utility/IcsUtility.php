<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Ics;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class IcsUtility
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     *
     */
    protected $entryRepository;

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Ics $ics
     * @return string
     */
    public function getIcsFile(Ics $ics)
    {
        $options = $ics->getOptionsArray();
        $calendars = $ics->getCalendars();
        $startDate = $ics->getStartDate();
        $endDate = $ics->getEndDate();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $feed = '';

        if ($options[2] || $options[3]) {
            $timeslotUtil = $objectManager->get(TimeslotUtility::class);
            $timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);
            /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
            foreach ($timeslots as $timeslot) {
                $feed .= $timeslot->getIcsOutput($ics);
            }
        }


        return $feed;
    }

    public function injectEntryRepository(\Blueways\BwBookingmanager\Domain\Repository\EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }



}
