<?php
namespace Blueways\BwBookingmanager\Helper;

class Tca{
    
    public function getTimeslotLabel(&$params, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);
        $newTitle = '';

        $startDate = new \DateTime($record['start_date']);
        $endDate = new \DateTime($record['end_date']);

        $repeatType = $record['repeat_type'];
        switch($repeatType){
            case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY:
                $newTitle .= $startDate->format('l') .', ';
                $newTitle .= $startDate->format('H:m') .' - ';
                $newTitle .= $endDate->format('H:m');
            break;

            default:
                $newTitle .= $startDate->format('d.m.y, H:m') . ' - ';
                $endDateFormat = $startDate->diff($endDate)->days == 0 ? 'H:m' : 'd.m.y, H:m';
                $newTitle .= $endDate->format($endDateFormat);
            break;
        }

        $params['title'] = $newTitle;
        return $params;
    }
}