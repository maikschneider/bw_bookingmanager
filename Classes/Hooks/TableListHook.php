<?php

namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Backend\RecordList\RecordListConstraint;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook in database query for Entry records display
 * use Demand and RecordlistConstraint to filter the default values and values submited through search form
 */
class TableListHook
{

    /** @var RecordListConstraint */
    protected $recordListConstraint;

    public function __construct()
    {
        $this->recordListConstraint = GeneralUtility::makeInstance(RecordListConstraint::class);
    }

    public function modifyQuery(
        array &$parameters,
        string $table,
        int $pageId,
        array $additionalConstraints,
        array $fieldList,
        QueryBuilder $queryBuilder
    ) {
        $isBookingManagerRoute = GeneralUtility::_GET('route') === '/bookingmanager/entry/list' || GeneralUtility::_GET('route') === '/web/bwbookingmanager/';
        if ($isBookingManagerRoute && $table === $this->recordListConstraint::TABLE) {

            // Fix for TYPO3 v9+: After the where parameter gets extended, the default parameter for storagePid gets lost
            $parameters['where'][] = 'pid=' . $pageId;

            $demands = [];
            $vars = GeneralUtility::_GET('demand');
            if (is_array($vars)) {
                $demands = $vars;
            }
            $this->recordListConstraint->extendQuery($parameters, $demands);
        }
    }
}
