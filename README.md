wp-capwatch
===========

This plugin leverages the CAPWATCH download from Civil Air Patrol National Headquarters to populate membership data and provide a contact form on unit websites.

To use, install the plugin and activate on your WordPress site. Then, from the dashboard, go to Settings > CAPWATCH Settings and do the following:

1. Download a CAPWATCH database from www.capnhq.gov and upload under the CAPWATCH Database section
2. Enter your unit charter number in the format: RGN-WG-xxx in the Unit Charter Number field
3. Save Changes
4. Drag the order of duty positions as you would like them to appear on the senior staff listing

Once the settings page is complete, you may use the following shortcodes anywhere on your site to drop CAPWATCH content into your WordPress environment:

> [duty_positions contact="(TRUE|FALSE)"] - The duty_positions shortcode creates a table of all senior duty positions for use on a staff roster style page.  If the contact attribute is false, then no links to contact members will be active. If set to true, then the link attribute must be set to the URL of another page that uses the member_email_form shortcode.
