<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Core\Cache\CacheManager;
use Blueways\BwBookingmanager\Helper\RenderConfiguration;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CalendarManagerUtility
{

    /**
     * @var Calendar
     */
    protected $calendar;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var TimeslotRepository
     */
    protected $timeslotRepository;

    /**
     * @var BlockslotRepository
     */
    protected $blockslotRepository;

    /**
     * CalendarManagerUtility constructor.
     *
     * @param Calendar $calendar
     */
    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @param DateConf $dateConf
     * @return mixed
     * @throws NoSuchCacheException
     * @throws InvalidQueryException
     */
    public function getConfiguration(DateConf $dateConf)
    {
        $cacheIdentifier = sha1($this->calendar->getUid() . $dateConf->start->getTimestamp() . $dateConf->end->getTimestamp());
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('bwbookingmanager_calendar');

        if (($configuration = $cache->get($cacheIdentifier)) === false) {
            $configuration = $this->buildAndCacheConfiguration($dateConf);
        }

        return $configuration;
    }

    /**
     * @param DateConf $dateConf
     * @return array
     * @throws NoSuchCacheException
     * @throws InvalidQueryException
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
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('bwbookingmanager_calendar');
        $cache->set($cacheIdentifier, $configuration, array_unique($cacheTags), 2592000);

        return $configuration;
    }

    /**
     * @param DateConf $dateConf
     * @return array
     * @throws NoSuchCacheException
     * @throws InvalidQueryException
     */
    private function buildConfiguration(DateConf $dateConf)
    {
        $entries = $this->entryRepository->findInRange($this->calendar, $dateConf, false)->toArray();
        $timeslots = $this->timeslotRepository->findInRange($this->calendar, $dateConf);
        $blockslots = $this->blockslotRepository->findAllInRange([$this->calendar], $dateConf->start, $dateConf->end);

        /** @var RenderConfiguration $calendarConfiguration */
        $calendarConfiguration = new RenderConfiguration($dateConf,
            $this->calendar);
        $calendarConfiguration->setTimeslots($timeslots);
        $calendarConfiguration->setEntries($entries);
        $calendarConfiguration->setBlockslots($blockslots);
        $configuration = $calendarConfiguration->getRenderConfiguration();

        return $configuration;
    }

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectTimeslotRepository(TimeslotRepository $timeslotRepository): void
    {
        $this->timeslotRepository = $timeslotRepository;
    }

    public function injectBlockslotRepository(BlockslotRepository $blockslotRepository): void
    {
        $this->blockslotRepository = $blockslotRepository;
    }

}
