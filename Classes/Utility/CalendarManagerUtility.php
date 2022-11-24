<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Blueways\BwBookingmanager\Helper\RenderConfiguration;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

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
        return $this->buildAndCacheConfiguration($dateConf);
    }

    /**
     * @param DateConf $dateConf
     * @return array
     * @throws NoSuchCacheException
     * @throws InvalidQueryException
     */
    private function buildAndCacheConfiguration(DateConf $dateConf)
    {
        return $this->buildConfiguration($dateConf);
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
        $calendarConfiguration = new RenderConfiguration(
            $dateConf,
            $this->calendar
        );
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
