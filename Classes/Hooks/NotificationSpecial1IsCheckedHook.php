<?php

namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Domain\Model\Notification;
use Blueways\BwBookingmanager\Helper\NotificationManager;

class NotificationSpecial1IsCheckedHook
{
    const HOOK_ID = 'special1IsChecked';

    const HOOK_LABEL = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.hook.special1IsChecked';

    /**
     * @var NotificationManager $notificationManager
     */
    protected $notificationManager;

    /**
     * @var Notification $notification
     */
    public function executeHook($notificationManager, $notification)
    {
        $this->notificationManager = $notificationManager;

        if ($notification->getHook() === NotificationSpecial1IsCheckedHook::HOOK_ID && $notificationManager->getEntry()->isSpecial1()) {
            $notificationManager->sendNotification($notification);
        }
    }
}
