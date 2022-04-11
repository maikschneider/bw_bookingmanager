<?php
namespace Blueways\BwBookingmanager\Hooks;

use Blueways\BwBookingmanager\Domain\Model\Timeslot;
class TimeslotIsBookableHook
{
    const HOOK_LABEL = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.is_bookable_hooks.24h';

    /**
     * @var Timeslot
     */
    protected $timeslot;

    /**
     * @var Timeslot $timeslot
     */
    public function isBookable($timeslot)
    {
        $this->timeslot = $timeslot;
        
        return $this->checkDate();
    }

    private function checkDate()
    {
        $now = new \DateTime('now');

        $startDate = clone $this->timeslot->getStartDate();
        $startDate->setTime(0, 0, 0);

        if ($startDate < $now) {
            return false;
        }
        
        return true;
    }
}
