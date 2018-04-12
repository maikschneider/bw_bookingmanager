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
    public function getCalendarReturnsInitialValueFor()
    {
    }

    /**
     * @test
     */
    public function setCalendarForSetsCalendar()
    {
    }
}
