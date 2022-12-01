<?php

namespace Blueways\BwBookingmanager\Helper;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @deprecated
 */
class NotificationManager
{

    protected Entry $entry;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Notification> $notifications
     */
    protected ObjectStorage $notifications;

    protected ConfigurationManager $configurationManager;

    /**
     * @var array
     */
    protected array $extbaseFrameworkConfiguration;

    /**
     * @param Entry $entry
     * @throws InvalidConfigurationTypeException
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
        $this->notifications = $entry->getCalendar()->getNotifications();
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->extbaseFrameworkConfiguration['settings'], $settings);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function notify()
    {
        $this->sendConfirmation();
        $this->sendNotifications(Notification::EVENT_CREATION);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function sendConfirmation(): void
    {
        // abort if not activated
        if (!$this->extbaseFrameworkConfiguration['settings']['mail']['doSendConfirmation']) {
            return;
        }

        $from = $this->extbaseFrameworkConfiguration['settings']['mail']['senderAddress'];
        $to = $this->entry->getEmail();
        $subject = $this->extbaseFrameworkConfiguration['settings']['mail']['subject'];
        $template = $this->extbaseFrameworkConfiguration['settings']['mail']['template'];

        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($to)
            ->from($from)
            ->subject($subject)
            ->format(FluidEmail::FORMAT_HTML)
            ->setTemplate($template)
            ->assign('record', $this->entry);

        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function sendNotifications($eventType): void
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

    private function triggerHook($notification): void
    {
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/bw_bookingmanager/notification']['sendNotification'] ?? [] as $className) {
            $_procObj = GeneralUtility::makeInstance($className);
            $_procObj->executeHook($this, $notification);
        }
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNotification($notification): void
    {
        $from = $this->extbaseFrameworkConfiguration['settings']['mail']['senderAddress'];
        $to = $notification->getEmail();
        $subject = $notification->getEmailSubject();
        $template = $notification->getTemplate();

        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($to)
            ->from($from)
            ->subject($subject)
            ->format(FluidEmail::FORMAT_HTML)
            ->setTemplate($template)
            ->assign('record', $this->entry);

        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function notifyDeletion(): void
    {
        $this->sendNotifications(Notification::EVENT_DELETION);
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
