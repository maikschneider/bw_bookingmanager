<?php

namespace Blueways\BwBookingmanager\Backend\RecordList;

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
            $startDate = $startDate->createFromFormat('d.m.Y',  $arguments['startDate']); 
        }
        $startDate->setTime(0,0,0);
        $parameters['where'][] = "start_date >= '" . $startDate->format('Y-m-d H:i:s') . "'";


    }
}
