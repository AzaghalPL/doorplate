<?php
/*
Plugin Name: Doorplate PDF generator extension
Description: Create CV documents based on Dorrplate entries. Please don't activate unless Doorplate plugin is activated too.
Version: 1.0
Author: Dominik Plewa
Author URI: http://students.mimuw.edu.pl/~347206
*/

// setup plugin database
function doorplate_install_cv_db () {
	global $wpdb;
	$prefix = $wpdb->prefix . 'doorplate_';
	$users = $prefix . 'users';
	$cv = $prefix . 'cv';

	// create or update basic table structure
	$sql = "CREATE TABLE $cv (
			id INT(9) NOT NULL AUTO_INCREMENT,
			user_id INT(4),
			file VARCHAR(255) NOT NULL,
			visible INT(1) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	if (!get_option("doorplate_db_cv_fk")) {
		// setup FOREIGN KEYS within plugin db tables
		$sql = "ALTER TABLE $cv
			ADD FOREIGN KEY (user_id)
			REFERENCES $users(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		add_option("doorplate_db_cv_fk", 1);	
	}
}
register_activation_hook( __FILE__, 'doorplate_install_cv_db' );

function doorplate_cv_menu() {
	require_once 'doorplate_manage_cv.php';
	add_object_page( 'Doorplate CV', 'Doorplate CV', 'publish_posts', 'doorplate_cvs', 'doorplate_cvs' );
}

require_once 'doorplate_new_cv.php';
add_action ('admin_menu', 'doorplate_cv_menu');
add_action ('admin_menu', 'doorplate_new_cv_menu');

require_once 'doorplate_manage_cv.php';
add_shortcode('doorplate_cv', 'doorplate_display_cv');

?>
