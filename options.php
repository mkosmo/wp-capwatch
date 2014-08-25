<?php

defined('ABSPATH') or die("No script kiddies please!");

class wp_capwatch_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'CAPWATCH Settings', 
            'CAPWATCH Settings', 
            'manage_options', 
            'wp-capwatch-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        global $wpdb;

        if ( $_FILES ) {
            handle_capwatch_upload();
        }

        // Set class property
        $this->options = get_option( 'wp_capwatch_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>CAPWATCH Settings</h2>           

            <h3>Upload CAPWATCH Database</h3>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="db_file" accept="application/zip" />
                <input type="submit" value="Upload Database" />
            </form>

            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'unit-settings' );   
                do_settings_sections( 'wp-capwatch-setting-admin' );
                submit_button(); 
            ?>
            </form>

            <h3>Sort Duty Positions</h3>
            <?php 

            $results = $wpdb->get_col( "SELECT DISTINCT Duty FROM {$wpdb->prefix}capwatch_duty_position WHERE Asst = 0", 0 );

            if ( $duty_position_order = get_option( 'wp_capwatch_duty_position_order' ) ) {
                $diff = array_diff( $results, $duty_position_order );
                $duty_positions = array_intersect( $duty_position_order, $results );
            } else {
                $duty_positions = $results;
            }

            ?>
            <ul id="duty_positions">
            <?php foreach( $duty_positions as $duty_position ) { ?>
                <li id="<?php echo $duty_position; ?>" class="ui-state-default"><?php echo $duty_position; ?></li>
            <?php } ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'unit-settings', // Option group
            'wp_capwatch_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'unit-settings', // ID
            'Unit Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'wp-capwatch-setting-admin' // Page
        );  

        add_settings_field(
            'unit_charter', // ID
            'Unit Charter Number', // Title 
            array( $this, 'unit_charter_cb' ), // Callback
            'wp-capwatch-setting-admin', // Page
            'unit-settings' // Section           
        );      

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['unit_charter'] ) )
            $new_input['unit_charter'] = sanitize_text_field( $input['unit_charter'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function unit_charter_cb()
    {
        printf(
            '<input type="text" id="unit_charter" name="wp_capwatch_options[unit_charter]" value="%s" />',
            isset( $this->options['unit_charter'] ) ? esc_attr( $this->options['unit_charter']) : ''
        );
    }
}

function update_duty_position_order_js() {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    jQuery('#duty_positions').sortable({
        cursor: 'move',
        update: function() {
            var order = jQuery('#duty_positions').sortable('toArray');
            var data = {
                'action': 'update_duty_position_order',
                'order': order
            }
            jQuery.post( ajaxurl, data );
        }
    });
});
</script>
<?php 
}
add_action( 'admin_footer', 'update_duty_position_order_js' );

if( is_admin() )
    $wp_capwatch_settings_page = new wp_capwatch_SettingsPage();