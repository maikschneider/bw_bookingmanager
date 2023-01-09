<?php

namespace Blueways\BwBookingmanager\Helper;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class NotificationDispatcher
{
    protected ConfigurationManager $configurationManager;

    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @throws InvalidConfigurationTypeException
     * @throws TransportExceptionInterface
     */
    public function dispatchEntryNotification(Notification $notification, Entry $entry)
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        $fromAddress = $configuration['plugin.']['tx_bwbookingmanager.']['settings.']['mail.']['senderAddress'] ?? '';
        $fromName = $configuration['plugin.']['tx_bwbookingmanager.']['settings.']['mail.']['senderName'] ?? '';
        if (!$fromAddress) {
            $fromAddress = MailUtility::getSystemFrom()[0];
            $fromName = MailUtility::getSystemFrom()[1] ?? '';
        }
        $from = new Address($fromAddress, $fromName);
        $to = $notification->getEmail() ?: $entry->getEmail();
        $subject = $notification->getEmailSubject();
        $template = $notification->getTemplate();

        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($to)
            ->from($from)
            ->subject($subject)
            ->format(FluidEmail::FORMAT_HTML)
            ->setTemplate($template)
            ->assign('record', $entry);

        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }
}
