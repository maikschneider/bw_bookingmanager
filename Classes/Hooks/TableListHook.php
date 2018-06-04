<?php
namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Recordlist\RecordList\AbstractDatabaseRecordList;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class TableListHook
{

    public function buildQueryParametersPostProcess(
        array &$parameters,
        string $table,
        int $pageId,
        array $additionalConstraints,
        array $fieldList,
        AbstractDatabaseRecordList $parentObject
    ) {
        if ($table === 'tx_bwbookingmanager_domain_model_entry') {

            // $parameters['where'][] = 'start_date>...';
            // var_dump($parameters);


        }
    }
}
