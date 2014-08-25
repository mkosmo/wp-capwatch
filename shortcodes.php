<?php

defined('ABSPATH') or die("No script kiddies please!");

function wp_capwatch_duty_positions( $atts ) {

	global $wpdb;

	$a = shortcode_atts( array(
		'link' => NULL,
		'contact' => NULL
		), $atts );

	$duty_position_order = get_option( 'wp_capwatch_duty_position_order' );

	$table_prefix = $wpdb->prefix . 'capwatch_';

	$qry = $wpdb->get_results( "
		SELECT mbr.CAPID, NameLast, NameFirst, Rank, Duty 
		FROM {$table_prefix}duty_position dp 
		INNER JOIN {$table_prefix}member mbr 
			ON mbr.CAPID = dp.CAPID  
		WHERE dp.Asst = 0
		" );

	foreach( $qry as $row ) {
		$duty_position[$row->Duty] = $row;
	}

	$string = "
	<table>
		<thead>
			<th>Position</th>
			<th>Name</th>
		</thead>
		<tbody>
";

	foreach( $duty_position_order as $row ) {
		$member = $duty_position[$row];
		$string .= "
			<tr>
				<td>{$member->Duty}</td>
";

		if ( $a['link'] && $a['contact'] ) {
			$link = $a['link'];
			$contact = md5( $member->CAPID );
			$string .= "<td><a href=\"{$link}?contact={$contact}\">{$member->Rank} {$member->NameFirst} {$member->NameLast}</a></td>";
		} else {
			$string .= "<td>{$member->Rank} {$member->NameFirst} {$member->NameLast}</td>";
		}

		$string .= "
			</tr>
";
	}

	$string .= "
		</tbody>
	</table>
";

	return $string;
}
add_shortcode( 'duty_positions', 'wp_capwatch_duty_positions' );

function wp_capwatch_member_contact( $atts, $content = NULL ) {

	global $wpdb;

	$a = shortcode_atts( array(
		'link' => NULL,
		'position' => NULL
		), $atts );

	$table_prefix = $wpdb->prefix . 'capwatch_';

	$qry = $wpdb->get_results( "
		SELECT mbr.CAPID, NameLast, NameFirst, Rank 
		FROM {$table_prefix}duty_position dp 
		INNER JOIN {$table_prefix}member mbr 
			ON mbr.CAPID = dp.CAPID 
		WHERE dp.Duty = '{$a['position']}' 
		AND dp.Asst = 0
		" );

	$link = $a['link'];
	$contact = md5( $qry[0]->CAPID );
	$content = $content ? $content : "{$qry[0]->Rank} {$qry[0]->NameFirst} {$qry[0]->NameLast}";

	return "<a href=\"{$link}?contact={$contact}\">{$content}</a>";
}
add_shortcode( 'member_contact', 'wp_capwatch_member_contact' );
