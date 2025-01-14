<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://themartechstack.com
 * @since      1.0.0
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/public
 * @author     Anthony Vasser <dev@themartechstack.com>
 */
class Ab_Testing_Software_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ab-testing-software-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ab-testing-software-public.js', array( 'jquery' ), $this->version, false );

	}

	function check_test_redirect () {
		global $wpdb;

		$page_id = get_the_ID();
		
		$test_table = $wpdb->prefix . "abtesting_tests";
		$test_result = $wpdb->get_results ("SELECT * FROM $test_table WHERE `page_id` = '".$page_id."' AND `status` = 'running'");
		if ( count($test_result) > 0 ) {
			$variants = unserialize($test_result[0]->variants);
			$visit_table = $wpdb->prefix . "abtesting_visits";
			$visit = $wpdb->get_results ("SELECT * FROM $visit_table WHERE `test_id` = '".$test_result[0]->id."' AND `ip` = '".$this->getIP()."'");
			if (empty($visit)) {
				$wpdb->insert( $visit_table, array(
					'test_id' 	=> $test_result[0]->id,
					'ip'   		=> $this->getIP(),
					'page_id'   => $variants[0]['page_id']),
					array( '%s', '%s', '%s' ) 
				);

				wp_redirect( get_permalink( $variants[0]['page_id'] ) );
				exit;
			} else {
				$last_names = array_column($variants, 'page_id');
				$key = array_search ($visit[0]->page_id, $last_names);
				
				if ( isset($last_names[$key+1]) ) {
					$page_id = $last_names[$key+1];
				} else {
					$page_id = $last_names[0];
				}
				$data = array(
					'page_id' => $page_id
				);

				$wpdb->update($visit_table, $data, array('id' => $visit[0]->id));
				wp_redirect( get_permalink( $page_id ) );
				exit;
			}

		}
	}

	function getIP() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
		{
			if (array_key_exists($key, $_SERVER) === true)
			{
				foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip)
				{
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)
					{
						return $ip;
					}
				}
			}
		}
	}

}
