# 1. Timeslots bearbeiten
## Timeslots löschen (27)
UPDATE tx_bwbookingmanager_domain_model_timeslot SET deleted=1 WHERE uid IN (8,4,5,10,15,17,22,23,24,25,26,27,28,30,31,34,35,36,37,38,39,42,43,44,45,46,47) AND pid=191;
## repeat_end entfernen
UPDATE tx_bwbookingmanager_domain_model_timeslot SET repeat_end=0 WHERE uid IN (9,16,18,19,11,12,13,14,40,41) AND pid=191;
## ferienverhalten setzen
UPDATE tx_bwbookingmanager_domain_model_timeslot SET holiday_setting=1 WHERE uid IN (9,16,18,19,11,12,13,14) AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_timeslot SET holiday_setting=2 WHERE uid IN (40,41) AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_timeslot SET repeat_type=1 WHERE uid IN (40,41) AND pid=191;

# 2. Entries neu zuordnen
## alle tage ausserhalb ferien (Di-So)
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=9 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=1 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=16 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=2 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=18 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=3 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=19 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=4 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=11 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=5 AND HOUR(FROM_UNIXTIME(start_date))=10 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=12 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=5 AND HOUR(FROM_UNIXTIME(start_date))=15 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=13 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=6 AND HOUR(FROM_UNIXTIME(start_date))=10 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=14 WHERE WEEKDAY(FROM_UNIXTIME(start_date))=6 AND HOUR(FROM_UNIXTIME(start_date))=15 AND pid=191;

## herbstferien
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=40 WHERE FROM_UNIXTIME(start_date)>DATE('2018-10-01 00:00:00') AND FROM_UNIXTIME(start_date)<DATE('2018-10-12 23:59:59') AND HOUR(FROM_UNIXTIME(start_date))=10 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=41 WHERE FROM_UNIXTIME(start_date)>DATE('2018-10-01 00:00:00') AND FROM_UNIXTIME(start_date)<DATE('2018-10-12 23:59:59') AND HOUR(FROM_UNIXTIME(start_date))=15;

## winterferien
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=40 WHERE FROM_UNIXTIME(start_date)>DATE('2018-12-19 00:00:00') AND FROM_UNIXTIME(start_date)<DATE('2019-01-04 23:59:59') AND HOUR(FROM_UNIXTIME(start_date))=10 AND pid=191;
UPDATE tx_bwbookingmanager_domain_model_entry SET timeslot=41 WHERE FROM_UNIXTIME(start_date)>DATE('2018-02-19 00:00:00') AND FROM_UNIXTIME(start_date)<DATE('2018-01-04 23:59:59') AND HOUR(FROM_UNIXTIME(start_date))=15 AND pid=191;
