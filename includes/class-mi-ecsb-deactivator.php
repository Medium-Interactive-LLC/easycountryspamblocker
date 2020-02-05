<?php

/**
 * Fired during plugin deactivation.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker_Deactivator
{
	/**
	 * Remove EasyCountrySpamBlocker data from the database.
	 * 
	 * Any saved data that has been entered into the database, such as the redirect URL, will be removed here.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// Set the default redirect URL in the database.
		EasyCountrySpamBlocker_Helper::delete_url();
	}
}
