<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class BlockslotTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Blockslot
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Blueways\BwBookingmanager\Domain\Model\Blockslot();
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
    public function getReasonReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getReason()
        );
    }

    /**
     * @test
     */
    public function setReasonForStringSetsReason()
    {
        $this->subject->setReason('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'reason',
            $this->subject
        );
    }
}
