<?php

namespace Blueways\BwBookingmanager\Tests\Functional\Domain\Repository;

use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\TestingFramework\Core\Exception;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TimeslotRepositoryTest extends FunctionalTestCase
{
    /**
     * @var array Load required extensions
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/bw_bookingmanager',
    ];

    /**
     * @var TimeslotRepository
     */
    private $subject;

    private $connection;

    /**
     * @var PersistenceManager
     */
    private $persistenceManager;

    /**
     * @var Typo3QuerySettings
     */
    private $querySettings;

    /**
     * @test
     * @throws Exception
     */
    public function correctTimeslotRepeatQueryResult()
    {
        $this->importDataSet('EXT:bw_bookingmanager/Tests/Functional/Fixtures/Calendar1.xml');
        $timeslots = $this->subject->getTimeslotsInCalendar(
            1,
            new \DateTime('1970-01-01 00:00:00'),
            new \DateTime('3000-01-01 00:00:00')
        );
        self::assertEquals([], $timeslots);
    }

    /**
     * @test
     * @throws Exception
     */
    public function correctAmountOfRepeating(): void
    {
        $this->importDataSet('EXT:bw_bookingmanager/Tests/Functional/Fixtures/Calendar1.xml');
        $this->importDataSet('EXT:bw_bookingmanager/Tests/Functional/Fixtures/Timeslot1.xml');

        $timeslots = $this->subject->getTimeslotsInCalendar(
            1,
            new \DateTime('2020-12-01 00:00:00'),
            new \DateTime('2020-12-31 23:59:00')
        );

        self::assertCount(16, $timeslots);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_bwbookingmanager_domain_model_timeslot');

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->querySettings = $objectManager->get(Typo3QuerySettings::class);
        $this->querySettings->setRespectStoragePage(false);

        $this->subject = $objectManager->get(TimeslotRepository::class);
        $this->subject->setDefaultQuerySettings($this->querySettings);
    }
}
