<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class CalendarControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Controller\CalendarController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Blueways\BwBookingmanager\Controller\CalendarController::class)
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
    public function listActionFetchesAllCalendarsFromRepositoryAndAssignsThemToView()
    {
        $allCalendars = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $calendarRepository = $this->getMockBuilder(\Blueways\BwBookingmanager\Domain\Repository\CalendarRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $calendarRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCalendars));
        $this->inject($this->subject, 'calendarRepository', $calendarRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('calendars', $allCalendars);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCalendarToView()
    {
        $calendar = new \Blueways\BwBookingmanager\Domain\Model\Calendar();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('calendar', $calendar);

        $this->subject->showAction($calendar);
    }
}
