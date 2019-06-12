<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CalendarManagerUtility
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $calendar;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository;

    /**
     * CalendarManagerUtility constructor.
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    public function __construct(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \Exception
     */
    public function getConfiguration(DateConf $dateConf): array
    {
        $cacheIdentifier = sha1($this->calendar->getUid() . $dateConf->start->getTimestamp() . $dateConf->end->getTimestamp());
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');

        if (($configuration = $cache->get($cacheIdentifier)) === false) {

            $cacheTags = ['calendar' . $this->calendar->getUid()];

            $entries = $this->entryRepository->findInRange($this->calendar, $dateConf);
            $timeslots = $this->timeslotRepository->findInRange($this->calendar, $dateConf);

            foreach ($entries as $entry) {
                $cacheTags[] = 'entry' . $entry->getUid();
            }

            foreach ($timeslots as $timeslot) {
                $cacheTags[] = 'timeslot' . $timeslot->getUid();
            }

            // build render configuration
            $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf,
                $this->calendar);
            $calendarConfiguration->setTimeslots($timeslots);
            $calendarConfiguration->setEntries($entries);
            $configuration = $calendarConfiguration->getRenderConfiguration();

            $cache->set($cacheIdentifier, $configuration, array_unique($cacheTags), 2592000);
        }

        return $configuration;
    }

}
