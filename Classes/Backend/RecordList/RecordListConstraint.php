<?php

namespace Blueways\BwBookingmanager\Backend\RecordList;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class RecordListConstraint
{

    const TABLE = 'tx_bwbookingmanager_domain_model_entry';

    /**
     * Check if current module is the news administration module
     *
     * @return bool
     */
    public function isInAdministrationModule()
    {
        if (self::is9Up()) {
            return GeneralUtility::_GET('route') === '/web/bwbookingmanager/';
        }

        return GeneralUtility::_GET('M') === 'web_BwBookingmanagerTxBookingmanagerM1';
    }

    /**
     * @return bool
     */
    private static function is9up(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getVersion()) >= 9000000;
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

        // end date
        if (isset($arguments['endDate']) && !empty($arguments['endDate'])) {
            $endDate = new \DateTime('now');
            $endDate = $endDate->createFromFormat('d.m.Y', $arguments['endDate']);
            $endDate->setTime(23, 59, 59);
            $parameters['where'][] = "end_date <= '" . $endDate->getTimestamp() . "'";
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

        // hidden
        $hidden = (int)$arguments['hidden'];
        if ($hidden > 0) {
            if ($hidden === 1) {
                $parameters['where'][] = 'hidden=1';
            } elseif ($hidden === 2) {
                $parameters['where'][] = 'hidden=0';
            }
        }

        // confirmation
        $showConfirmed = (int)$arguments['showConfirmed'];
        if (isset($arguments['showConfirmed']) && $showConfirmed === 0) {
            $parameters['where'][] = 'confirmed=0';
        }
        $showUnconfirmed = (int)$arguments['showUnconfirmed'];
        if (isset($arguments['showUnconfirmed']) && $showUnconfirmed === 0) {
            $parameters['where'][] = 'confirmed=1';
        }

        // calendar
        if (isset($arguments['calendarUid']) && $arguments['calendarUid'] !== '0') {
            $parameters['where'][] = "calendar=" . $arguments['calendarUid'];
        }
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
