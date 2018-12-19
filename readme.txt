=== wp-capwatch ===
Contributors: mclarty, mkosmo
Tags: cap
Requires at least: 4.6
Tested up to: 5.0
Stable tag: master
Requires PHP: 5.2.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Plugin to assist with loading CAPWATCH data in to Wordpress sites.

== Description ==

This plugin leverages the CAPWATCH download from Civil Air Patrol National Headquarters to populate membership data and provide a contact form on unit websites.

== Installation ==

1. Download a CAPWATCH database from www.capnhq.gov and upload under the CAPWATCH Database section
2. Enter your unit charter number in the format: RGN-WG-xxx in the Unit Charter Number field
3. Save Changes
4. Drag the order of duty positions as you would like them to appear on the senior staff listing

Once the settings page is complete, you may use the following shortcodes anywhere on your site to drop CAPWATCH content into your WordPress environment:

`[duty_positions contact="(TRUE|FALSE)"]`

The duty_positions shortcode creates a table of all senior duty positions for use on a staff roster style page.  If the contact attribute is false, then no links to contact members will be active. If set to true, then their name will link to their email address.

== Changelog ==

= 1.1.0 =
* General cleanup.
* Create new members from CAPWATCH if they don't exist.
* Update existing users email/grade/name if CAPWATCH updates.

= 1.0.1-451a =
* Initial adaptations for TX-451.
* Squadron duty roster no longer uses custom email form.
* Displays assistants in duty positions.

= 1.0.1 =
* Original version forked from Nick McLarty's repository.
