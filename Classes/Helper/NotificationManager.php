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

    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->notifications = $entry->getCalendar()->getNotifications();
    }

    public function sendNotifications()
    {

    }

    private function createEmailHtml()
    {
        $emailView = GeneralUtility::makeInstance(('TYPO3\\CMS\\Fluid\\View\\StandaloneView'));
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
        $templatePathAndFilename = $templateRootPath . 'Email/' . $templateName . '.html';
        $emailView->setTemplatePathAndFilename($templatePathAndFilename);
        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();
    }
}
