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
class EasyCountrySpamBlocker_Redirect
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
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string            $plugin_name       The name of the plugin.
	 * @param      string            $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Tries to redirect the visitor.
	 * 
	 * @since 1.0.0
	 */
	public function redirect()
	{
		// If the plugin isn't enabled, simply moved on.
		if( !EasyCountrySpamBlocker_Helper::get_wp_option( 'mi-ecsb_is_enabled', false ) )
		{
			return;
		}
		
	    // Setup required data.
		$geoInformation = array();
		
		// If forwarded headers are set, obtain the client's IP.
		if( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) )
		{
			// Obtain the client's IP address.
			$tmpIP = explode( ',', $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] );
			$clientIP = end( $tmpIP );
			
			// Try to get information about the client.
			$geoInformation = $this->get_visitor_info( $clientIP );
			
			// If no information was found, try a different method.
			if( $geoInformation === null )
			{
				// Obtain the client's IP a different way. If the web host uses a proxy, this won't work (unless the server is located outside the US).
				$clientIP = $_SERVER[ 'REMOTE_ADDR' ];
				$clientIP = '76.188.19.75';
            	
            	// Try to get information one more time.
            	$geoInformation = $this->get_visitor_info( $clientIP );
			}
		}
		else // Else, use the remote address.
		{
			// Obtain the client's IP a different way. If the web host uses a proxy, this won't work (unless the server is located outside the US).
        	$clientIP = $_SERVER[ 'REMOTE_ADDR' ];
        	
        	// Try to get information one more time.
        	$geoInformation = $this->get_visitor_info( $clientIP );
		}
		
		// If no information was found, simply let the user browse the site.
		if( $geoInformation === null )
		{
			return;
		}
		
		// Grab the country code.
		$countryCode = $geoInformation->countryCode;
		
		// If no country found is found, do not continue.
		if( $countryCode === null ) {
			return;
		}

		// Grab supported countries.
		$supported_countries = EasyCountrySpamBlocker_Helper::get_wp_option( 'mi-ecsb_countries', [] );
		if( empty( $supported_countries ) ) {
			return;
		}
		
		// If the retrieved country code is not in the list of supported countries, redirect.
		if( !in_array( strtoupper( $countryCode ), $supported_countries ) ) {
			// Redirect the visitor and give a "403 Forbidden" status code.
			http_response_code( 403 );
			header( 'Location: ' . EasyCountrySpamBlocker_Helper::get_url() );
			exit();
		}
	}
	
	/**
	 * Returns true if the provided IP is a real IP. Otherwise, returns false.
	 * 
	 * @since 1.0.0
	 * @access private
	 */
	private function is_valid_ip( $ip )
	{
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
	}
	
	/**
	 * Tries to retrieve information about the IP address with GeoPlugin.net.
	 * 
	 * @since 1.0.0
	 * @access private
	 */
	private function get_visitor_info( $ip )
	{
		if( !$this->is_valid_ip( $ip ) )
		{
			return null;
		}
		
		// Set the API URL.
		$url = 'http://ip-api.com/json/' . $ip;
		
		// Make a request to the API.
		$request = wp_remote_get( $url, array( 'user-agent' => EasyCountrySpamBlocker_Helper::get_user_agent() ) );
		$response = $request[ 'response' ];
		
		// If the request or response is null, return null.
		if( $request === null || $response === null )
		{
		    return null;
		}
		
		// If there is no response code, return null.
		if( $response[ 'code' ] === null )
		{
		    return null;
		}
		
		// If the response code is NOT 200, return null.
		if( $response[ 'code' ] !== 200 )
		{
		    return null;
		}
		
		// If the response body is null, return null.
		if( $request[ 'body' ] === null )
		{
			return null;
		}
		
		// Convert the JSON response into an object.
		return json_decode( $request[ 'body' ] );
	}
}
