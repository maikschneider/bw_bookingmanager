<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use DateTime;

class DstFixUtility
{
    public static function adjustEntryDates(Entry &$entry)
    {
        $timezone = (new DateTime())->getTimezone();
        $timezoneOffset = (new DateTime($entry->getStartDate()->format('D jS M y'), $timezone))->getOffset();

        if (!$timezoneOffset) {
            return;
        }

        $entry->getStartDate()->modify('- ' . ($timezoneOffset . 'seconds'));
        $entry->getEndDate()->modify('- ' . ($timezoneOffset . 'seconds'));
    }
}
