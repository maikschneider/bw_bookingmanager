<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class CalendarTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Blueways\BwBookingmanager\Domain\Model\Calendar();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTimeslotsReturnsInitialValueForTimeslot()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getTimeslots()
        );
    }

    /**
     * @test
     */
    public function setTimeslotsForObjectStorageContainingTimeslotSetsTimeslots()
    {
        $timeslot = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
        $objectStorageHoldingExactlyOneTimeslots = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneTimeslots->attach($timeslot);
        $this->subject->setTimeslots($objectStorageHoldingExactlyOneTimeslots);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneTimeslots,
            'timeslots',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addTimeslotToObjectStorageHoldingTimeslots()
    {
        $timeslot = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
        $timeslotsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $timeslotsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($timeslot));
        $this->inject($this->subject, 'timeslots', $timeslotsObjectStorageMock);

        $this->subject->addTimeslot($timeslot);
    }

    /**
     * @test
     */
    public function removeTimeslotFromObjectStorageHoldingTimeslots()
    {
        $timeslot = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
        $timeslotsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $timeslotsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($timeslot));
        $this->inject($this->subject, 'timeslots', $timeslotsObjectStorageMock);

        $this->subject->removeTimeslot($timeslot);
    }

    /**
     * @test
     */
    public function getBlockslotsReturnsInitialValueForBlockslot()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getBlockslots()
        );
    }

    /**
     * @test
     */
    public function setBlockslotsForObjectStorageContainingBlockslotSetsBlockslots()
    {
        $blockslot = new \Blueways\BwBookingmanager\Domain\Model\Blockslot();
        $objectStorageHoldingExactlyOneBlockslots = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneBlockslots->attach($blockslot);
        $this->subject->setBlockslots($objectStorageHoldingExactlyOneBlockslots);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneBlockslots,
            'blockslots',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addBlockslotToObjectStorageHoldingBlockslots()
    {
        $blockslot = new \Blueways\BwBookingmanager\Domain\Model\Blockslot();
        $blockslotsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $blockslotsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($blockslot));
        $this->inject($this->subject, 'blockslots', $blockslotsObjectStorageMock);

        $this->subject->addBlockslot($blockslot);
    }

    /**
     * @test
     */
    public function removeBlockslotFromObjectStorageHoldingBlockslots()
    {
        $blockslot = new \Blueways\BwBookingmanager\Domain\Model\Blockslot();
        $blockslotsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $blockslotsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($blockslot));
        $this->inject($this->subject, 'blockslots', $blockslotsObjectStorageMock);

        $this->subject->removeBlockslot($blockslot);
    }
}
