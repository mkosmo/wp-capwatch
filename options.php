<?php

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
        // Set class property
        $this->options = get_option( 'wp_capwatch_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>CAPWATCH Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'unit-settings' );   
                do_settings_sections( 'wp-capwatch-setting-admin' );
                submit_button(); 
            ?>
            </form>
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

if( is_admin() )
    $wp_capwatch_settings_page = new wp_capwatch_SettingsPage();