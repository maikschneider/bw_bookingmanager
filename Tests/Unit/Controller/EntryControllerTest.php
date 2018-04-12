<?php
namespace Blueways\BwBookingmanager\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Maik Schneider <m.schneider@blueways.de>
 */
class EntryControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Blueways\BwBookingmanager\Controller\EntryController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Blueways\BwBookingmanager\Controller\EntryController::class)
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
    public function createActionAddsTheGivenEntryToEntryRepository()
    {
        $entry = new \Blueways\BwBookingmanager\Domain\Model\Entry();

        $entryRepository = $this->getMockBuilder(\::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepository->expects(self::once())->method('add')->with($entry);
        $this->inject($this->subject, 'entryRepository', $entryRepository);

        $this->subject->createAction($entry);
    }
}
