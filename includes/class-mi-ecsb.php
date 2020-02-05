<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/includes
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      EasyCountrySpamBlocker_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if ( defined( 'EASYCOUNTRYSPAMBLOCKER_VERSION' ) )
		{
			$this->version = EASYCOUNTRYSPAMBLOCKER_VERSION;
		}
		else
		{
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'mi-ecsb';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register_form_actions();
		$this->try_redirect();
	}
	
	

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - EasyCountrySpamBlocker_Loader. Orchestrates the hooks of the plugin.
	 * - EasyCountrySpamBlocker_i18n. Defines internationalization functionality.
	 * - EasyCountrySpamBlocker_Admin. Defines all hooks for the admin area.
	 * - EasyCountrySpamBlocker_Public. Defines all hooks for the public side of the site.
	 * - EasyCountrySpamBlocker_Form_Actions. Defines all form and AJAX actions that are used in the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mi-ecsb-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mi-ecsb-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mi-ecsb-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mi-ecsb-public.php';
		
		/**
		 * The class responsible for defining all form and AJAX actions that are used in the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mi-ecsb-form-actions.php';
		
		/**
		 * The class responsible for redirecting the visitor if conditions are met.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mi-ecsb-redirect.php';
		
		// Create an instance of the loader.
		$this->loader = new EasyCountrySpamBlocker_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the EasyCountrySpamBlocker_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new EasyCountrySpamBlocker_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new EasyCountrySpamBlocker_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_loader() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		
		$this->loader->add_filter( 'plugin_action_links_' . EASYCOUNTRYSPAMBLOCKER_PLUGIN_BASENAME, $plugin_admin, 'add_settings_link' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new EasyCountrySpamBlocker_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}
	
	/**
	 * Register all form and AJAX actions used by the plguin.
	 * 
	 * @since   1.0.0
	 * @access  private
	 */
	private function register_form_actions()
	{
		$plugin_form_actions = new EasyCountrySpamBlocker_Form_Actions( $this->get_plugin_name(), $this->get_version(), $this->get_loader() );
		
		// Register traditional form actions.
		$plugin_form_actions->register_form_actions();
		
		// Register AJAX form actions.
		$plugin_form_actions->register_ajax_actions();
	}
	
	/**
	 * Tries to redirect the visitor. This will only happen if the required conditions are met.
	 * 
	 * @since  1.0.0
	 * @access private
	 */
	private function try_redirect()
	{
		$plugin_redirect = new EasyCountrySpamBlocker_Redirect( $this->get_plugin_name(), $this->get_version() );
		
		// Attempt to redirect.
		$this->loader->add_action( 'init', $plugin_redirect, 'redirect' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    EasyCountrySpamBlocker_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
