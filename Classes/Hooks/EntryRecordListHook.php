<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Imaging\Icon;

class EntryRecordListHook implements \TYPO3\CMS\Recordlist\RecordList\RecordListHookInterface
{

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
            unset($cells['hide']);
            unset($cells['delete']);
            unset($cells['secondary']);
            unset($cells['history']);
            unset($cells['viewBig']);

            $cells['primary'] = [];

            $iconFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconFactory::class);
            $languageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Lang\LanguageService::class);

            if ($row['confirmed']) {
                $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=0';
                $cells['primary']['confirmed'] = '<a class="btn btn-default t3js-record-confirm" data-confirmed="yes" href="#"'
                    . ' data-params="' . htmlspecialchars($params) . '"'
                    . ' data-toggle="tooltip"'
                    . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '"'
                    . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '">'
                    . $iconFactory->getIcon('actions-edit-unhide', Icon::SIZE_SMALL)->render() . '</a>';
            } else {
                $params = 'data[' . $table . '][' . $row['uid'] . '][confirmed]=1';
                $cells['primary']['confirmed'] = '<a class="btn btn-default t3js-record-confirm" data-confirmed="no" href="#"'
                    . ' data-params="' . htmlspecialchars($params) . '"'
                    . ' data-toggle="tooltip"'
                    . ' data-toggle-title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.unconfirm') . '"'
                    . ' title="' . $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_be.xlf:administration.recordlist.button.confirm') . '">'
                    . $iconFactory->getIcon('actions-edit-hide', Icon::SIZE_SMALL)->render() . '</a>';
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
