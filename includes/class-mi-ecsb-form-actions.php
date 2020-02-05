<?php

/**
 * Defines all custom form actions used by the plugin.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 */

/**
 * Defines all custom form actions used by the plugin.
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker_Form_Actions
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * The loader for the plugin.
	 * 
	 * @since   1.0.0
	 * @access  private
	 * @var     EasyCountrySpamBlocker_Loader  $loader  The loader for the plugin.
	 */
	private $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string            $plugin_name       The name of the plugin.
	 * @param      string            $version    The version of this plugin.
	 * @param      EasyCountrySpamBlocker_Loader  $loader  The loader for the plugin.
	 */
	public function __construct( $plugin_name, $version, $loader )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->loader = $loader;
	}
	
	/**
	 * Registers all form actions used in the plugin.
	 * 
	 * @since 1.0.0
	 */
	public function register_form_actions()
	{
		// Nothing here yet...
	}
	
	/**
	 * Registers all AJAX actions used in the plugin.
	 * 
	 * @since 1.0.0
	 */
	public function register_ajax_actions()
	{
		// Save ECSB Settings action.
	    $this->loader->add_action( 'wp_ajax_ecsb_settings', $this, 'handle_ecsb_settings_ajax' );
	    $this->loader->add_action( 'wp_ajax_nopriv_ecsb_settings', $this, 'handle_ecsb_settings_ajax' );
	}
	
	/**
	 * Handles the "ecsb_settings" form action.
	 * 
	 * @since 1.0.0
	 */
	public function handle_ecsb_settings_ajax()
	{
		// If the URL is not set or is empty, send a failure.
		if( !EasyCountrySpamBlocker_Helper::is_real_input( $_POST[ 'redirectUrl' ] ) )
		{
			// Set the return data.
			$return_data = array(
				'msg'       => __( 'The provided URL was either not set correctly, or was empty.', 'mi-ecsb' ),
				'exit_code' => 1001,
				'is_error'  => true
			);
			
			// Send en error as JSON.
			wp_send_json_error( $return_data );
			wp_die();
		}
		
		// Create store for POST data.
		$post_data = array();

		// Sanitize the is enabled.
		$post_data[ 'is_enabled' ] = EasyCountrySpamBlocker_Helper::sanitize_checkbox( $_POST[ 'isEnabled' ] );

		// Sanitize the countries.
		$post_data[ 'countries' ] = is_null( $_POST[ 'countries' ] ) ? [] : $_POST[ 'countries' ];
		
		// Sanitize the redirect URL.
		$post_data[ 'redirect_url' ] = EasyCountrySpamBlocker_Helper::sanitize_url( $_POST[ 'redirectUrl' ] );
		
		// If a custom URL protocol was sent, use it.
		if( EasyCountrySpamBlocker_Helper::is_real_input( $_POST[ 'urlProtocol' ] ) )
		{
			// Sanitize the URL protocol.
			$url_protocol = EasyCountrySpamBlocker_Helper::sanitize_url_protocol( $_POST[ 'urlProtocol' ] );
			
			// If the URL protocol is valid, use it.
			if( EasyCountrySpamBlocker_Helper::is_valid_url( $url_protocol ) )
			{
				// Remove any URL protocols from the URL.
				$post_data[ 'redirect_url' ] = EasyCountrySpamBlocker_Helper::strip_url_protocol( $post_data[ 'redirect_url' ] );
				
				// Add the URL protocol to the URL.
				$post_data[ 'redirect_url' ] = $url_protocol . $post_data[ 'redirect_url' ];
			}
		}
		
		// If the URL isn't valid, send an error.
		if( $post_data[ 'redirect_url' ] === null )
		{
			// Set the return data.
			$return_data = array(
				'msg'       => __( 'The provided URL was not a valid URL.', 'mi-ecsb' ),
				'exit_code' => 1002,
				'is_error'  => true
			);
			
			// Send an error as JSON.
			wp_send_json_error( $return_data );
			wp_die();
		}

		// Save enabled status in the WordPress database.
		EasyCountrySpamBlocker_Helper::save_wp_option( 'mi-ecsb_is_enabled', $post_data[ 'is_enabled' ] );

		// Save countries to the WordPress database.
		EasyCountrySpamBlocker_Helper::save_wp_option( 'mi-ecsb_countries', $post_data[ 'countries' ] );
		
		// Save URL in the WordPress database.
		EasyCountrySpamBlocker_Helper::save_url( $post_data[ 'redirect_url' ] );
	    
	    // Set the return data.
	    $return_data = array(
			'msg'       => __( 'URL Successfully saved!', 'mi-ecsb' ),
			'countries' => $post_data[ 'countries' ],
    		'exit_code' => 0,
			'is_error'  => false
    	);
	    
		// Send a success as JSON.
		wp_send_json_success( $return_data );
		wp_die();
	}
}
