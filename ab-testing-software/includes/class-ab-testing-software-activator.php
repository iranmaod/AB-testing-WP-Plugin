<?php

/**
 * Fired during plugin activation
 *
 * @link       https://themartechstack.com
 * @since      1.0.0
 *
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ab_Testing_Software
 * @subpackage Ab_Testing_Software/includes
 * @author     Anthony Vasser <dev@themartechstack.com>
 */
class Ab_Testing_Software_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
        $tests_table = $wpdb->prefix . 'abtesting_tests';
        $visits_table = $wpdb->prefix . 'abtesting_visits';
        $license_table = $wpdb->prefix . 'abtesting_license';

        $test_sql = "CREATE TABLE IF NOT EXISTS `$tests_table` (
			`id` int NOT NULL AUTO_INCREMENT,
			`name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
			`page_id` int NOT NULL,
			`variants` text COLLATE utf8mb4_general_ci NOT NULL,
			`status` enum('running','paused','error','renew') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'running',
			`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$wpdb->query($test_sql);

		$visit_sql = "CREATE TABLE `$visits_table` (
			`id` int NOT NULL AUTO_INCREMENT,
			`test_id` int NOT NULL,
			`ip` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
			`page_id` int NOT NULL,
			`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$wpdb->query($visit_sql);

		$license_sql = "CREATE TABLE `$license_table` (
			`id` int NOT NULL AUTO_INCREMENT,
			`license` text COLLATE utf8mb4_general_ci NOT NULL,
			`startdate` datetime NOT NULL,
			`days` int NOT NULL,
			`tests` int NOT NULL,
			`active` TINYINT NOT NULL,
			`data` text COLLATE utf8mb4_general_ci NOT NULL,
			`enddate` datetime NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$wpdb->query($license_sql);
	}

}
