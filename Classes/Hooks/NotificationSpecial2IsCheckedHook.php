<?php
namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Helper\NotificationManager;
use Blueways\BwBookingmanager\Domain\Model\Notification;
class NotificationSpecial2IsCheckedHook
{
    const HOOK_ID = 'special2IsChecked';
    const HOOK_LABEL = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_notification.hook.special2IsChecked';

    /**
     * @var NotificationManager $notificationManager
     */
    protected $notificationManager;

    /**
     * @var NotificationManager $notificationManager
     * @var Notification $notification
     */
    public function executeHook($notificationManager, $notification)
    {
        $this->notificationManager = $notificationManager;

        if ($notification->getHook() === NotificationSpecial2IsCheckedHook::HOOK_ID && $this->notificationManager->getEntry()->isSpecial2()) {
            $this->notificationManager->sendNotification($notification);
        }
    }
}
