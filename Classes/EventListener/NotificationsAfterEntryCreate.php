<?php

namespace Blueways\BwBookingmanager\EventListener;

use Blueways\BwBookingmanager\Domain\Model\Notification;
use Blueways\BwBookingmanager\Event\AfterEntryCreationEvent;
use Blueways\BwBookingmanager\Helper\NotificationDispatcher;

class NotificationsAfterEntryCreate
{
    protected NotificationDispatcher $notificationDispatcher;

    public function __construct(NotificationDispatcher $notificationDispatcher)
    {
        $this->notificationDispatcher = $notificationDispatcher;
    }

    public function __invoke(AfterEntryCreationEvent $event): void
    {
        $entry = $event->getEntry();

        $notifications = $entry->getCalendar()->getNotifications();

        foreach ($notifications as $notification) {
            if ($notification->getEvent() === Notification::EVENT_CREATION) {
                $this->notificationDispatcher->dispatchEntryNotification($notification, $entry);
            }
        }
    }
}
