<?php
namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Backend\RecordList\RecordListConstraint;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\RecordList\AbstractDatabaseRecordList;

/**
 * Hook in database query for Entry records display
 * use Demand and RecordlistConstraint to filter the default values and values submited through search form
 *
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
        if ($table === $this->recordListConstraint::TABLE && $this->recordListConstraint->isInAdministrationModule()) {
            $demands = [];
            $vars = GeneralUtility::_GET('tx_bwbookingmanager_web_bwbookingmanagertxbookingmanagerm1');
            if (is_array($vars) && is_array($vars['demand'])) {
                $demands = $vars['demand'];
            }
            $this->recordListConstraint->extendQuery($parameters, $demands);
        }
    }
}
