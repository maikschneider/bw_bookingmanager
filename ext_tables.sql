#
# Table structure for table 'tx_bwbookingmanager_domain_model_calendar'
#
CREATE TABLE tx_bwbookingmanager_domain_model_calendar (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	timeslots int(11) unsigned DEFAULT '0' NOT NULL,
	blockslots int(11) unsigned DEFAULT '0' NOT NULL,
	notifications int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_timeslot'
#
CREATE TABLE tx_bwbookingmanager_domain_model_timeslot (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start_date datetime NOT NULL,
	end_date datetime NOT NULL,
	repeat_type int(11) DEFAULT '0' NOT NULL,
	max_weight int(11) DEFAULT '0' NOT NULL,
	entries int(11) unsigned DEFAULT '0' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_end datetime DEFAULT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_entry'
#
CREATE TABLE tx_bwbookingmanager_domain_model_entry (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	timeslot int(11) unsigned DEFAULT '0' NOT NULL,
	calendar int(11) unsigned DEFAULT '0' NOT NULL,

	start_date datetime NOT NULL,
	end_date datetime NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	prename varchar(255) DEFAULT '' NOT NULL,
	street varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	newsletter smallint(5) unsigned DEFAULT '0' NOT NULL,
	weight int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

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

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for table 'tx_bwbookingmanager_domain_model_blockslot'
#
CREATE TABLE tx_bwbookingmanager_domain_model_blockslot (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	start_date datetime NOT NULL,
	end_date datetime NOT NULL,
	reason varchar(255) DEFAULT '' NOT NULL,
	calendars int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

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