<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class EntryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Entry
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Blueways\BwBookingmanager\Domain\Model\Entry();
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
    public function getPrenameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPrename()
        );
    }

    /**
     * @test
     */
    public function setPrenameForStringSetsPrename()
    {
        $this->subject->setPrename('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'prename',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStreetReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getStreet()
        );
    }

    /**
     * @test
     */
    public function setStreetForStringSetsStreet()
    {
        $this->subject->setStreet('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'street',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getZipReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipForStringSetsZip()
    {
        $this->subject->setZip('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'zip',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCityReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCityForStringSetsCity()
    {
        $this->subject->setCity('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'city',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPhoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function setPhoneForStringSetsPhone()
    {
        $this->subject->setPhone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'phone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'email',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getNewsletterReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getNewsletter()
        );
    }

    /**
     * @test
     */
    public function setNewsletterForBoolSetsNewsletter()
    {
        $this->subject->setNewsletter(true);

        self::assertAttributeEquals(
            true,
            'newsletter',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getWeightReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getWeight()
        );
    }

    /**
     * @test
     */
    public function setWeightForIntSetsWeight()
    {
        $this->subject->setWeight(12);

        self::assertAttributeEquals(
            12,
            'weight',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTimeslotReturnsInitialValueForTimeslot()
    {
        self::assertEquals(
            null,
            $this->subject->getTimeslot()
        );
    }

    /**
     * @test
     */
    public function setTimeslotForTimeslotSetsTimeslot()
    {
        $timeslotFixture = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();
        $this->subject->setTimeslot($timeslotFixture);

        self::assertAttributeEquals(
            $timeslotFixture,
            'timeslot',
            $this->subject
        );
    }
}
