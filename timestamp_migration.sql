ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` ADD `end_date2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` ADD `repeat_end2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` ADD `start_date2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` ADD `end_date2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` ADD `start_date2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` ADD `end_date2` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` ADD `start_date2` INT DEFAULT 0 NOT NULL;

UPDATE `tx_bwbookingmanager_domain_model_timeslot` SET end_date2=UNIX_TIMESTAMP(end_date) WHERE end_date IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_timeslot` SET repeat_end2=UNIX_TIMESTAMP(repeat_end) WHERE repeat_end IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_timeslot` SET start_date2=UNIX_TIMESTAMP(start_date) WHERE start_date IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_entry` SET end_date2=UNIX_TIMESTAMP(end_date) WHERE end_date IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_entry` SET start_date2=UNIX_TIMESTAMP(start_date) WHERE start_date IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_blockslot` SET end_date2=UNIX_TIMESTAMP(end_date) WHERE end_date IS NOT NULL;
UPDATE `tx_bwbookingmanager_domain_model_blockslot` SET start_date2=UNIX_TIMESTAMP(start_date) WHERE start_date IS NOT NULL;

ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` DROP COLUMN `end_date`;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` DROP COLUMN `repeat_end`;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` DROP COLUMN `start_date`;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` DROP COLUMN `end_date`;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` DROP COLUMN `start_date`;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` DROP COLUMN `end_date`;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` DROP COLUMN `start_date`;

ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` CHANGE COLUMN `end_date2` `end_date` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` CHANGE COLUMN `repeat_end2` `repeat_end` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_timeslot` CHANGE COLUMN `start_date2` `start_date` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` CHANGE COLUMN `end_date2` `end_date` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_entry` CHANGE COLUMN `start_date2` `start_date` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` CHANGE COLUMN `end_date2` `end_date` INT DEFAULT 0 NOT NULL;
ALTER TABLE `tx_bwbookingmanager_domain_model_blockslot` CHANGE COLUMN `start_date2` `start_date` INT DEFAULT 0 NOT NULL;
