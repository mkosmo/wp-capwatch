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
			$contact = sha1( $member->CAPID );
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

	if ( $qry ) {
		$link = $a['link'];
		$contact = sha1( $qry[0]->CAPID );
		$content = $content ? $content : "{$qry[0]->Rank} {$qry[0]->NameFirst} {$qry[0]->NameLast}";
		return "<a href=\"{$link}?contact={$contact}\">{$content}</a>";
	} else {
		return "Vacant";
	}

}
add_shortcode( 'member_contact', 'wp_capwatch_member_contact' );

function wp_capwatch_member_email_form( $atts ) {

	global $wpdb;

	$a = shortcode_atts( array(
		'contact' => $_GET['contact'],
		'form_name' => NULL
		), $atts );

	$table_prefix = $wpdb->prefix . 'capwatch_';

	$qry = $wpdb->get_results( "
		SELECT NameLast, NameFirst, Rank 
		FROM {$table_prefix}member mbr
		WHERE sha1( mbr.CAPID ) = '{$a['contact']}' 
		" );

	$string = "
	<form id=\"email_form\">
		<p>
			<label for=\"to\" />To:</label> 
			<input type=\"text\" value=\"{$qry[0]->Rank} {$qry[0]->NameFirst} {$qry[0]->NameLast}\" readonly=\"true\" />
		</p>

		<p>
			<label for=\"from\" />Your Name:</label>
			<input type=\"text\" name=\"from\" id=\"from\" />
		</p>

		<p>
			<label for=\"email\" />Your Email Address:</label>
			<input type=\"email\" name=\"email\" id=\"email\" />
		</p>

		<p>
			<label for=\"subject\" />Subject:</label>
			<input type=\"text\" name=\"subject\" id=\"subject\" />
		</p>

		<p>
			<label for=\"message\" />Message:</label>
			<textarea name=\"message\" id=\"message\" rows=\"5\"></textarea>
		</p>

		<p>
			<input type=\"submit\" value=\"Send Message\" />
		</p>
	</form>
	<script type=\"text/javascript\">
	jQuery(document).ready(function($) {
		jQuery('#email_form').submit(function(event) {
			var data = {
				'action': 'send_member_email',
				'contact': '{$a['contact']},
				'form_name': '{$a['form_name']}',
				'from': jQuery('#from').val(),
				'email': jQuery('#email').val(),
				'subject': jQuery('#subject').val(),
				'message': jQuery('#message').val()
			}
			event.preventDefault();
			jQuery.post( '/wp-admin/admin-ajax.php', data, function(result) {
				alert(result)
			} );
			jQuery('#email_form').html('<h3>Your message has been delivered.</h3>');
			jQuery('html, body').animate({scrollTop:0}, 'slow');
		});
	});
	</script>
";

	return $string;

}
add_shortcode( 'member_email_form', 'wp_capwatch_member_email_form' );
