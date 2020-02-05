<?php

/**
 * Defines helper functions.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 */

/**
 * Defines helper functions.
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker_Helper
{
	/**
	 * Has the class been initialized?
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      bool    $initialized    Is the class initialized?
	 */
	private static $initialized = false;
	
	/**
	 * The name of the option in the database that the URL is stored in.
	 * 
	 * @since 1.0.0
	 * @access private
	 * @var string $url_option_name The option name of the URL for the database.
	 */
	private static $url_option_name = 'mi-ecsb_redirect_url';
	
	/**
	 * The default URL to use.
	 * 
	 * @since 1.0.0
	 * @access private
	 * @var string $default_url The default URL to use.
	 */
	private static $default_url = 'https://google.com/';
	
	/**
	 * Create an empty initializer for the class, since everything in this class are static functions.
	 * 
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {}
	
	/**
	 * The real "constructor" for the class. Anything that needs to be initialized in any function is done here.
	 * 
	 * @since 1.0.0
	 * @access private
	 */
	private static function initialize()
	{
	    // If the class has already been initialized once, do not do it again.
	    if( self::$initialized )
	    {
	        return;
	    }
	    
	    // Set the class as initialized!
	    self::$initialized = true;
	}

	/**
	 * Save an option to the WP database.
	 * 
	 * @since 1.0.1
	 */
	public static function save_wp_option( $option_name, $value )
	{
		self::initialize();

		// If the name or value provided is not a real input, return.
		if( is_null( $option_name ) || is_null( $value ) )
		{
			return;
		}

		// Save value to WP database.
		update_option( $option_name, $value );
	}
	
	/**
	 * Retrieves an option from the WP database.
	 * 
	 * @since 1.0.1
	 */
	public static function get_wp_option( $option_name, $default_value )
	{
		self::initialize();

		return !is_null( $option_name ) ? get_option( $option_name, $default_value ) : null;
	}
	
	/**
	 * Saves the provided URL into the WordPress database, assuming it is valid and safe.
	 * 
	 * @since 1.0.0
	 */
	public static function save_url( $url )
	{
	    self::initialize();
	    
		// If the URL provided is not a real input, return.
	    if( !self::is_real_input( $url ) )
	    {
	    	return;
	    }
	    
	    // If the URL provided is not a real URL, return.
	    if( !self::is_valid_url( $url ) )
	    {
	    	return;
	    }
	    
	    // Save URL in the WordPress database.
		update_option( self::$url_option_name, $url );
	}
	
	/**
	 * Returns the saved URL from the WordPress database, assuming it is valid and safe.
	 * 
	 * @since 1.0.0
	 */
	public static function get_url()
	{
	    self::initialize();
	    
	    // Get URL from the WordPress database.
	    $url = get_option( self::$url_option_name, esc_url( self::$default_url, self::get_allowed_url_protocols() ) );
	    
	    // If the URL is not a real input, return.
	    if( !self::is_real_input( $url ) )
	    {
	    	// If the URL is empty, save the default URL and return it.
	    	if( trim( $url ) == '' )
	    	{
	    		// Get the default URL.
	    		$url = self::$default_url;
	    		
	    		// Save the default URL to the database.
	    		self::save_url( $url );
	    		
	    		return $url;
	    	}
	    	else
	    	{
	    		return null;
	    	}
	    }
	    
	    // If the URL is not a real URL, return.
	    if( !self::is_valid_url( $url ) )
	    {
	    	return null;
	    }
	    
	    return $url;
	}
	
	/**
	 * Deletes the saved URL from the database. This should probably only be used during plugin deactivation.
	 * 
	 * @since 1.0.0
	 */
	public static function delete_url()
	{
		delete_option( self::$url_option_name );
	}
	
	/**
	 * Returns the protocol of a provided URL.
	 * 
	 * @since 1.0.0
	 */
	public static function get_url_protocol( $url )
	{
		self::initialize();
		
		// If the provided URL is not a real input, return.
	    if( !self::is_real_input( $url ) )
	    {
	    	return null;
	    }
	    
	    // If the URL provided is not a real URL, return.
	    if( !self::is_valid_url( $url ) )
	    {
	    	return null;
	    }
	    
	    return self::remove_whitespace( parse_url( $url )[ 'scheme' ] );
	}
	
	/**
	 * Sanitizes a URL.
	 * 
	 * @since 1.0.0
	 */
	public static function sanitize_url( $url )
	{
	    self::initialize();
	    
	    // Remove whitespace from URL.
	    $safe_url = self::remove_whitespace( $url );
	    
	    // If the URL provided is not a real input, return null.
	    if( !self::is_real_input( $safe_url ) )
	    {
	        return null;
	    }
	    
	    // Strip anything dangerous away from the URL.
	    $safe_url = esc_url_raw(
			strip_tags(
				stripslashes(
					filter_var( $safe_url, FILTER_VALIDATE_URL )
				)
			),
			self::get_allowed_url_protocols()
		);
		
		// If the URL is not a valid URL, return null.
		if( !self::is_valid_url( $safe_url ) )
		{
		    return null;
		}
	    
	    return $safe_url;
	}
	
	/**
	 * Sanitizes a URL's protocol.
	 * 
	 * @since 1.0.1
	 */
	public static function sanitize_url_protocol( $protocol )
	{
		self::initialize();
		
		// Remove whitespace from URL.
	    $safe_protocol = self::remove_whitespace( $protocol );
		
		// If the protocol provided is not a real input, return null.
		if( !self::is_real_input( $safe_protocol ) )
		{
			return null;
		}
		
		// Strip anything dangerous away from the protocol.
		$safe_protocol = esc_url_raw(
			strip_tags(
				stripslashes(
					filter_var( $safe_protocol, FILTER_SANITIZE_URL )
				)
			)
		);
		
		// If the safe protocol is not a valid URL, return null.
		if( !self::is_valid_url( $safe_protocol ) )
		{
			return null;
		}
		
		return $safe_protocol;
	}

	/**
	 * Sanitizes a checkbox value.
	 * 
	 * @since 1.0.0
	 */
	public static function sanitize_checkbox( $value )
	{
		self::initialize();

		$safe_checkbox = $value;

		// Convert value into true or false boolean.
		$safe_checkbox = $safe_checkbox == 'on' || $safe_checkbox == 'true' ? true : false;

		return $safe_checkbox;
	}
	
	/**
	 * Removes a URL protocol from a provided URL.
	 * 
	 * @since 1.0.0
	 */
	public static function strip_url_protocol( $url )
	{
		self::initialize();
		
		// If the URL provided is not a real input, return null.
	    if( !self::is_real_input( $url ) )
	    {
	        return null;
	    }
	    
	    // Strip anything dangerous away from the URL.
	    $stripped_url = preg_replace('/(^\w+:|^)\/\//', '', $url );
	    
	    return $stripped_url;
	}
	
	/**
	 * Returns true if the provided input is set and not empty. Otherwise, returns false.
	 * 
	 * @since 1.0.0
	 */
	public static function is_real_input( $input )
	{
	    self::initialize();

	    // If the input is not set, return false.
	    if( !isset( $input ) || $input == null )
	    {
	        return false;
	    }
	    
	    // If the input is empty, return false.
	    if( trim( $input ) == '' )
	    {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Removes all whitespace from a string.
	 * 
	 * @since 1.0.0
	 */
	public static function remove_whitespace( $input )
	{
		return preg_replace('/\s+/', '', $input );
	}
	
	/**
	 * Returns true if the input given is a valid HTTP or HTTPS URL. Otherwise, returns false.
	 * 
	 * @since 1.0.0
	 */
	public static function is_valid_url( $url )
	{
	    self::initialize();
	    
	    // If the URL provided is not a real input, return false.
	    if( !self::is_real_input( $url ) )
	    {
	        return false;
	    }
	    
	    // If the URL is not a valid URL, return false.
	    if( trim( $url ) == '' || $url !== esc_url_raw( $url, self::get_allowed_url_protocols() ) || strpos( $url, '.' ) === false )
	    {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Returns the allowed URL protocols.
	 * 
	 * @since 1.0.0
	 */
	public static function get_allowed_url_protocols()
	{
	    self::initialize();
	    
	    return array( 'http', 'https' );
	}
	
	/**
	 * Returns the user agent to use when connecting to another end point.
	 * 
	 * @since 1.0.0
	 */
	public static function get_user_agent()
	{
		self::initialize();
		
		global $wp_version;
		return 'WordPress/' . $wp_version . '; ' . home_url();
	}

	/**
	 * Returns a list of all countries in the world.
	 * 
	 * @since 1.1.0
	 */
	public static function get_countries()
	{
		self::initialize();

		return array(
			'Afghanistan'                                => 'AF',
			'Albania'                                    => 'AL',
			'Algeria'                                    => 'DZ',
			'American Samoa'                             => 'AS',
			'Andorra'                                    => 'AD',
			'Angola'                                     => 'AO',
			'Anguilla'                                   => 'AI',
			'Antarctica'                                 => 'AQ',
			'Antigua And Barbuda'                        => 'AG',
			'Argentina'                                  => 'AR',
			'Armenia'                                    => 'AM',
			'Aruba'                                      => 'AW',
			'Australia'                                  => 'AU',
			'Austria'                                    => 'AT',
			'Azerbaijan'                                 => 'AZ',
			'Bahamas'                                    => 'BS',
			'Bahrain'                                    => 'BH',
			'Bangladesh'                                 => 'BD',
			'Barbados'                                   => 'BB',
			'Belarus'                                    => 'BY',
			'Belgium'                                    => 'BE',
			'Belize'                                     => 'BZ',
			'Benin'                                      => 'BJ',
			'Bermuda'                                    => 'BM',
			'Bhutan'                                     => 'BT',
			'Bolivia'                                    => 'BO',
			'Bosnia And Herzegovina'                     => 'BA',
			'Botswana'                                   => 'BW',
			'Bouvet Island'                              => 'BV',
			'Brazil'                                     => 'BR',
			'British Indian Ocean Territory'             => 'IO',
			'Brunei Darussalam'                          => 'BN',
			'Bulgaria'                                   => 'BG',
			'Burkina Faso'                               => 'BF',
			'Burundi'                                    => 'BI',
			'Cambodia'                                   => 'KH',
			'Cameroon'                                   => 'CM',
			'Canada'                                     => 'CA',
			'Cape Verde'                                 => 'CV',
			'Cayman Islands'                             => 'KY',
			'Central African Republic'                   => 'CF',
			'Chad'                                       => 'TD',
			'Chile'                                      => 'CL',
			'People\'s Republic Of China'                => 'CN',
			'Christmas Island'                           => 'CX',
			'Cocos (Keeling) Islands'                    => 'CC',
			'Colombia'                                   => 'CO',
			'Comoros'                                    => 'KM',
			'Congo'                                      => 'CG',
			'Congo, The Democratic Republic Of'          => 'CD',
			'Cook Islands'                               => 'CK',
			'Costa Rica'                                 => 'CR',
			'Côte D\'Ivoire'                             => 'CI',
			'Croatia'                                    => 'HR',
			'Cuba'                                       => 'CU',
			'Cyprus'                                     => 'CY',
			'Czech Republic'                             => 'CZ',
			'Denmark'                                    => 'DK',
			'Djibouti'                                   => 'DJ',
			'Dominica'                                   => 'DM',
			'Dominican Republic'                         => 'DO',
			'Ecuador'                                    => 'EC',
			'Egypt'                                      => 'EG',
			'Western Sahara'                             => 'EH',
			'El Salvador'                                => 'SV',
			'Equatorial Guinea'                          => 'GQ',
			'Eritrea'                                    => 'ER',
			'Estonia'                                    => 'EE',
			'Ethiopia'                                   => 'ET',
			'Falkland Islands (Malvinas)'                => 'FK',
			'Faroe Islands'                              => 'FO',
			'Fiji'                                       => 'FJ',
			'Finland'                                    => 'FI',
			'France'                                     => 'FR',
			'French Guiana'                              => 'GF',
			'French Polynesia'                           => 'PF',
			'French Southern Territories'                => 'TF',
			'Gabon'                                      => 'GA',
			'Gambia'                                     => 'GM',
			'Georgia'                                    => 'GE',
			'Germany'                                    => 'DE',
			'Ghana'                                      => 'GH',
			'Gibraltar'                                  => 'GI',
			'Greece'                                     => 'GR',
			'Greenland'                                  => 'GL',
			'Grenada'                                    => 'GD',
			'Guadeloupe'                                 => 'GP',
			'Guam'                                       => 'GU',
			'Guatemala'                                  => 'GT',
			'Guinea'                                     => 'GN',
			'Guinea-Bissau'                              => 'GW',
			'Guyana'                                     => 'GY',
			'Haiti'                                      => 'HT',
			'Heard Island And Mcdonald Islands'          => 'HM',
			'Honduras'                                   => 'HN',
			'Hong Kong'                                  => 'HK',
			'Hungary'                                    => 'HU',
			'Iceland'                                    => 'IS',
			'India'                                      => 'IN',
			'Indonesia'                                  => 'ID',
			'Iran, Islamic Republic Of'                  => 'IR',
			'Iraq'                                       => 'IQ',
			'Ireland'                                    => 'IE',
			'Israel'                                     => 'IL',
			'Italy'                                      => 'IT',
			'Jamaica'                                    => 'JM',
			'Japan'                                      => 'JP',
			'Jordan'                                     => 'JO',
			'Kazakhstan'                                 => 'KZ',
			'Kenya'                                      => 'KE',
			'Kiribati'                                   => 'KI',
			'Korea, Democratic People\'s Republic Of'    => 'KP',
			'Korea, Republic Of'                         => 'KR',
			'Kuwait'                                     => 'KW',
			'Kyrgyzstan'                                 => 'KG',
			'Lao People\'s Democratic Republic'          => 'LA',
			'Latvia'                                     => 'LV',
			'Lebanon'                                    => 'LB',
			'Lesotho'                                    => 'LS',
			'Liberia'                                    => 'LR',
			'Libyan Arab Jamahiriya'                     => 'LY',
			'Liechtenstein'                              => 'LI',
			'Lithuania'                                  => 'LT',
			'Luxembourg'                                 => 'LU',
			'Macao'                                      => 'MO',
			'Macedonia, The Former Yugoslav Republic Of' => 'MK',
			'Madagascar'                                 => 'MG',
			'Malawi'                                     => 'MW',
			'Malaysia'                                   => 'MY',
			'Maldives'                                   => 'MV',
			'Mali'                                       => 'ML',
			'Malta'                                      => 'MT',
			'Marshall Islands'                           => 'MH',
			'Martinique'                                 => 'MQ',
			'Mauritania'                                 => 'MR',
			'Mauritius'                                  => 'MU',
			'Mayotte'                                    => 'YT',
			'Mexico'                                     => 'MX',
			'Micronesia, Federated States Of'            => 'FM',
			'Moldova, Republic Of'                       => 'MD',
			'Monaco'                                     => 'MC',
			'Mongolia'                                   => 'MN',
			'Montserrat'                                 => 'MS',
			'Morocco'                                    => 'MA',
			'Mozambique'                                 => 'MZ',
			'Myanmar'                                    => 'MM',
			'Namibia'                                    => 'NA',
			'Nauru'                                      => 'NR',
			'Nepal'                                      => 'NP',
			'Netherlands'                                => 'NL',
			'Netherlands Antilles'                       => 'AN',
			'New Caledonia'                              => 'NC',
			'New Zealand'                                => 'NZ',
			'Nicaragua'                                  => 'NI',
			'Niger'                                      => 'NE',
			'Nigeria'                                    => 'NG',
			'Niue'                                       => 'NU',
			'Norfolk Island'                             => 'NF',
			'Northern Mariana Islands'                   => 'MP',
			'Norway'                                     => 'NO',
			'Oman'                                       => 'OM',
			'Pakistan'                                   => 'PK',
			'Palau'                                      => 'PW',
			'Palestinian Territory, Occupied'            => 'PS',
			'Panama'                                     => 'PA',
			'Papua New Guinea'                           => 'PG',
			'Paraguay'                                   => 'PY',
			'Peru'                                       => 'PE',
			'Philippines'                                => 'PH',
			'Pitcairn'                                   => 'PN',
			'Poland'                                     => 'PL',
			'Portugal'                                   => 'PT',
			'Puerto Rico'                                => 'PR',
			'Qatar'                                      => 'QA',
			'Réunion'                                    => 'RE',
			'Romania'                                    => 'RO',
			'Russian Federation'                         => 'RU',
			'Rwanda'                                     => 'RW',
			'Saint Helena'                               => 'SH',
			'Saint Kitts And Nevis'                      => 'KN',
			'Saint Lucia'                                => 'LC',
			'Saint Pierre And Miquelon'                  => 'PM',
			'Saint Vincent And The Grenadines'           => 'VC',
			'Samoa'                                      => 'WS',
			'San Marino'                                 => 'SM',
			'Sao Tome And Principe'                      => 'ST',
			'Saudi Arabia'                               => 'SA',
			'Senegal'                                    => 'SN',
			'Serbia And Montenegro'                      => 'CS',
			'Seychelles'                                 => 'SC',
			'Sierra Leone'                               => 'SL',
			'Singapore'                                  => 'SG',
			'Slovakia'                                   => 'SK',
			'Slovenia'                                   => 'SI',
			'Solomon Islands'                            => 'SB',
			'Somalia'                                    => 'SO',
			'South Africa'                               => 'ZA',
			'South Georgia And South Sandwich Islands'   => 'GS',
			'Spain'                                      => 'ES',
			'Sri Lanka'                                  => 'LK',
			'Sudan'                                      => 'SD',
			'Suriname'                                   => 'SR',
			'Svalbard And Jan Mayen'                     => 'SJ',
			'Swaziland'                                  => 'SZ',
			'Sweden'                                     => 'SE',
			'Switzerland'                                => 'CH',
			'Syrian Arab Republic'                       => 'SY',
			'Taiwan, Republic Of China'                  => 'TW',
			'Tajikistan'                                 => 'TJ',
			'Tanzania, United Republic Of'               => 'TZ',
			'Thailand'                                   => 'TH',
			'Timor-Leste'                                => 'TL',
			'Togo'                                       => 'TG',
			'Tokelau'                                    => 'TK',
			'Tonga'                                      => 'TO',
			'Trinidad And Tobago'                        => 'TT',
			'Tunisia'                                    => 'TN',
			'Turkey'                                     => 'TR',
			'Turkmenistan'                               => 'TM',
			'Turks And Caicos Islands'                   => 'TC',
			'Tuvalu'                                     => 'TV',
			'Uganda'                                     => 'UG',
			'Ukraine'                                    => 'UA',
			'United Arab Emirates'                       => 'AE',
			'United Kingdom'                             => 'GB',
			'United States'                              => 'US',
			'United States Minor Outlying Islands'       => 'UM',
			'Uruguay'                                    => 'UY',
			'Uzbekistan'                                 => 'UZ',
			'Venezuela'                                  => 'VE',
			'Vanuatu'                                    => 'VU',
			'Viet Nam'                                   => 'VN',
			'British Virgin Islands'                     => 'VG',
			'U.S. Virgin Islands'                        => 'VI',
			'Wallis And Futuna'                          => 'WF',
			'Yemen'                                      => 'YE',
			'Zimbabwe'                                   => 'ZW',
		);
	}
}
