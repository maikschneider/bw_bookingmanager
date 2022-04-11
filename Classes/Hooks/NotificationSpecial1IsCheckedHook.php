<?php
namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Helper\NotificationManager;
use Blueways\BwBookingmanager\Domain\Model\Notification;
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
        $this->notification = $notification;

        if ($this->notification->getHook() === NotificationSpecial1IsCheckedHook::HOOK_ID && $this->notificationManager->getEntry()->isSpecial1()) {
            $this->notificationManager->sendNotification($this->notification);
        }
    }
}
