<?php

defined('ABSPATH') or die("No script kiddies please!");

function wp_capwatch_duty_positions( $atts ) {

        global $wpdb;

        $a = shortcode_atts( array(
                'link' => NULL,
                'contact' => NULL
                ), $atts );

        $duty_position_order = get_option( 'wp_capwatch_duty_position_order' );
        $duty_position = array();

        $table_prefix = $wpdb->prefix . 'capwatch_';

        $qry = $wpdb->get_results( "
                SELECT mbr.CAPID, NameLast, NameFirst, Rank, Duty, Asst, Contact
                FROM {$table_prefix}duty_position dp
                INNER JOIN {$table_prefix}member mbr
                        ON mbr.CAPID = dp.CAPID
                INNER JOIN {$table_prefix}member_contact contact
                        ON contact.CAPID = dp.CAPID
                WHERE contact.Type = 'EMAIL'
                        AND contact.Priority = 'PRIMARY'
                ORDER BY Duty, Asst, NameLast, NameFirst
                " );
                #WHERE dp.Asst = 0
                #" );

        foreach( $qry as $row ) {
                if ( !is_array($duty_position[$row->Duty]) ) {
                        $duty_position[$row->Duty] = array();
                }
                array_push($duty_position[$row->Duty], $row);
                #$duty_position[$row->Duty] = $row;
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
                $dp = $duty_position[$row];
                foreach ( $dp as $member ) {
                        if ($member->Asst === "1")
                                $member->Duty = "Assistant " . $member->Duty;
                        $string .= "
                                <tr>
                                        <td>{$member->Duty}</td>
                        ";

                        if ( $a['link'] && $a['contact'] ) {
                                $link = $a['link'];
                                $contact = sha1( $member->CAPID );
                                $string .= "<td><a href=\"mailto:{$member->Contact}\">{$member->Rank} {$member->NameFirst} {$member->NameLast}</a></td>";
                        } else {
                                $string .= "<td>{$member->Rank} {$member->NameFirst} {$member->NameLast}</td>";
                        }

                        $string .= "
                                </tr>
                        ";
                }
        }

        $string .= "
                </tbody>
        </table>
";

        return $string;
}
add_shortcode( 'duty_positions', 'wp_capwatch_duty_positions' );
