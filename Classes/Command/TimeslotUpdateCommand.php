<?php

namespace Blueways\BwBookingmanager\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TimeslotUpdateCommand extends Command
{

    protected function configure()
    {
        $this->setDescription('Updates database relation to calendars and repeat_type of timeslots');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        // check for invalid timeslots
        $errorTimeslots = $this->getTimeslotsWithMultipleRelations();
        if (count($errorTimeslots)) {
            $io->error('There are Timeslots with multiple relations to Calendar!');
            $errorTimeslots = array_map(function ($timeslot) {
                return (string)$timeslot['uid_local'];
            }, $errorTimeslots);
            $io->error('UIDs: ' . implode(',', $errorTimeslots));
            return 0;
        }

        $this->copyCalendarRelationToTimeslots($io);

        $this->migrateRepeatSettings($io);

        return 0;
    }

    protected function getTimeslotsWithMultipleRelations()
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_bwbookingmanager_domain_model_timeslot');
        $qb->select('uid_local')
            ->addSelectLiteral(
                $qb->expr()->count('*', 'count')
            )
            ->from('tx_bwbookingmanager_calendar_timeslot_mm', 'm')
            ->join('m', 'tx_bwbookingmanager_domain_model_timeslot', 't', $qb->expr()->eq('t.uid', 'm.uid_local'))
            ->groupBy('uid_local')
            ->having(
                $qb->expr()->gt('count', 1)
            );

        $timeslots = $qb->execute()->fetchAll();

        return $timeslots;
    }

    protected function copyCalendarRelationToTimeslots(SymfonyStyle $io)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');

        $io->writeln('Checking relations table..');
        $updateQuery4 = "select COUNT(*) from tx_bwbookingmanager_calendar_timeslot_mm";
        $result = $connection->executeQuery($updateQuery4);

        $numberOfMigrations = array_shift($result->fetchAll()[0]);

        // abort if no relation
        if ($numberOfMigrations === 0) {
            $io->note('There are no relations to update, skipping migration');
            return;
        }

        $io->note('There are ' . (string)$numberOfMigrations . ' relations to update');
        $io->writeln('Copy nm relation to timeslot.calendar..');
        $updateQuery = 'update tx_bwbookingmanager_domain_model_timeslot, tx_bwbookingmanager_calendar_timeslot_mm
            set tx_bwbookingmanager_domain_model_timeslot.calendar = tx_bwbookingmanager_calendar_timeslot_mm.uid_foreign
            where tx_bwbookingmanager_domain_model_timeslot.uid = tx_bwbookingmanager_calendar_timeslot_mm.uid_local;';
        $result = $connection->executeQuery($updateQuery);
        $io->writeln('done.');

        $io->writeln('Copy number of relations to calendar.timeslots..');
        $updateQuery2 = 'update tx_bwbookingmanager_domain_model_calendar c
set c.timeslots = (select COUNT(*) from tx_bwbookingmanager_domain_model_timeslot t where c.uid = t.calendar and t.deleted=0);';
        $result = $connection->executeQuery($updateQuery2);
        $io->writeln('done.');

        $io->writeln('Truncate relation table..');
        $updateQuery3 = 'TRUNCATE tx_bwbookingmanager_calendar_timeslot_mm;';
        $result = $connection->executeQuery($updateQuery3);
        $io->writeln('done.');

        $io->success('Relation migration successful');
    }

    protected function migrateRepeatSettings(SymfonyStyle $io)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');

        $io->writeln('Checking for repeating timeslots that can be merged..');

        $updateQuery = "select t1.calendar, time(FROM_UNIXTIME(t1.start_date)) as 'time', t1.uid, count(*) as 'count', group_concat(t1.uid order by t1.start_date) as 'slots'
from tx_bwbookingmanager_domain_model_timeslot t1
where t1.deleted=0 and t1.repeat_type=2
group by t1.calendar, time(FROM_UNIXTIME(t1.start_date)), time(FROM_UNIXTIME(t1.end_date)), t1.max_weight

