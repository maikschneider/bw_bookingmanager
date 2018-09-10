<?php

namespace Blueways\BwBookingmanager\ViewHelpers\Be;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BetterTableListViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\TableListViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('noControlPanels', 'bool', 'show hide, delete, edit buttons in row', true, true);
    }

    /**
     * Renders a record list as known from the TYPO3 list module
     * Note: This feature is experimental!
     *
     * @return string the rendered record list
     * @see \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
     */
    public function render()
    {
        $tableName = $this->arguments['tableName'];
        $fieldList = $this->arguments['fieldList'];
        $storagePid = $this->arguments['storagePid'];
        $levels = $this->arguments['levels'];
        $filter = $this->arguments['filter'];
        $recordsPerPage = $this->arguments['recordsPerPage'];
        $sortField = $this->arguments['sortField'];
        $sortDescending = $this->arguments['sortDescending'];
        $readOnly = $this->arguments['readOnly'];
        $enableClickMenu = $this->arguments['enableClickMenu'];
        $clickTitleMode = $this->arguments['clickTitleMode'];
        $noControlPanels = $this->arguments['noControlPanels'];

        $this->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Recordlist/Recordlist');

        $pageinfo = BackendUtility::readPageAccess(GeneralUtility::_GP('id'), $GLOBALS['BE_USER']->getPagePermsClause(1));
        /** @var $dblist \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList */
        $dblist = GeneralUtility::makeInstance(\TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class);
        $dblist->pageRow = $pageinfo;
        if ($readOnly === false) {
            $dblist->calcPerms = $GLOBALS['BE_USER']->calcPerms($pageinfo);
        }
        $dblist->showClipboard = false;
        $dblist->disableSingleTableView = true;
        $dblist->clickTitleMode = $clickTitleMode;
        $dblist->clickMenuEnabled = $enableClickMenu;
        if ($storagePid === null) {
            $frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $storagePid = $frameworkConfiguration['persistence']['storagePid'];
        }
        $dblist->start($storagePid, $tableName, (int)GeneralUtility::_GP('pointer'), $filter, $levels, $recordsPerPage);
        $dblist->allFields = true;
        $dblist->dontShowClipControlPanels = true;
        $dblist->displayFields = false;
        $dblist->setFields = [$tableName => $fieldList];
        $dblist->noControlPanels = $noControlPanels;
        $dblist->sortField = $sortField;
        $dblist->sortRev = $sortDescending;
        $dblist->script = $_SERVER['REQUEST_URI'];
        $dblist->generateList();

        $js = 'var T3_THIS_LOCATION = ' . GeneralUtility::quoteJSvalue(rawurlencode(GeneralUtility::getIndpEnv('REQUEST_URI')));
        $html = GeneralUtility::wrapJS($js) . $dblist->HTMLcode;

        return $html;
    }
}
