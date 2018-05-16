<?php
namespace Blueways\BwBookingmanager\Domain\Model;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * Calendar
 */
class Calendar extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * timeslots
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Timeslot>
     * @lazy
     */
    protected $timeslots = null;

    /**
     * blockslots
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Blockslot>
     */
    protected $blockslots = null;

    /**
     * notifications
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Notification>
     */
    protected $notifications = null;

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
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

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
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->timeslots = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->blockslots = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Blockslot
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslot
     * @return void
     */
    public function addBlockslot(\Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslot)
    {
        $this->blockslots->attach($blockslot);
    }

    /**
     * Removes a Blockslot
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslotToRemove The Blockslot to be removed
     * @return void
     */
    public function removeBlockslot(\Blueways\BwBookingmanager\Domain\Model\Blockslot $blockslotToRemove)
    {
        $this->blockslots->detach($blockslotToRemove);
    }

    /**
     * Returns the blockslots
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Blockslot> $blockslots
     */
    public function getBlockslots()
    {
        return $this->blockslots;
    }

    /**
     * Sets the blockslots
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Blockslot> $blockslots
     * @return void
     */
    public function setBlockslots(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $blockslots)
    {
        $this->blockslots = $blockslots;
    }

    /**
     * Adds a Timeslot
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot
     * @return void
     */
    public function addTimeslot(\Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot)
    {
        $this->timeslots->attach($timeslot);
    }

    /**
     * Removes a Timeslot
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslotToRemove The Timeslot to be removed
     * @return void
     */
    public function removeTimeslot(\Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslotToRemove)
    {
        $this->timeslots->detach($timeslotToRemove);
    }

    /**
     * Returns the timeslots
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Timeslot> $timeslots
     */
    public function getTimeslots()
    {
        return $this->timeslots;
    }

    /**
     * Sets the timeslots
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Timeslot> $timeslots
     * @return void
     */
    public function setTimeslots(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $timeslots)
    {
        $this->timeslots = $timeslots;
    }

    /**
     * Sets the notifications
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Notification> $notifications
     * @return void
     */
    public function setNotifications(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Adds a Notification
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Notification $notification
     * @return void
     */
    public function addNotification(\Blueways\BwBookingmanager\Domain\Model\Notification $notification)
    {
        $this->notifications->attach($notification);
    }

    /**
     * Removes a Notification
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Notification $NotificationToRemove The Notification to be removed
     * @return void
     */
    public function removeNotification(\Blueways\BwBookingmanager\Domain\Model\Notification $notificationToRemove)
    {
        $this->notifications->detach($notificationToRemove);
    }

    /**
     * Returns the notifications
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Notification> $notifications
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