order by t1.calendar, time(FROM_UNIXTIME(t1.start_date));";

        $result = $connection->executeQuery($updateQuery)->fetchAll();

        if (empty($result)) {
            $io->note('There are no timeslots that can be merged, skipping migration');
            return;
        }

        $io->note('There are ' . (string)count($result) . ' timeslot migrations possible');

        // calculate $timeslotWeekDays => [ 'uid' => xyz, 'weekday' => 16] // weekday is binary value
        $entryWeekDayQuery = "select uid, DAYOFWEEK(FROM_UNIXTIME(start_date)) as 'weekday' from tx_bwbookingmanager_domain_model_timeslot;";
        $timeslotWeekDays = $connection->executeQuery($entryWeekDayQuery)->fetchAll();
        $weekDayMapping = [0, 64, 32, 16, 8, 4, 2, 1];
        $timeslotWeekDays = array_map(function ($timeslot) use ($weekDayMapping) {
            $timeslot['weekday'] = $weekDayMapping[(int)$timeslot['weekday']];
            return $timeslot;
        }, $timeslotWeekDays);

        $entryConditions = '';
        $timeslotRepeatDaysConditions = '';
        $timeslotListToDelete = [];
        $timeslotListforRepeatType4 = [];

        foreach ($result as $migration) {
            $timeslotUids = explode(',', $migration['slots']);

            // calculate repeat repeat_days
            $repeatCode = array_map('intval', $timeslotUids);
            $repeatCode = array_reduce($repeatCode, function ($carry, $timeslotUid) use ($timeslotWeekDays) {
                $key = array_search($timeslotUid, array_column($timeslotWeekDays, 'uid'));
                return $carry += $timeslotWeekDays[$key]['weekday'];
            });

            // divide into main timeslot and timeslots to divide
            $mainTimeslotUid = array_shift($timeslotUids);

            // add to list for setting new repeat_type
            $timeslotListforRepeatType4[] = $mainTimeslotUid;

            // construct list for timeslots to delete
            $timeslotListToDelete = array_merge($timeslotListToDelete, $timeslotUids);

            // update timeslots to new repeat_type and repeat_days
            $timeslotRepeatDaysConditions .= ' WHEN uid=' . $mainTimeslotUid . " THEN " . $repeatCode;

            // re-map entries to new timeslots
            foreach ($timeslotUids as $oldUid) {
                $entryConditions .= ' WHEN timeslot=' . $oldUid . ' THEN ' . $mainTimeslotUid;
            }
        }

        // create and execute the queries
        if ($entryConditions !== "") {
            $entryUpdateQuery = "update tx_bwbookingmanager_domain_model_entry set timeslot = case" . $entryConditions . " else timeslot end;";
            $connection->executeQuery($entryUpdateQuery);
        }

        if (count($timeslotListforRepeatType4)) {
            // concat the lists for in(|) query
            $timeslotListforRepeatType4 = implode(',', $timeslotListforRepeatType4);
            $timeslotUpdateQuery = "update tx_bwbookingmanager_domain_model_timeslot set repeat_type = case when uid in (" . $timeslotListforRepeatType4 . ") then 4 else repeat_type end, repeat_days = case" . $timeslotRepeatDaysConditions . " else repeat_days end;";
            $connection->executeQuery($timeslotUpdateQuery);
        }

        if (count($timeslotListToDelete)) {
            // concat the lists for in(|) query
            $timeslotListToDelete = implode(',', $timeslotListToDelete);
            $timeslotDeleteQuery = "delete from tx_bwbookingmanager_domain_model_timeslot where uid in (" . $timeslotListToDelete . ");";
            $connection->executeQuery($timeslotDeleteQuery);
        }

        $io->success('Repeat migration successfull');
    }
}
