<?php

namespace Blueways\BwBookingmanager\Helper;

class DashboardCharts
{

    /**
     * @var Array<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     */
    protected $calendars = null;

    public function __construct($calendars)
    {
        $this->calendars = $calendars;
    }

    public function getOnloadCharts()
    {
        $now = new \DateTime();
        return $this->getCharts($now);
    }

    public function getCharts($startDate)
    {
        $charts = [];
        foreach($this->calendars as $key => $calendar){
            $charts[$key]['chart1'] = $this->getChart1($calendar);
        }
        return $charts;
    }

    private function getChart1($calendar)
    {
        return 'hossa';
    }

    /**
     * @param string $routeName
     * @param array $data
     */
    public static function getDashboardChartUri(array $data): string
    {
        $routeName = 'ajax_dashboard_charts';
        $uriArguments['arguments'] = json_encode($data);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac($uriArguments['arguments'], $routeName);

        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }
}
