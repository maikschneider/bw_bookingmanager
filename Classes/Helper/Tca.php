<?php
namespace Blueways\BwBookingmanager\Helper;

class Tca
{
    public function getTimeslotLabel(&$params, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);
        $newTitle = '';

        $startDate = new \DateTime(date('Y-m-d H:i:sP', $record['start_date']));
        $endDate = new \DateTime(date('Y-m-d H:i:sP', $record['end_date']));
        $repeatEnd = new \DateTime(date('Y-m-d H:i:sP', $record['repeat_end']));

        $repeatType = $record['repeat_type'];
        switch ($repeatType) {
        case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY:
            $newTitle .= $startDate->format('l') . ', ';
            $newTitle .= $startDate->format('H:i') . ' - ';
            $newTitle .= $endDate->format('H:i');
            $repeatEnd = $record['repeat_end'] ? $repeatEnd->format('d.m.y') : '∞';
            $newTitle .= ' (' . $startDate->format('d.m.y') . ' - ' . $repeatEnd . ')';
            break;

        case \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_DAILY:
            $newTitle .= $startDate->format('H:i') . ' - ' . $endDate->format('H:i');
            $repeatEnd = $record['repeat_end'] ? $repeatEnd->format('d.m.y') : '∞';
            $newTitle .= ' (' . $startDate->format('d.m.y') . ' - ' . $repeatEnd . ')';
            break;

        default:
            $newTitle .= $startDate->format('d.m.y, H:i') . ' - ';
            $endDateFormat = $startDate->diff($endDate)->days == 0 ? 'H:i' : 'd.m.y, H:i';
            $newTitle .= $endDate->format($endDateFormat);
            break;
        }

        $params['title'] = $newTitle;
        return $params;
    }

    public function getBlockslotLabel(&$params, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);

        $startDate = new \DateTime(date('Y-m-d H:i:sP', $record['start_date']));
        $endDate = new \DateTime(date('Y-m-d H:i:sP', $record['end_date']));

        $newTitle = $record['reason'] . ' (' . $startDate->format('d.m.y') . ' - ' . $endDate->format('d.m.y') . ')';

        $params['title'] = $newTitle;
        return $params;
    }

    public function getHolidayLabel(&$params, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);

        $startDate = new \DateTime(date('Y-m-d H:i:sP', $record['start_date']));
        $endDate = new \DateTime(date('Y-m-d H:i:sP', $record['end_date']));

        $newTitle = $record['name'] . ' (' . $startDate->format('d.m.y') . ' - ' . $endDate->format('d.m.y') . ')';

        $params['title'] = $newTitle;
        return $params;
    }

    public function getEntryLabel(&$params, $parentObject)
    {
        $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($params['table'], $params['row']['uid']);
        $newTitle = '';

        $startDate = new \DateTime(date('Y-m-d H:i:sP', $record['start_date']));
        $endDate = new \DateTime(date('Y-m-d H:i:sP', $record['end_date']));


        $newTitle .= $startDate->format('d.m.y, H:i') . '-';
        $endDateFormat = $startDate->diff($endDate)->days == 0 ? 'H:i' : 'd.m.y, H:i';
        $newTitle .= $endDate->format($endDateFormat);

        $params['title'] = $newTitle;
        return $params;
    }

}
