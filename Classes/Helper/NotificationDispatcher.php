<?php

namespace Blueways\BwBookingmanager\Helper;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class NotificationDispatcher
{
    protected ConfigurationManager $configurationManager;

    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Notification $notification
     * @param \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function dispatchEntryNotification(Notification $notification, Entry $entry): void
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $useBwEmail = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('bw_bookingmanager', 'useBwEmail');

        if ($useBwEmail) {
            $emailView = GeneralUtility::makeInstance(\Blueways\BwEmail\View\EmailView::class);
            $emailView->setLayoutRootPaths($configuration['plugin.']['tx_bwemail.']['view.']['layoutRootPaths.']);
            $emailView->setPartialRootPaths($configuration['plugin.']['tx_bwemail.']['view.']['partialRootPaths.']);
            $emailView->setTemplateRootPaths($configuration['plugin.']['tx_bwemail.']['view.']['templateRootPaths.']);
            $emailView->setTemplate($notification->getTemplate());
            $emailView->assign('record', $entry);
            $html = $emailView->render();

            $fromAddress = $configuration['plugin.']['tx_bwemail.']['settings.']['senderAddress'] ?? '';
            $fromName = $configuration['plugin.']['tx_bwemail.']['settings.']['senderName'] ?? '';
            $replyTo = $configuration['plugin.']['tx_bwemail.']['settings.']['replytoAddress'] ?? '';
            $bcc = $configuration['plugin.']['tx_bwemail.']['settings.']['bcc'] ?? '';
            if (!$fromAddress) {
                $fromAddress = MailUtility::getSystemFrom()[0];
                $fromName = MailUtility::getSystemFrom()[1] ?? '';
            }
            $from = $fromName ? [$fromAddress => $fromName] : [$fromAddress];
            $to = $notification->getEmail() ?: $entry->getEmail();
            $subject = $notification->getEmailSubject();

            $mailMessage = GeneralUtility::makeInstance(MailMessage::class);
            $mailMessage->setTo($to)
                ->setFrom($from)
                ->setSubject($subject)
                ->html($html);

            if ($replyTo) {
                $mailMessage->setReplyTo($replyTo);
            }

            if ($bcc) {
                $mailMessage->setBcc($bcc);
            }

            $mailMessage->send();
            return;
        }

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
