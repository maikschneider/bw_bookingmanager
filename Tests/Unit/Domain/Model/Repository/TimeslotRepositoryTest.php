<?php

declare(strict_types=1);

namespace Blueways\BwBookingmanager\Tests\Unit\Domain\Model\Repository;

use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use Prophecy\Prophecy\ProphecySubjectInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TimeslotRepositoryTest extends UnitTestCase
{

    /**
     * @var TimeslotRepository
     */
    protected TimeslotRepository $subject;

    /** @var ObjectManagerInterface|ProphecySubjectInterface */
    protected $objectManager;

    /**
     * @test
     */
    public function isRepository(): void
    {
        self::assertInstanceOf(Repository::class, $this->subject);
    }

    protected function setUp(): void
    {
        $this->objectManager = $this->prophesize(ObjectManager::class)->reveal();
        $this->subject = new TimeslotRepository($this->objectManager);
    }


}


