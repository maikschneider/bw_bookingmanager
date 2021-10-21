#
# Table structure for table 'tx_bwbookingmanager_domain_model_calendar'
#
CREATE TABLE tx_bwbookingmanager_domain_model_calendar (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	record_type varchar(255) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	timeslots int(11) unsigned DEFAULT '0' NOT NULL,
	blockslots int(11) unsigned DEFAULT '0' NOT NULL,
	holidays int(11) unsigned DEFAULT '0' NOT NULL,
	notifications int(11) unsigned DEFAULT '0' NOT NULL,
	direct_booking smallint(5) unsigned DEFAULT '0' NOT NULL,
	entries int(11) unsigned DEFAULT '0' NOT NULL,
	icss int(11) unsigned DEFAULT '0' NOT NULL,
	default_start_time int(11) unsigned DEFAULT '0' NOT NULL,
	default_end_time int(11) unsigned DEFAULT '0' NOT NULL,
	min_length int(11) unsigned DEFAULT '0' NOT NULL,
	min_offset int(11) unsigned DEFAULT '0' NOT NULL,
	color varchar(10) default '' not null,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_timeslot'
#
CREATE TABLE tx_bwbookingmanager_domain_model_timeslot (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,
	repeat_type int(11) DEFAULT '0' NOT NULL,
	repeat_days int(11) DEFAULT '0' NOT NULL,
	max_weight int(11) DEFAULT '0' NOT NULL,
	is_bookable_hooks int(11) DEFAULT '0' NOT NULL,
	holiday_setting int(11) DEFAULT '0' NOT NULL,
	entries int(11) unsigned DEFAULT '0' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,
	calendar int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_end int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_entry'
#
CREATE TABLE tx_bwbookingmanager_domain_model_entry (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	timeslot int(11) unsigned DEFAULT '0' NOT NULL,
	calendar int(11) unsigned DEFAULT '0' NOT NULL,

	record_type varchar(255) DEFAULT '' NOT NULL,
	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	prename varchar(255) DEFAULT '' NOT NULL,
	street varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	token varchar(255) DEFAULT '' NOT NULL,
	newsletter smallint(5) unsigned DEFAULT '0' NOT NULL,
	confirmed smallint(5) unsigned DEFAULT '0' NOT NULL,
	special1 smallint(5) unsigned DEFAULT '0' NOT NULL,
	special2 smallint(5) unsigned DEFAULT '0' NOT NULL,
	weight int(11) DEFAULT '0' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	gender smallint(5) unsigned default '0' not null,
	notes text,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_notification'
#
CREATE TABLE tx_bwbookingmanager_domain_model_notification (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	hook varchar(255) DEFAULT '' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,
	event int(11) unsigned DEFAULT '0' NOT NULL,
	template varchar(255) DEFAULT '' NOT NULL,
	email_subject varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_blockslot'
#
CREATE TABLE tx_bwbookingmanager_domain_model_blockslot (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,
	reason varchar(255) DEFAULT '' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_holiday'
#
CREATE TABLE tx_bwbookingmanager_domain_model_holiday (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_bwbookingmanager_calendar_timeslot_mm'
#
CREATE TABLE tx_bwbookingmanager_calendar_timeslot_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_bwbookingmanager_calendar_blockslot_mm'
#
CREATE TABLE tx_bwbookingmanager_calendar_blockslot_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_bwbookingmanager_calendar_holiday_mm'
#
CREATE TABLE tx_bwbookingmanager_calendar_holiday_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_bwbookingmanager_calendar_notification_mm'
#
CREATE TABLE tx_bwbookingmanager_calendar_notification_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

CREATE TABLE fe_users (
	entries varchar(11) DEFAULT 0 NOT NULL
);

CREATE TABLE tt_content (
    calendar int(11) unsigned DEFAULT '0' NOT NULL,
);

CREATE TABLE tx_bwbookingmanager_domain_model_ics (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	calendars int(11) unsigned DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	options int(11) DEFAULT '0' NOT NULL,
	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,

	entry_title varchar(255) DEFAULT '' NOT NULL,
	entry_location varchar(255) DEFAULT '' NOT NULL,
	entry_description varchar(255) DEFAULT '' NOT NULL,
	timeslot_title varchar(255) DEFAULT '' NOT NULL,
	timeslot_location varchar(255) DEFAULT '' NOT NULL,
	timeslot_description varchar(255) DEFAULT '' NOT NULL,
	blockslot_title varchar(255) DEFAULT '' NOT NULL,
	blockslot_location varchar(255) DEFAULT '' NOT NULL,
	blockslot_description varchar(255) DEFAULT '' NOT NULL,
	holiday_title varchar(255) DEFAULT '' NOT NULL,
	holiday_location varchar(255) DEFAULT '' NOT NULL,
	holiday_description varchar(255) DEFAULT '' NOT NULL,
	secret varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_bwbookingmanager_calendar_ics_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
    sorting_foreign int(11) DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
