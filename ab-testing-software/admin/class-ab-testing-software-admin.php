<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themartechstack.com
 * @since      1.0.0
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/admin
 * @author     Anthony Vasser <dev@themartechstack.com>
 */
class Ab_Testing_Software_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ab_Testing_Software_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ab_Testing_Software_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ab-testing-software-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ab_Testing_Software_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ab_Testing_Software_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ab-testing-software-admin.js', array( 'jquery' ), $this->version, false );

	}

	function admin_testingsoftware_page()
	{
		add_menu_page(
			$this->plugin_name,
			'AB Testing Software',
			'administrator',
			$this->plugin_name,
			array(
				$this,
				'displayAdminDashboard',
			),
			'dashicons-email',
			20
		);

		add_submenu_page(
			$this->plugin_name,
			'Create New',
			'Create New',
			'administrator',
			$this->plugin_name . '-new',
			array(
				$this,
				'displayCreateNewAB',
			)
		);

		add_submenu_page(
			'null',
			'License',
			'License',
			'administrator',
			$this->plugin_name . '-license',
			array(
				$this,
				'displayLicenseScreen',
			)
		);
	}
	
	function displayAdminDashboard(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ab-testing-software-admin-display.php';
	}
	
	function displayCreateNewAB(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ab-testing-software-admin-create.php';
	}

	function displayLicenseScreen() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/ab-testing-software-admin-license.php';
	}

	function tests_action_callback(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/load_posts_data.php';
	}
}
