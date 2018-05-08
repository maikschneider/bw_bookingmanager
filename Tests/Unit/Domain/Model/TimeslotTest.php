<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class TimeslotTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Timeslot
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getStartDateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getStartDate()
        );
    }

    /**
     * @test
     */
    public function setStartDateForDateTimeSetsStartDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setStartDate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'startDate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEndDateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getEndDate()
        );
    }

    /**
     * @test
     */
    public function setEndDateForDateTimeSetsEndDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setEndDate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'endDate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getRepeatTypeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getRepeatType()
        );
    }

    /**
     * @test
     */
    public function setRepeatTypeForIntSetsRepeatType()
    {
        $this->subject->setRepeatType(12);

        self::assertAttributeEquals(
            12,
            'repeatType',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMaxWeightReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getMaxWeight()
        );
    }

    /**
     * @test
     */
    public function setMaxWeightForIntSetsMaxWeight()
    {
        $this->subject->setMaxWeight(12);

        self::assertAttributeEquals(
            12,
            'maxWeight',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEntriesReturnsInitialValueForEntry()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getEntries()
        );
    }

    /**
     * @test
     */
    public function setEntriesForObjectStorageContainingEntrySetsEntries()
    {
        $entry = new \Blueways\BwBookingmanager\Domain\Model\Entry();
        $objectStorageHoldingExactlyOneEntries = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneEntries->attach($entry);
        $this->subject->setEntries($objectStorageHoldingExactlyOneEntries);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneEntries,
            'entries',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addEntryToObjectStorageHoldingEntries()
    {
        $entry = new \Blueways\BwBookingmanager\Domain\Model\Entry();
        $entriesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $entriesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($entry));
        $this->inject($this->subject, 'entries', $entriesObjectStorageMock);

        $this->subject->addEntry($entry);
    }

    /**
     * @test
     */
    public function removeEntryFromObjectStorageHoldingEntries()
    {
        $entry = new \Blueways\BwBookingmanager\Domain\Model\Entry();
        $entriesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $entriesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($entry));
        $this->inject($this->subject, 'entries', $entriesObjectStorageMock);

        $this->subject->removeEntry($entry);
    }

    /**
     * @test
     */
    public function getCalendarsReturnsInitialValueForCalendar()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getCalendars()
        );
    }

    /**
     * @test
     */
    public function setCalendarsForObjectStorageContainingCalendarSetsCalendars()
    {
        $calendar = new \Blueways\BwBookingmanager\Domain\Model\Calendar();
        $objectStorageHoldingExactlyOneCalendars = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneCalendars->attach($calendar);
        $this->subject->setCalendars($objectStorageHoldingExactlyOneCalendars);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneCalendars,
            'calendars',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addCalendarToObjectStorageHoldingCalendars()
    {
        $calendar = new \Blueways\BwBookingmanager\Domain\Model\Calendar();
        $calendarsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $calendarsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($calendar));
        $this->inject($this->subject, 'calendars', $calendarsObjectStorageMock);

        $this->subject->addCalendar($calendar);
    }

    /**
     * @test
     */
    public function removeCalendarFromObjectStorageHoldingCalendars()
    {
        $calendar = new \Blueways\BwBookingmanager\Domain\Model\Calendar();
        $calendarsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $calendarsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($calendar));
        $this->inject($this->subject, 'calendars', $calendarsObjectStorageMock);

        $this->subject->removeCalendar($calendar);
    }
}
