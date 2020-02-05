<?php

/**
 * Fired during plugin activation.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker_Activator
{
	/**
	 * Activates EasyCountrySpamBlocker.
	 * 
	 * Sets up any required database data, such as the redirect URL.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		// Set the default redirect URL in the database.
		EasyCountrySpamBlocker_Helper::save_url( EasyCountrySpamBlocker_Helper::get_url() );
	}
}
