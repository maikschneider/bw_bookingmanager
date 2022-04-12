<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Recordlist\RecordList\RecordListHookInterface;

class EntryRecordListHook implements RecordListHookInterface
{
    /**
     * @return array
     */
    private function loadAllTypoScriptSettings()
    {
        $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $typoscript = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        return $typoscript['module.']['tx_bwbookingmanager.']['settings.'];
    }

    /**
     * @param string $table
     * @param array $row
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function makeClip($table, $row, $cells, &$parentObject)
    {
        return $cells;
    }

    /**
     * @param string $table
     * @param array $row
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function makeControl($table, $row, $cells, &$parentObject)
    {
        if ($table === 'tx_bwbookingmanager_domain_model_entry') {
            // remove edit, hide, delete button (no idea why there added twice)
            $cells['primary'] = [];

            $settings = $this->loadAllTypoScriptSettings();

            if ($settings['showDeleteButton'] === '0') {
                unset($cells['delete']);
            }

            if ($settings['showHideButton'] === '0') {
                unset($cells['hide']);
            }

            if ($settings['showSecondaryButton'] == '0') {
                unset($cells['secondary']);
            }

            if ($settings['showHistoryButton'] === '0') {
                unset($cells['history']);
            }

            if ($settings['showViewBigButton'] === '0') {
                unset($cells['viewBig']);
            }

            if ($settings['showEditButton'] === '0') {
                unset($cells['edit']);
            }

            if ($settings['showConfirmButton'] == '1') {
                $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
                $languageService = $GLOBALS['LANG'];

                if ($row['confirmed']) {
                    $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=0';
                    $cells['primary']['confirmed'] = '<a class="btn btn-default t3js-record-confirm" data-confirmed="yes" href="#"'
                        . ' data-params="' . htmlspecialchars($params) . '"'
                        . ' data-toggle="tooltip"'
                        . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '"'
                        . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '">'
                        . $iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL)->render() . '</a>';
                } else {
                    $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=1';
                    $cells['primary']['confirmed'] = '<a class="btn btn-default t3js-record-confirm" data-confirmed="no" href="#"'
                        . ' data-params="' . htmlspecialchars($params) . '"'
                        . ' data-toggle="tooltip"'
                        . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '"'
                        . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '">'
                        . $iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)->render() . '</a>';
                }
            }
        }
        return $cells;
    }

    /**
     * @param string $table
     * @param array $currentIdList
     * @param array $headerColumns
     * @param object $parentObject
     * @return array
     */
    public function renderListHeader($table, $currentIdList, $headerColumns, &$parentObject)
    {
        return $headerColumns;
    }

    /**
     * @param string $table
     * @param array $currentIdList
     * @param array $cells
     * @param object $parentObject
     * @return array
     */
    public function renderListHeaderActions($table, $currentIdList, $cells, &$parentObject)
    {
        return $cells;
    }
}
