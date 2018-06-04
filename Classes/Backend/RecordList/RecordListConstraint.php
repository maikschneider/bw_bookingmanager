<?php

namespace Blueways\BwBookingmanager\Backend\RecordList;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $vars = GeneralUtility::_GET('M');
        return $vars === 'web_BwBookingmanagerTxBookingmanagerM1';
    }

    public function extendQuery(array &$parameters, array $arguments)
    {
        // always extend query with start date, use current date as default
        $startDate = new \DateTime('now');
        if (isset($arguments['startDate']) && !empty($arguments['startDate'])) {
            $startDate = $startDate->createFromFormat('d.m.Y', $arguments['startDate']);
        }
        $startDate->setTime(0, 0, 0);
        $parameters['where'][] = "start_date >= '" . $startDate->format('Y-m-d H:i:s') . "'";

        // end date
        if (isset($arguments['endDate']) && !empty($arguments['endDate'])) {
            $endDate = new \DateTime('now');
            $endDate = $endDate->createFromFormat('d.m.Y', $arguments['endDate']);
            $endDate->setTime(23, 59, 59);
            $parameters['where'][] = "end_date <= '" . $endDate->format('Y-m-d H:i:s') . "'";
        }

        // search word
        if (isset($arguments['searchWord']) && !empty($arguments['searchWord'])) {
            $words = GeneralUtility::trimExplode(' ', $arguments['searchWord'], true);
            $fields = ['name', 'prename', 'email', 'street', 'city', 'zip', 'phone'];
            $parameters['where'][] = $this->getDatabaseConnection()->searchQuery($words, $fields, self::TABLE);
        }

        // order
        if (isset($arguments['sortingField'])) {
            $direction = ($arguments['sortingDirection'] === 'asc' || $arguments['sortingDirection'] === 'desc') ? $arguments['sortingDirection'] : '';
            $parameters['orderBy'] = [[$arguments['sortingField'], $direction]];
        }

        // hidden
        $hidden = (int) $arguments['hidden'];
        if ($hidden > 0) {
            if ($hidden === 1) {
                $parameters['where'][] = 'hidden=1';
            } elseif ($hidden === 2) {
                $parameters['where'][] = 'hidden=0';
            }
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
