<?php

namespace Blueways\BwBookingmanager\Helper;

/**
 * This is fwefewfew
 * PHP version 7.2
 *
 * @package  BwBookingManager
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https: //opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http://www.blueways.de
 */

use Blueways\BwBookingmanager\Domain\Model\Notification;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class NotificationManager
{

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     */
    protected $entry = null;

    /**
     * @var array<\Blueways\BwBookingmanager\Domain\Model\Notification> $notifications
     */
    protected $notifications = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager
     */
    protected $configurationManager;

    /**
     * @var Array
     */
    protected $extbaseFrameworkConfiguration;

    /**
     * NotificationManager constructor.
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->notifications = $entry->getCalendar()->getNotifications();
        $this->configurationManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
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

    public function notifyDeletion()
    {
        $this->sendNotifications(Notification::EVENT_DELETION);
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
        $emailView = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView');
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('bw_email')) {
            $emailView = GeneralUtility::makeInstance('Blueways\BwEmail\View\EmailView');
        }
        $emailView->setLayoutRootPaths($this->extbaseFrameworkConfiguration['view']['layoutRootPaths']);
        $emailView->setPartialRootPaths($this->extbaseFrameworkConfiguration['view']['partialRootPaths']);
        $emailView->setTemplateRootPaths($this->extbaseFrameworkConfiguration['view']['templateRootPaths']);
        $emailView->getRenderingContext()->setControllerName('Email');
        $emailView->setTemplate($templateName);
        $emailView->assign('entry', $this->entry);

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
     * @param \Blueways\BwBookingmanager\Domain\Model\Notification $notification
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

    public function getEntry()
    {
        return $this->entry;
    }
}
