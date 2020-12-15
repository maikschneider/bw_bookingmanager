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
        $updateQuery4 = 'select COUNT(*) from tx_bwbookingmanager_calendar_timeslot_mm';
        $result = $connection->executeQuery($updateQuery4);

        $numberOfMigrations = $result->fetchNumeric()[0];

        // abort if no relation
        if ((int)$numberOfMigrations === 0) {
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
}
