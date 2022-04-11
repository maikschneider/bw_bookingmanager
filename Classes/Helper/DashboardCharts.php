<?php

namespace Blueways\BwBookingmanager\Helper;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Entry;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;
class DashboardCharts
{

    /**
     * @var ObjectStorage<Calendar> $calendars
     */
    protected $calendars = null;

    /**
     * @var ObjectStorage<Entry> $entries
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

    /**
     * @var LanguageService $languageService
     */
    protected $languageService = null;


    public function __construct($calendars, $entries, $startDate, $view)
    {
        $this->calendars = $calendars;
        $this->startDate = $startDate;
        $this->entries = $entries;
        $this->view = $view;

        $this->generateCalendarColors();
        $this->languageService = GeneralUtility::makeInstance(LanguageService::class);
    }

    public function getChart1()
    {
        $charts = [];
        if (sizeof($this->calendars) > 1) {
            $charts['calendar-uid-0'] = $this->getChart1Ctx($this->calendars);
        }
        foreach ($this->calendars as $key => $calendar) {
            $charts['calendar-uid-' . $calendar->getUid()] = $this->getChart1Ctx([$calendar]);
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
                    'yAxes' => [[
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => 'Bookings',
                            'fontStyle' => 'bold'
                        ],
                        'ticks' => [
                            'beginAtZero' => true
                        ]]
                    ],
                    'xAxes' => [[
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => $this->getXLabel(),
                            'fontStyle' => 'bold'
                        ],
                        'ticks' => [
                            'beginAtZero' => true
                        ]]
                    ],
                ],
                //'events' => ['click'],
                'onClick' => ''
            ]
        ];

        return $ctx;
    }

    // @TODO: label via languageservice
    private function getXLabel()
    {
        if($this->view === 'year'){
            return $this->startDate->format('Y');
        }
        if($this->view === 'month'){
            return $this->startDate->format('F Y');
        }
        if($this->view === 'week'){
            $endDate = clone $this->startDate;
            $endDate->modify('+6 days');
            return 'Kalenderwoche '.$this->startDate->format('W').' ('.$this->startDate->format('d.m').'-'. $endDate->format('d.m').')';
        }
    }

    private function getChart1Datasets($calendars)
    {
        $datasets = [];
        foreach ($calendars as $key => $calendar) {

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

            foreach ($this->entries as $entry) {

                if ($entry->getCalendar()->getUid() !== $calendar->getUid()) {
                    continue;
                }

                $yearOffset = $entry->getStartDate()->format('n') - 1;
                $data[$yearOffset]++;
            }
        }

        if ($this->view === 'month') {

            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->startDate->format('n'), $this->startDate->format('Y'));
            $data = array_fill(0, $daysInMonth, 0);

            foreach ($this->entries as $entry) {

                if ($entry->getCalendar()->getUid() !== $calendar->getUid()) {
                    continue;
                }

                $monthOffset = $entry->getStartDate()->format('j') - 1;
                $data[$monthOffset]++;
            }
        }

        if ($this->view === 'week') {

            $data = array_fill(0, 7, 0);

            foreach ($this->entries as $entry) {

                if ($entry->getCalendar()->getUid() !== $calendar->getUid()) {
                    continue;
                }

                $dayOffset = ($entry->getStartDate()->format('w') + 6) % 7;
                $data[$dayOffset]++;
            }
        }

        return $data;
    }

    private function getChart1Labels()
    {
        $labels = [];

        if ($this->view === 'year') {
            for ($i = 1; $i < 13; $i++) {
                $labels[] = $this->languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.monthNames.short.' . $i);
            }
        }

        if ($this->view === 'month') {
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->startDate->format('n'), $this->startDate->format('Y'));
            $labels = range(1, $daysInMonth);
        }

        if ($this->view === 'week') {
            for ($i = 1; $i < 8; $i++) {
                $labels[] = $this->languageService->sL('LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:date.dayNames.short.' . $i);
            }
        }

        return $labels;
    }

    public function getDashboardChartUri(string $routeName, array $data): string
    {
        $uriArguments['arguments'] = json_encode($data);
        $uriArguments['signature'] = GeneralUtility::hmac($uriArguments['arguments'],
            $routeName);

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($routeName, $uriArguments);
    }

    public static function getStaticDashboardChartUri(string $routeName, array $data): string
    {
        $uriArguments['arguments'] = json_encode($data);
        $uriArguments['signature'] = GeneralUtility::hmac($uriArguments['arguments'],
            $routeName);

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
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

        foreach ($this->calendars as $key => $calendar) {
            $this->calendarColors[$calendar->getUid()] = $colors[$key];
        }
    }
}
