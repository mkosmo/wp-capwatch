<?php

defined('ABSPATH') or die("No script kiddies please!");

function handle_capwatch_upload() {

	if ( $errorIndex = $_FILES['db_file']['error'] ) {
		$error = capwatchError( '<strong>Error on Upload:<strong> ' . $errorIndex );
	} else {
		$tmp_name = $_FILES['db_file']['tmp_name'];
		$name = $_FILES['db_file']['name'];
		$upload_dir = wp_upload_dir();
		$upload_dir['capwatch'] = $upload_dir['basedir'] . '/capwatch';
		if ( file_exists( $upload_dir['capwatch'] ) ) {
			deleteDir( $upload_dir['capwatch'] );
		}
		$mkdir = mkdir( $upload_dir['capwatch'] );
	}

	if ( $mkdir ) {
		$moveFile = move_uploaded_file( $tmp_name, $upload_dir['capwatch'] . '/' . $name );
	} elseif ( !$error ) {
		$error = capwatchError( '<strong>Error creating CAPWATCH temporary directory</strong>' );
	}

	if ( $moveFile ) {
		$zip = new ZipArchive;
		$rs = $zip->open( $upload_dir['capwatch'] . '/' . $name );
		if ( $rs ) {
			$zip->extractTo( $upload_dir['capwatch'] );
			$zip->close();
			$unzipped = TRUE;
		} elseif ( !$error ) {
			$error = capwatchError( '<strong>Error during unzip of CAPWATCH archive</strong>' );
		}
	} elseif ( !$error ) {
		$error = capwatchError( '<strong>Error moving uploaded file to CAPWATCH temporary directory</strong>' );
	}

	if ( $unzipped ) {
		dbLoadTable( $upload_dir['capwatch'] . '/Member.txt', 'member' );
		dbLoadTable( $upload_dir['capwatch'] . '/MbrContact.txt', 'member_contact' );
		dbLoadTable( $upload_dir['capwatch'] . '/DutyPosition.txt', 'duty_position' );
		dbLoadTable( $upload_dir['capwatch'] . '/CadetDutyPositions.txt', 'cadet_duty_position' );
		deleteDir( $upload_dir['capwatch'] );
	}

	syncUsers();

}

function dbLoadTable( $fileName, $tableName ) {

	global $wpdb;

	$table_name = $wpdb->prefix . 'capwatch_' . $tableName;

	$qry = "TRUNCATE TABLE {$table_name}";

	$rs = $wpdb->query( $qry );

	if ( $rs == FALSE ) {
		$error = capwatchError( '<strong>MySQL Error:</strong> Query <em>' . $qry . '</em> failed.' );
		$wpdb->print_error();
		return FALSE;
	}

	$qry = "SHOW COLUMNS FROM {$table_name}";

	$rs = $wpdb->get_results( $qry );

	foreach( $rs as $key => $column ) {
		$columns[$key] = $column->Field;
	}

	$fileData = file( $fileName );

	$i = 0;

	foreach( $fileData as $row ) {
		if ( $i ) {
			$cols = str_getcsv( $row );
			foreach( $columns as $key => $colName ) {
				$array[$colName] = str_replace( '"', NULL, $cols[$key] );
			}
			$wpdb->insert( $table_name, $array );
		}

		$i++;
	}

}

function capwatchError( $msg ) {

	$error = new WP_Error( 'broke', __( '<div class="error">' . $msg . '</div>' ) );
	echo $error->get_error_message();
	return TRUE;

}

function deleteDir( $dirPath ) {

	if ( !is_dir( $dirPath ) ) {
		throw new InvalidArgumentException( '$dirPath must be a directory' );
	}

	if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
		$dirPath .= '/';
	}

	$files = glob( $dirPath . '*', GLOB_MARK );

	foreach ( $files as $file ) {
		if ( is_dir( $file ) ) {
			deleteDir( $file );
		} else {
			unlink( $file );
		}
	}

	rmdir( $dirPath );

}

function syncUsers() {
	if (!isset($_POST["create_users"]))
		return;

	global $wpdb;
	$table_prefix = $wpdb->prefix . 'capwatch_';
	$qry = $wpdb->get_results( "
					SELECT member.CAPID, NameLast, NameFirst, NameMiddle, Rank,
						Contact AS Email
					FROM {$table_prefix}member member
					INNER JOIN {$table_prefix}member_contact contact
						ON contact.CAPID = member.CAPID
					WHERE contact.Type = 'EMAIL'
						AND contact.Priority = 'PRIMARY'
					ORDER BY NameLast, NameFirst
					" );

	if ( $qry == FALSE ) {
		$error = capwatchError( '<strong>MySQL Error:</strong> Query failed.' );
		$wpdb->print_error();
		return FALSE;
	}

	if (WP_DEBUG === true)
		error_log(print_r(var_dump($qry)));

	foreach( $qry as $row ) {
		$userdata = array(
			'user_login' => $row->CAPID,
			'first_name' => $row->NameFirst,
			'last_name' => $row->NameLast,
			'user_email' => $row->Email,
		);

		$userid = username_exists( $row->CAPID );
		if ( !$userid and email_exists($row->Email) == false ) {
			$userdata['user_pass'] = wp_generate_password( $length=12, $include_standard_special_chars=false );
			$userid = wp_insert_user( $userdata );
			wp_new_user_notification( $userid, null, 'both' );
			unset( $userdata['user_pass'] );
		}

		if (WP_DEBUG === true)
			error_log(print_r(var_dump($userid)));

		$userdata['ID'] = $userid;
		wp_update_user( $userdata );
		update_user_meta( $userid, 'grade', $row->Rank);
	}
}
