<?php

namespace Blueways\BwBookingmanager\Events;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Recordlist\Event\ModifyRecordListRecordActionsEvent;

class ModifyEntryListTableListener
{
    protected array $settings;

    public function __construct(ConfigurationManager $configurationManager)
    {
        $typoScript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $this->settings = $typoScript['module.']['tx_bwbookingmanager.']['settings.'];
    }

    public function modifyRecordActions(ModifyRecordListRecordActionsEvent $event): void
    {
        $table = $event->getTable();
        $row = $event->getRecord();

        if ($table === 'tx_bwbookingmanager_domain_model_entry') {
            $settings = $this->settings;

            $event->removeAction('view');

            if ($settings['showDeleteButton'] === '0') {
                $event->removeAction('delete');
            }

            if ($settings['showHideButton'] === '0') {
                $event->removeAction('hide');
            }

            if ($settings['showSecondaryButton'] === '0') {
                $event->removeAction('secondary');
            }

            if ($settings['showHistoryButton'] === '0') {
                $event->removeAction('history');
            }

            if ($settings['showViewBigButton'] === '0') {
                $event->removeAction('viewBig');
            }

            if ($settings['showEditButton'] === '0') {
                $event->removeAction('edit');
            }

            if ($settings['showConfirmButton'] === '1') {
                $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
                $languageService = $GLOBALS['LANG'];

                if ($row['confirmed']) {
                    $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=0';
                    $btnHtml = '<a class="btn btn-default t3js-record-confirm" data-confirmed="yes" href="#"'
                        . ' data-params="' . htmlspecialchars($params) . '"'
                        . ' data-toggle="tooltip"'
                        . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '"'
                        . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '">'
                        . $iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL)->render() . '</a>';
                } else {
                    $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=1';
                    $btnHtml = '<a class="btn btn-default t3js-record-confirm" data-confirmed="no" href="#"'
                        . ' data-params="' . htmlspecialchars($params) . '"'
                        . ' data-toggle="tooltip"'
                        . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '"'
                        . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '">'
                        . $iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)->render() . '</a>';
                }

                $event->setAction($btnHtml, 'confirmation', 'primary');
            }

            // remove empty action groups
            $actionGroups = array_filter($event->getActions());
            $event->setActions($actionGroups);
        }
    }
}
