<?php

namespace Blueways\BwBookingmanager\Helper;

class DashboardCharts
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Calendar> $calendars
     */
    protected $calendars = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blueways\BwBookingmanager\Domain\Model\Entry> $entries
     */
    protected $entries = null;

    /**
     * @var \DateTime $startDate
     */
    protected $startDate = null;

    /**
     * @var string $view
     */
    protected $view = null;


    public function __construct($calendars, $entries, $startDate, $view)
    {
        $this->calendars = $calendars;
        $this->startDate = $startDate;
        $this->entries = $entries;
        $this->view = $view;
    }

    public function getChart1()
    {
        $charts = [];
        if (sizeof($this->calendars)>1) {
            $charts['calendar-uid-0'] = $this->getChart1Info(false);
        }
        foreach($this->calendars as $key => $calendar){
            $charts['calendar-uid-'.$calendar->getUid()] = $this->getChart1Info($calendar);
        }
        return $charts;
    }

    private function getChart1Info($calendar)
    {
        $ctx = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Jan', 'Feb', 'Marz', 'April'],
                'datasets' => [
                    [
                        'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'borderWidth' => 1,
                        'data' => [2, -4, 12, 4],
                        'label' => 'Dataset 1',
                    ]
                ]
            ],
            'options' => [
                'responsive' => false,
                'scales' => [
                    'yAxes' => [
                        'ticks' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ]
        ];

        return $ctx;
    }

    public static function getDashboardChartUri(string $routeName, array $data): string
    {
        $uriArguments['arguments'] = json_encode($data);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac($uriArguments['arguments'], $routeName);

        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }
}
