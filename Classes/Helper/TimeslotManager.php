<?php
namespace Blueways\BwBookingmanager\Helper;

/**
 * This class oganizes the correct arrangement of timeslots 
 */
class TimeslotManager
{
    /**
     * @var Array<\Blueways\BwBookingmanager\Domain\Model\Timeslot> $timeslots
     */
    protected $timeslots = null;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     */
    protected $calendar = null;

    /**
     * @var \DateTime $startDate
     */
    protected $startDate = null;

    /**
     * @var \DateTime $endDate
     */
    protected $endDate = null;

    /**
     * __construct
     */
    public function __construct(
        $timeslots, 
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate, 
        \DateTime $endDate
        )
    {
        $this->timeslots = $timeslots;
        $this->calendar = $calendar;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * starts all organising actions and returns the finished timeslots
     */
    public function getTimeslots()
    {
        $this->repeatTimeslots();
        $this->filterTimeslots();

        return $this->timeslots;
    }

    /**
     * checks every slot for type of repeat and merges duplicated slots back
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $timeslots
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return void
     */
    public function repeatTimeslots()
    {
        $timeslots = $this->timeslots->toArray();
        $newTimeslots = [];
        foreach ($timeslots as $timeslot) {
            $repeatType = $timeslot->getRepeatType();
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_DAILY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatDailyTimeslot($timeslot));
            }
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_WEEKLY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatWeeklyTimeslot($timeslot));
            }
            if($repeatType == \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_MONTHLY){
                $newTimeslots = array_merge($newTimeslots, $this->repeatMonthlyTimeslot($timeslot));
            }
        }

        $this->timeslots = array_merge($timeslots, $newTimeslots);
    }

    /**
     * removes slots that are in the past and overlayed by a blockslot
     */
    private function filterTimeslots()
    {
        $blockslots = $this->calendar->getBlockslots();

        foreach ($this->calendar->getBlockslots() as $blockslot) {

            $blockStartDate = $blockslot->getStartDate();
            $blockEndDate = $blockslot->getEndDate();

            if(
                ($blockStartDate <= $this->startDate && $blockEndDate > $this->startDate) ||
                ($blockStartDate >= $this->startDate && $blockStartDate < $this->endDate)
                ){
                var_dump('Block greift!');
            }
        }
    }

    /**
     * duplicates daily timeslot for whole date range
     */
    private function repeatDailyTimeslot($timeslot)
    {
        $newTimeslots = [];

        // default fill the whole date range with that timeslot
        $daysToFillTimeslots = $this->endDate->diff($this->startDate)->days + 1;
        $dateToStartFilling = $this->startDate;

        // create new timeslots and modify start and end date
        for($i=0; $i<$daysToFillTimeslots; $i++){
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+'.$i.' days');
            $newEndDate = clone $dateToStartFilling;
            $newEndDate->modify('+'.$i.' days');

            // dont add new timeslot if placed before actual timeslot or even at same time
            if($newStartDate <= $timeslot->getStartDate()){
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate){
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);

            $newTimeslots[] = $newTimeslot;
        }

        return $newTimeslots;
    }

    /**
     * Counts the occurences of a day of week in a date range
     * credits to this crazy motherfucker: https://stackoverflow.com/questions/20068975/count-the-no-of-fridays-or-any-between-two-specific-dates/20071461#20071461
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $dayOfWeek
     */
    private function dayCount($from, $to, $day) {

        $wF = $from->format('w');
        $wT = $to->format('w');
        if ($wF < $wT)      $isExtraDay = $day >= $wF && $day <= $wT;
        elseif ($wF == $wT) $isExtraDay = $wF == $day;
        else                $isExtraDay = $day >= $wF || $day <= $wT;

        return floor($from->diff($to)->days / 7) + $isExtraDay;
    }

    /**
     * duplicates weekly timeslots in date range
     */
    private function repeatWeeklyTimeslot($timeslot)
    {
        $newTimeslots = [];

        // default fill the all mondays (or tuesdays..) of date range
        $daysToFillTimeslots = $this->dayCount($this->startDate, $this->endDate, $timeslot->getStartDate()->format('w'));
        $dateToStartFilling = clone $this->startDate;
        $dateToStartFilling->modify('-1 days');
        $dateToStartFilling->modify('next '.$timeslot->getStartDate()->format('l'));

        for($i=0; $i<$daysToFillTimeslots; $i++){
            $newStartDate = clone $dateToStartFilling;
            $newStartDate->modify('+'.$i.' weeks');
            $newEndDate = clone $dateToStartFilling;
            $newEndDate->modify('+'.$i.' weeks');

            // dont add new timeslot if placed before actual timeslot or even at same time
            if($newStartDate <= $timeslot->getStartDate()){
                continue;
            }
            // dont add new timeslot if repeat end date is reached
            if($timeslot->getRepeatEnd() && $timeslot->getRepeatEnd() <= $newStartDate){
                continue;
            }

            $newTimeslot = clone $timeslot;
            $newTimeslot->setStartDate($newStartDate);
            $newTimeslot->setEndDate($newEndDate);

            $newTimeslots[] = $newTimeslot;
        }

        return $newTimeslots;
    }

    /**
     * @TODO: implement
     */
    private function repeatMonthlyTimeslot($timeslot)
    {
        return [];
    }

}