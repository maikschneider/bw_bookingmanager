<?php

namespace Blueways\BwBookingmanager\Helper;

/**
 * This is fwefewfew
 * PHP version 7.2
 *
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https: //opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http://www.blueways.de
 */
use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;
use Blueways\BwEmail\Utility\SenderUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class NotificationManager
{
    /**
     * @var Entry $entry
     */
    protected $entry;

    /**
     * @var array<Notification> $notifications
     */
    protected $notifications;

    /**
     * @var ConfigurationManager $configurationManager
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $extbaseFrameworkConfiguration;

    /**
     * @var \Blueways\BwEmail\Utility\SenderUtility
     */
    protected $senderUtility;

    /**
     * NotificationManager constructor.
     *
     * @param Entry $entry
     * @throws InvalidConfigurationTypeException
     */
    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->notifications = $entry->getCalendar()->getNotifications();
        $this->configurationManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->senderUtility = GeneralUtility::makeInstance(SenderUtility::class);
    }

    /**
     * Override (Merge) settings from plugin settings with typoscript
     *
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->extbaseFrameworkConfiguration['settings'], $settings);
    }

    public function notify()
    {
        $this->sendConfirmation();
        $this->sendNotifications(Notification::EVENT_CREATION);
    }

    private function sendConfirmation()
    {
        // abbort if not activated
        if (!$this->extbaseFrameworkConfiguration['settings']['mail']['doSendConfirmation']) {
            return false;
        }

        $from = $this->extbaseFrameworkConfiguration['settings']['mail']['senderAddress'];
        $to = $this->entry->getEmail();
        $subject = $this->extbaseFrameworkConfiguration['settings']['mail']['subject'];
        $template = $this->extbaseFrameworkConfiguration['settings']['mail']['template'];
        $body = $this->getMailBody($template);
        $replyTo = $from;

        $this->sendMail($from, $to, $subject, $body, $replyTo);
    }

    private function getMailBody($templateName)
    {
        $emailView = GeneralUtility::makeInstance('Blueways\BwEmail\View\EmailView');
        $emailView->getRenderingContext()->setControllerName('Email');
        $emailView->setTemplate($templateName);
        $emailView->assign('record', $this->entry);

        $emailBody = $emailView->render();

        return $emailBody;
    }

    private function sendMail($from, $to, $subject, $body, $replyTo)
    {
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($to)
            ->setReplyTo($replyTo)
            ->setFrom($from)
            ->setSubject($subject)
            ->setBody($body, 'text/html');

        $message->send();
    }

    private function sendNotifications($eventType)
    {
        foreach ($this->notifications as $notification) {
            if ($notification->getEvent() !== $eventType) {
                continue;
            }

            if ($notification->hasHook()) {
                $this->triggerHook($notification);
            } else {
                $this->sendNotification($notification);
            }
        }
    }

    private function triggerHook($notification)
    {
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'] ?? [] as $className) {
            $_procObj = GeneralUtility::makeInstance($className);
            $_procObj->executeHook($this, $notification);
        }
    }

    /**
     * @param Notification $notification
     */
    public function sendNotification($notification)
    {
        $from = $this->extbaseFrameworkConfiguration['settings']['mail']['senderAddress'];
        $to = $notification->getEmail();
        $subject = $notification->getEmailSubject();
        $template = $notification->getTemplate();
        $body = $this->getMailBody($template);
        $replyTo = $this->entry->getEmail();

        $this->sendMail($from, $to, $subject, $body, $replyTo);
    }

    public function notifyDeletion()
    {
        $this->sendNotifications(Notification::EVENT_DELETION);
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
