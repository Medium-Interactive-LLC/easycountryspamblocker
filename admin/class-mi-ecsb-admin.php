<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mediuminteractive.com/
 * @since      1.0.0
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    EasyCountrySpamBlocker
 * @subpackage EasyCountrySpamBlocker/admin
 * @author     Medium Interactive, LLC <contact@mediuminteractive.com>
 */
class EasyCountrySpamBlocker_Admin
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
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	
	/**
	 * Adds a back-end admin menu for ECSB.
	 * 
	 * @since 1.0.0
	 */
	public function add_admin_menu()
	{
		add_options_page(
			__( 'Easy Country Spam Blocker', 'mi-ecsb' ),
			__( 'Easy Country Spam Blocker', 'mi-ecsb' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'render_admin_menu' )
		);
	}

	/**
	 * Renders the admin menu.
	 * 
	 * @since 1.0.0
	 */
	public function render_admin_menu()
	{
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/mi-ecsb-admin-display.php';
	}
	
	/**
	 * Adds a Settings link to the 'Plugins' page for this plugin in the WordPress admin area.
	 * 
	 * @since 1.0.0
	 */
	public function add_settings_link( $actions )
	{
		// Setup the settings link.
		$settings_link = '<a href="' . esc_url( get_admin_url() ) . 'options-general.php?page=' . $this->plugin_name . '">' . __( 'Settings', 'mi-ecsb' ) . '</a>';
		
		// Push the settings link to the actions list.
		array_push( $actions, $settings_link );
		
		return $actions;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in EasyCountrySpamBlocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The EasyCountrySpamBlocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mi-ecsb-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in EasyCountrySpamBlocker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The EasyCountrySpamBlocker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// Register the script.
		wp_register_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/mi-ecsb-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);

        // Localize the script with required data.
        $data = array(
        	'AjaxURL'          => esc_url( admin_url( 'admin-ajax.php' ) ),
        	'UnkownSuccessMsg' => __( 'Hmm... Everything seems to have worked; however, we could not fetch some data. Try refreshing the page. If the problem persists, please contact Medium Interactive.', 'mi-ecsb' ),
        	'ErrorCodeMsg'     => __( 'Error Code:', 'mi-ecsb' ),
        	'UnknownErrorMsg'  => __( 'Hmm... That was a weird error. Try refreshing the page. If the problem persists, please contact Medium Interactive.', 'mi-ecsb' ),
        	'InvalidURLMsg'    => __( 'Input does not end with a domain extension. Please enter a valid URL.', 'mi-ecsb' )
    	);
        wp_localize_script( $this->plugin_name, 'ECSBAdmin', $data );
        
        // Enqueued the script with localized data.
        wp_enqueue_script( $this->plugin_name );
	}
}
