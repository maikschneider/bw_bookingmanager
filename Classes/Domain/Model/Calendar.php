<?php

namespace Blueways\BwBookingmanager\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/
/**
 * Calendar
 */
class Calendar extends AbstractEntity
{
    const ENTRY_TYPE_CLASSNAME = 'Blueways\\BwBookingmanager\\Domain\\Model\\Entry';

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * timeslots
     *
     * @var ObjectStorage<Timeslot>
     * @Extbase\ORM\Lazy
     */
    protected $timeslots;

    /**
     * blockslots
     *
     * @var ObjectStorage<Blockslot>
     * @Extbase\ORM\Lazy
     */
    protected $blockslots;

    /**
     * holidays
     *
     * @var ObjectStorage<Holiday>
     * @Extbase\ORM\Lazy
     */
    protected $holidays;

    /**
     * notifications
     *
     * @var ObjectStorage<Notification>
     * @Extbase\ORM\Lazy
     */
    protected $notifications;

    /**
     * entries
     *
     * @var ObjectStorage<Entry>
     * @Extbase\ORM\Lazy
     */
    protected $entries;

    /**
     * @var bool
     */
    protected $directBooking;

    /**
     * @var int
     */
    protected $defaultStartTime;

    /**
     * @return int
     */
    public function getDefaultStartTime(): int
    {
        return $this->defaultStartTime;
    }

    /**
     * @return int
     */
    public function getDefaultEndTime(): int
    {
        return $this->defaultEndTime;
    }

    /**
     * @var int
     */
    protected $defaultEndTime;

    /**
     * @var int
     */
    protected $minLength;

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @var string
     */
    protected $color;

    /**
     * @return int
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * @return int
     */
    public function getMinOffset(): int
    {
        return $this->minOffset;
    }

    /**
     * @var int
     */
    protected $minOffset;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    protected function initStorageObjects()
    {
        $this->timeslots = new ObjectStorage();
        $this->blockslots = new ObjectStorage();
    }

    /**
     * @return ObjectStorage
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param ObjectStorage $entries
     */
    public function setEntries(ObjectStorage $entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return bool
     */
    public function isDirectBooking(): bool
    {
        return $this->directBooking;
    }

    /**
     * @param bool $directBooking
     */
    public function setDirectBooking(bool $directBooking)
    {
        $this->directBooking = $directBooking;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Adds a Blockslot
     *
     * @param Blockslot $blockslot
     */
    public function addBlockslot(Blockslot $blockslot)
    {
        $this->blockslots->attach($blockslot);
    }

    /**
     * Removes a Blockslot
     *
     * @param Blockslot $blockslotToRemove The Blockslot to be removed
     */
    public function removeBlockslot(Blockslot $blockslotToRemove)
    {
        $this->blockslots->detach($blockslotToRemove);
    }

    /**
     * Returns the blockslots
     *
     * @return ObjectStorage<Blockslot> $blockslots
     */
    public function getBlockslots()
    {
        return $this->blockslots;
    }

    /**
     * Sets the blockslots
     *
     * @param ObjectStorage<Blockslot> $blockslots
     */
    public function setBlockslots(ObjectStorage $blockslots)
    {
        $this->blockslots = $blockslots;
    }

    /**
     * @return ObjectStorage
     */
    public function getHolidays(): ObjectStorage
    {
        return $this->holidays;
    }

    /**
     * @param ObjectStorage $holidays
     */
    public function setHolidays(ObjectStorage $holidays): void
    {
        $this->holidays = $holidays;
    }

    /**
     * Adds a Timeslot
     *
     * @param Timeslot $timeslot
     */
    public function addTimeslot(Timeslot $timeslot)
    {
        $this->timeslots->attach($timeslot);
    }

    /**
     * Removes a Timeslot
     *
     * @param Timeslot $timeslotToRemove The Timeslot to be removed
     */
    public function removeTimeslot(Timeslot $timeslotToRemove)
    {
        $this->timeslots->detach($timeslotToRemove);
    }

    /**
     * Returns the timeslots
     *
     * @return ObjectStorage<Timeslot> $timeslots
     */
    public function getTimeslots()
    {
        return $this->timeslots;
    }

    /**
     * Sets the timeslots
     *
     * @param ObjectStorage<Timeslot> $timeslots
     */
    public function setTimeslots(ObjectStorage $timeslots)
    {
        $this->timeslots = $timeslots;
    }

    /**
     * Adds a Notification
     *
     * @param Notification $notification
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications->attach($notification);
    }

    /**
     * Removes a Notification
     *
     * @param Notification $NotificationToRemove The Notification to be removed
     */
    public function removeNotification(Notification $notificationToRemove)
    {
        $this->notifications->detach($notificationToRemove);
    }

    /**
     * Returns the notifications
     *
     * @return ObjectStorage<Notification> $notifications
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Sets the notifications
     *
     * @param ObjectStorage<Notification> $notifications
     */
    public function setNotifications(ObjectStorage $notifications)
    {
        $this->notifications = $notifications;
    }

    public function getTimeslotEntries()
    {
        $entries = new ObjectStorage();
        foreach ($this->timeslots as $timeslot) {
            $entries->addAll($timeslot->getEntries());
        }

        return $entries;
    }
}
