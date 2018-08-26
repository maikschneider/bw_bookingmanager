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

    /**
     * @var array $calendarColors
     */
    protected $calendarColors = null;


    public function __construct($calendars, $entries, $startDate, $view)
    {
        $this->calendars = $calendars;
        $this->startDate = $startDate;
        $this->entries = $entries;
        $this->view = $view;

        $this->generateCalendarColors();
    }

    public function getChart1()
    {
        $charts = [];
        if (sizeof($this->calendars)>1) {
            $charts['calendar-uid-0'] = $this->getChart1Ctx($this->calendars);
        }
        foreach($this->calendars as $key => $calendar){
            $charts['calendar-uid-'.$calendar->getUid()] = $this->getChart1Ctx([$calendar]);
        }
        return $charts;
    }

    private function getChart1Ctx($calendars)
    {
        $ctx = [
            'type' => 'bar',
            'data' => [
                'labels' => $this->getChart1Labels(),
                'datasets' => $this->getChart1Datasets($calendars),
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

    private function getChart1Datasets($calendars)
    {
        $datasets = [];
        foreach ($calendars as $key => $calendar) {

            $colorKey = 0;

            $datasets[] = [
                'backgroundColor' => 'rgba(' . $this->calendarColors[$calendar->getUid()] . ', 0.5)',
                'borderColor' => 'rgb(' . $this->calendarColors[$calendar->getUid()] . ')',
                'borderWidth' => 1,
                'data' => $this->getChart1Data($calendar),
                'label' => $calendar->getName(),
            ];

        }
        return $datasets;
    }

    private function getChart1Data($calendar)
    {
        $data = [];

        if ($this->view === 'year') {

            $data = array_fill(0, 11, 0);

            foreach($this->entries as $entry) {

                if($entry->getCalendar()->getUid() !== $calendar->getUid()){
                    continue;
                }

                $yearOffset = $entry->getStartDate()->format('n') - 1;
                $data[$yearOffset]++;
            }

        }

        return $data;
    }

    private function getChart1Labels()
    {
        $labels = [];
        $languageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Lang\LanguageService::class);

        if ($this->view === 'year') {
            for ($i = 1; $i < 13; $i++) {
                $labels[] = $languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.monthNames.short.' . $i);
            }
        }

        if($this->view === 'month') {
            $labels = [];
        }

        return $labels;
    }

    public static function getDashboardChartUri(string $routeName, array $data): string
    {
        $uriArguments['arguments'] = json_encode($data);
        $uriArguments['signature'] = \TYPO3\CMS\Core\Utility\GeneralUtility::hmac($uriArguments['arguments'], $routeName);

        $uriBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }

    public function generateCalendarColors()
    {
        $colors = [
            '255, 99, 132',
            '255, 159, 64',
            '255, 205, 86',
            '75, 192, 192',
            '54, 162, 235',
            '153, 102, 255',
            '201, 203, 207'
        ];

        foreach ($this->calendars as $key => $calendar){
            $this->calendarColors[$calendar->getUid()] = $colors[$key];
        }
    }
}
