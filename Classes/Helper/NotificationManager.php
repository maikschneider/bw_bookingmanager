<?php
namespace Blueways\BwBookingmanager\Helper;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class oganizes the correct arrangement of timeslots
 */
class NotificationManager
{
    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Entry $entry
     */
    protected $entry = null;

    /**
     * @var Array<\Blueways\BwBookingmanager\Domain\Model\Notification> $notifications
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

    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->notifications = $entry->getCalendar()->getNotifications();
        $this->configurationManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
        $this->extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
    }

    public function notify()
    {
        $this->sendConfirmation();
        $this->sendNotifications();
    }

    private function sendConfirmation()
    {   
        // abbort if not activated
        if (!$this->extbaseFrameworkConfiguration['settings']['mail']['doSendConfirmation']) return false;

        $from = $this->extbaseFrameworkConfiguration['settings']['mail']['sender'];
        $to = $this->entry->getEmail();
        $subject = $this->extbaseFrameworkConfiguration['settings']['mail']['subject'];
        $template = $this->extbaseFrameworkConfiguration['settings']['mail']['template'];
        $body = $this->getMailBody('Email/' . $template);

        $this->sendMail($from, $to, $subject, $body);
    }

    private function sendNotifications()
    {
        foreach ($this->notifications as $notification) {
            // check for hooks
            $this->sendNotification($notification);
        }
    }

    private function sendNotification()
    {

    }

    private function sendMail($from, $to, $subject, $body)
    {
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($to)
            ->setFrom($from)
            ->setSubject($subject)
            ->setBody($body, 'text/html');
        
        $message->send();
        var_dump($message->isSent());
    }

    private function getMailBody($templateName)
    {

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        $emailView = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView');
        $emailView->setLayoutRootPaths($extbaseFrameworkConfiguration['view']['layoutRootPaths']);
        $emailView->setPartialRootPaths($extbaseFrameworkConfiguration['view']['partialRootPaths']);
        $emailView->setTemplateRootPaths($extbaseFrameworkConfiguration['view']['templateRootPaths']);

        $emailView->setTemplate($templateName);
        $emailView->assign('entry', $this->entry);

        $emailBody = $emailView->render();

        return $emailBody;
    }
}
