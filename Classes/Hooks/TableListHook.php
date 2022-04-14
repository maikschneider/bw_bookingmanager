<?php

namespace Blueways\BwBookingmanager\Hooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook in database query for Entry records display
 * use Demand and RecordlistConstraint to filter the default values and values submited through search form
 */
class TableListHook
{
    public function modifyQuery(
        array &$parameters,
        string $table,
        int $pageId,
        array $additionalConstraints,
        array $fieldList,
        QueryBuilder $queryBuilder
    ) {
        $isBookingManagerRoute = GeneralUtility::_GET('route') === '/bookingmanager/entry/list' || GeneralUtility::_GET('route') === '/module/web/bookingmanager';
        if ($isBookingManagerRoute) {

            // Fix for TYPO3 v9+: After the where parameter gets extended, the default parameter for storagePid gets lost
            $parameters['where'][] = 'pid=' . $pageId;

            $demands = [];
            $vars = GeneralUtility::_GET('demand');
            if (is_array($vars)) {
                $demands = $vars;
            }

            $this->extendQuery($parameters, $demands);

//            if ($demands && isset($demands['hidden']) && (int)$demands['hidden'] === 1) {
//                $queryBuilder
//                    ->getRestrictions()
//                    ->removeAll()
//                    ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
//            }

            if (isset($parameters['orderBy'][0])) {
                $queryBuilder->orderBy($parameters['orderBy'][0][0], $parameters['orderBy'][0][1]);
                unset($parameters['orderBy']);
            }
            if (!empty($parameters['whereDoctrine'])) {
                $queryBuilder->andWhere(...$parameters['whereDoctrine']);
                unset($parameters['where']);
            }
        }
    }

    public function extendQuery(array &$parameters, array $arguments)
    {
        $parameters['whereDoctrine'] = [];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_bwbookingmanager_domain_model_entry');
        $expressionBuilder = $queryBuilder->expr();

        // always extend query with start date, use current date as default
        $startDate = new \DateTime('now');
        if (isset($arguments['startDate']) && !empty($arguments['startDate'])) {
            $startDate = $startDate->createFromFormat('d.m.Y', $arguments['startDate']);
        }
        $startDate->setTime(0, 0, 0);
        $parameters['where'][] = "start_date >= '" . $startDate->getTimestamp() . "'";
        $parameters['whereDoctrine'][] = $expressionBuilder->gte('start_date', $startDate->getTimestamp());

        // end date
        if (isset($arguments['endDate']) && !empty($arguments['endDate'])) {
            $endDate = new \DateTime('now');
            $endDate = $endDate->createFromFormat('d.m.Y', $arguments['endDate']);
            $endDate->setTime(23, 59, 59);
            $parameters['where'][] = "end_date <= '" . $endDate->getTimestamp() . "'";
            $parameters['whereDoctrine'][] = $expressionBuilder->lte('end_date', $endDate->getTimestamp());
        }

        // search word
        if (isset($arguments['searchWord']) && !empty($arguments['searchWord'])) {
            $words = GeneralUtility::trimExplode(' ', $arguments['searchWord'], true);
            $fields = ['name', 'prename', 'email', 'street', 'city', 'zip', 'phone'];
            $fieldParts = [];
            foreach ($fields as $field) {
                $likeParts = [];
                $nameParts = str_getcsv($arguments['searchWord'], ' ');
                foreach ($nameParts as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $likeParts[] = $expressionBuilder->like(
                            $field,
                            $queryBuilder->quote('%' . $queryBuilder->escapeLikeWildcards($part) . '%')
                        );
                    }
                }
                if (!empty($likeParts)) {
                    $fieldParts[] = $expressionBuilder->orX(...$likeParts);
                }
            }
            $parameters['whereDoctrine'][] = $expressionBuilder->orX(...$fieldParts);
            $parameters['where'][] = $expressionBuilder->orX(...$fieldParts);
        }

        // order (default: start date)
        $parameters['orderBy'] = [['start_date', 'asc']];
        if (isset($arguments['sortingField'])) {
            $direction = ($arguments['sortingDirection'] === 'asc' || $arguments['sortingDirection'] === 'desc') ? $arguments['sortingDirection'] : '';
            $parameters['orderBy'] = [[$arguments['sortingField'], $direction]];
        }

        // confirmation
        $showConfirmed = (int)$arguments['showConfirmed'];
        if (isset($arguments['showConfirmed']) && $showConfirmed === 0) {
            $parameters['where'][] = 'confirmed=0';
            $parameters['whereDoctrine'][] = $expressionBuilder->eq('confirmed', 0);
        }
        $showUnconfirmed = (int)$arguments['showUnconfirmed'];
        if (isset($arguments['showUnconfirmed']) && $showUnconfirmed === 0) {
            $parameters['where'][] = 'confirmed=1';
            $parameters['whereDoctrine'][] = $expressionBuilder->eq('confirmed', 1);
        }

        // calendar
        if (isset($arguments['calendarUid']) && $arguments['calendarUid'] !== '0') {
            $parameters['where'][] = 'calendar=' . $arguments['calendarUid'];
            $parameters['whereDoctrine'][] = $expressionBuilder->eq('calendar', $arguments['calendarUid']);
        }
    }
}
