<?php
namespace Blueways\BwBookingmanager\Hooks;

class IsCheckedSpecialHook
{
    /**
     * @var Blueways\BwBookingmanager\Helper\NotificationManager $notificationManager
     */
    protected $notificationManager;

    /**
     * @var Blueways\BwBookingmanager\Domain\Model\Notification $notification
     */

    public function executeHook($notificationManager, $notification)
    {
        $this->notificationManager = $notificationManager;
        $this->notification = $notification;

        $this->checkSpecials();
    }

    private function checkSpecials()
    {
        if ($this->notification->getHook() === 'special1IsChecked' && $this->notificationManager->getEntry()->isSpecial1()) {
            $this->notificationManager->sendNotification($this->notification);
        }

        if ($this->notification->getHook() === 'special2IsChecked' && $this->notificationManager->getEntry()->isSpecial2()) {
            $this->notificationManager->sendNotification($this->notification);
        }

    }
}
