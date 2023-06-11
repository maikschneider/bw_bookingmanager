<?php

namespace Blueways\BwBookingmanager\EventListener;

use Blueways\BwBookingmanager\Domain\Model\Notification;
use Blueways\BwBookingmanager\Event\AfterEntryCreationEvent;
use Blueways\BwBookingmanager\Event\NotificationCondition\NotificationConditionInterface;
use Blueways\BwBookingmanager\Helper\NotificationDispatcher;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class NotificationsAfterEntryCreate
{
    protected NotificationDispatcher $notificationDispatcher;

    public function __construct(NotificationDispatcher $notificationDispatcher)
    {
        $this->notificationDispatcher = $notificationDispatcher;
    }

    /**
     * @throws InvalidConfigurationTypeException
     * @throws TransportExceptionInterface
     */
    public function __invoke(AfterEntryCreationEvent $event): void
    {
        $entry = $event->getEntry();

        $notifications = $entry->getCalendar()->getNotifications();

        foreach ($notifications as $notification) {
            if ($notification->getEvent() !== Notification::EVENT_CREATION) {
                continue;
            }

            $conditionNames = GeneralUtility::trimExplode(',', $notification->getConditions(), true);

            foreach ($conditionNames as $conditionName) {
                if (!is_subclass_of($conditionName, NotificationConditionInterface::class)) {
                    throw new \RuntimeException('Class ' . $conditionName . ' does not implement NotificationConditionInterface::class');
                }

                /** @var NotificationConditionInterface $condition */
                $condition = GeneralUtility::makeInstance($conditionName);
                if (!$condition->doSend($entry, $notification)) {
                    continue 2;
                }
            }

            $this->notificationDispatcher->dispatchEntryNotification($notification, $entry);
        }
    }
}
