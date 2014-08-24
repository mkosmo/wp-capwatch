<?php

global $capwatch_db_version;
$capwatch_db_version = '1.0';

function capwatch_install() {

	global $wpdb;
	global $capwatch_db_version;

	$table_prefix = $wpdb->prefix . 'capwatch_';
	
	/*
	 * We'll set the default character set and collation for this table.
	 * If we don't do this, some characters could end up being converted 
	 * to just ?'s when saved in our table.
	 */
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $table_prefix . "member";
	$sql = "CREATE TABLE $table_name (
		CAPID int(6) NOT NULL,
		SSN varchar(9) NOT NULL,
		NameLast varchar(50) NOT NULL,
		NameFirst varchar(30) NOT NULL,
		NameMiddle varchar(1) NOT NULL,
		NameSuffix varchar(5) NOT NULL,
		Gender varchar(6) NOT NULL,
		DOB varchar(10) NOT NULL,
		Profession varchar(50) NOT NULL,
		EducationLevel varchar(2) NOT NULL,
		Citizen varchar(20) NOT NULL,
		ORGID int(6) NOT NULL,
		Wing varchar(3) NOT NULL,
		Unit varchar(3) NOT NULL,
		Rank varchar(10) NOT NULL,
		Joined varchar(10) NOT NULL,
		Expiration varchar(10) NOT NULL,
		OrgJoined varchar(10) NOT NULL,
		UsrID varchar(20) NOT NULL,
		DateMod varchar(10) NOT NULL,
		LSCode varchar(1) NOT NULL,
		Type varchar(15) NOT NULL,
		RankDate varchar(10) NOT NULL,
		Region varchar(3) NOT NULL,
		MbrStatus varchar(20) NOT NULL,
		PicStatus varchar(20) NOT NULL,
		PicDate varchar(10) NOT NULL,
		CdtWaiver varchar(20) NOT NULL,
		UNIQUE KEY CAPID (CAPID)
	) $charset_collate;";
	dbDelta( $sql );

	$table_name = $table_prefix . "duty_position";
	$sql = "CREATE TABLE $table_name (
		CAPID int(6) NOT NULL,
		Duty varchar(100) NOT NULL,
		FunctArea varchar(5) NOT NULL,
		Lvl varchar(10) NOT NULL,
		Asst tinyint(1) NOT NULL,
		UsrID varchar(20) NOT NULL,
		DateMod varchar(10) NOT NULL,
		ORGID int(6) NOT NULL
	) $charset_collate;";
	dbDelta( $sql );

	add_option( 'capwatch_db_version', $capwatch_db_version );

}

function capwatch_uninstall() {

	global $wpdb;
	global $capwatch_db_version;

	$table_prefix = $wpdb->prefix . 'capwatch_';

	$table_list = array( 'member', 'duty_position' );

	foreach( $table_list as $table ) {
		$table_name = $table_prefix . $table;
		$sql .= "DROP TABLE $table_name";
		$wpdb->query( $sql );
	}

}