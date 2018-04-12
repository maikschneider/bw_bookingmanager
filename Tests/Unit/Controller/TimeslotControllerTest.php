<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class TimeslotControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Controller\TimeslotController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Blueways\BwBookingmanager\Controller\TimeslotController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllTimeslotsFromRepositoryAndAssignsThemToView()
    {

        $allTimeslots = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $timeslotRepository = $this->getMockBuilder(\::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $timeslotRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTimeslots));
        $this->inject($this->subject, 'timeslotRepository', $timeslotRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('timeslots', $allTimeslots);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTimeslotToView()
    {
        $timeslot = new \Blueways\BwBookingmanager\Domain\Model\Timeslot();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('timeslot', $timeslot);

        $this->subject->showAction($timeslot);
    }
}
