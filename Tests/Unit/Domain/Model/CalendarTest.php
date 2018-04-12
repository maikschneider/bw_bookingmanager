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
        self::assertEquals(
            null,
            $this->subject->getTimeslots()
        );
    }

    /**
     * @test
     */
    public function setTimeslotsForTimeslotSetsTimeslots()
    {
        $timeslotsFixture = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
        $this->subject->setTimeslots($timeslotsFixture);

        self::assertAttributeEquals(
            $timeslotsFixture,
            'timeslots',
            $this->subject
        );
    }
}
