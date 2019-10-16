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
     * @return mixed
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function getConfiguration(DateConf $dateConf)
    {
        $cacheIdentifier = sha1($this->calendar->getUid() . $dateConf->start->getTimestamp() . $dateConf->end->getTimestamp());
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');

        if (($configuration = $cache->get($cacheIdentifier)) === false) {
            $configuration = $this->buildAndCacheConfiguration($dateConf);
        }

        return $configuration;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    private function buildAndCacheConfiguration(DateConf $dateConf)
    {
        $configuration = $this->buildConfiguration($dateConf);
        $cacheTags = ['calendar' . $this->calendar->getUid()];

        foreach ($configuration['timeslots'] as $key => $timeslot) {
            $cacheTags[] = 'timeslot' . $timeslot->getUid();
            $configuration['timeslots'][$key] = $timeslot->getApiOutput();
        }

        foreach ($configuration['entries'] as $key => $entry) {
            $cacheTags[] = 'entry' . $entry->getUid();
            $configuration['entries'][$key] = $entry->getApiOutput();
        }

        $cacheIdentifier = sha1($this->calendar->getUid() . $dateConf->start->getTimestamp() . $dateConf->end->getTimestamp());
        $cache = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache('bwbookingmanager_calendar');
        $cache->set($cacheIdentifier, $configuration, array_unique($cacheTags), 2592000);

        return $configuration;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    private function buildConfiguration(DateConf $dateConf)
    {
        $entries = $this->entryRepository->findInRange($this->calendar, $dateConf, false)->toArray();
        $timeslots = $this->timeslotRepository->findInRange($this->calendar, $dateConf);

        /** @var \Blueways\BwBookingmanager\Helper\RenderConfiguration $calendarConfiguration */
        $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf,
            $this->calendar);
        $calendarConfiguration->setTimeslots($timeslots);
        $calendarConfiguration->setEntries($entries);
        $configuration = $calendarConfiguration->getRenderConfiguration();

        return $configuration;
    }

}
