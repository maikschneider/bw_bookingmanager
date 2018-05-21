<?php
namespace Blueways\BwBookingmanager\Hooks;

class Entry24hValidationHook
{
    const HOOK_LABEL = 'LLL:EXT:bw_bookingmanager/Resources/Private/Language/locallang_db.xlf:tx_bwbookingmanager_domain_model_timeslot.validation_hooks.24h';

    /**
     * @var Blueways\BwBookingmanager\Domain\Validator\EntryValidator $entryValidator
     */
    protected $entryValidator;

    /**
     * @var Blueways\BwBookingmanager\Domain\Validator\EntryValidator $entryValidator
     */
    public function executeHook($entryValidator)
    {
        $this->entryValidator = $entryValidator;
        
        $this->checkDate();
    }

    private function checkDate()
    {
        $now = new \DateTime('now');
        // if..
        //$this->entryValidator->addValidationHookError('date passt niht', 1313131);
    }

}
