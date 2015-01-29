<?php
/*
Plugin Name: Doorplate
Description: Create and manage database entries about your career.
Version: 1.0
Author: Dominik Plewa
Author URI: http://students.mimuw.edu.pl/~347206
*/

// setup plugin database
function doorplate_install_db () {
	global $wpdb;
	$prefix = $wpdb->prefix . 'doorplate_';
	$users = $prefix . 'users';
	$entry_work = $prefix . 'entry_work';
	$entry_course = $prefix . 'entry_course';
	$entry_skill = $prefix . 'entry_skill';
	$entry_demo = $prefix . 'entry_demo';
	$tag = $prefix . 'tag';
	$tag_work = $prefix . 'tag_work';
	$tag_course = $prefix . 'tag_course';
	$tag_skill = $prefix . 'tag_skill';
	$tag_demo = $prefix . 'tag_demo';


	// create or update basic table structure
	$sql = "CREATE TABLE $users (
			id INT(4) NOT NULL AUTO_INCREMENT,
			name VARCHAR(15) NOT NULL,
			surname VARCHAR(25) NOT NULL,
			email VARCHAR(25),
			phone INT(9),
			gender VARCHAR(10) NOT NULL,
			marital_status VARCHAR(25),
			birth_date DATE NOT NULL,
			picture VARCHAR(255),
			education VARCHAR(255),
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;

		CREATE TABLE $entry_work (
			id INT(9) NOT NULL AUTO_INCREMENT,
			user_id INT(4),
			description VARCHAR(255),
			visible INT(1) NOT NULL,
			company VARCHAR(128) NOT NULL,
			job VARCHAR(128) NOT NULL,
			start_date DATE NOT NULL,
			end_date DATE,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;

		CREATE TABLE $entry_course (
			id INT(9) NOT NULL AUTO_INCREMENT,
			user_id INT(4),
			description VARCHAR(255),
			visible INT(1) NOT NULL,
			name VARCHAR(128) NOT NULL,
			end_date DATE,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;

		CREATE TABLE $entry_skill (
			id INT(9) NOT NULL AUTO_INCREMENT,
			user_id INT(4),
			description VARCHAR(255),
			visible INT(1) NOT NULL,
			name VARCHAR(128) NOT NULL,
			PRIMARY KEY  (id)			
		) ENGINE=InnoDB;

		CREATE TABLE $entry_demo (
			id INT(9) NOT NULL AUTO_INCREMENT,
			user_id INT(4),
			description VARCHAR(255),
			visible INT(1) NOT NULL,
			name VARCHAR(128) NOT NULL,
			file VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;

		CREATE TABLE $tag (
			id INT(9) NOT NULL AUTO_INCREMENT,
			name VARCHAR(25) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB;

		CREATE TABLE $tag_work (
			id_work INT(9) NOT NULL,
			id_tag INT(9) NOT NULL,
			PRIMARY KEY  (id_work,id_tag)
		) ENGINE=InnoDB;

		CREATE TABLE $tag_course (
			id_course INT(9) NOT NULL,
			id_tag INT(9) NOT NULL,
			PRIMARY KEY  (id_course,id_tag)
		) ENGINE=InnoDB;

		CREATE TABLE $tag_skill (
			id_skill INT(9) NOT NULL,
			id_tag INT(9) NOT NULL,
			PRIMARY KEY  (id_skill,id_tag)
		) ENGINE=InnoDB;

		CREATE TABLE $tag_demo (
			id_demo INT(9) NOT NULL,
			id_tag INT(9) NOT NULL,
			PRIMARY KEY  (id_demo,id_tag)
		) ENGINE=InnoDB;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );	
	if (!get_option("doorplate_db_fk")) {
		// setup FOREIGN KEYS within plugin db tables
		$sql = "ALTER TABLE $entry_work
			ADD FOREIGN KEY (user_id)
			REFERENCES $users(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $entry_course	
			ADD FOREIGN KEY (user_id)
			REFERENCES $users(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $entry_skill
			ADD FOREIGN KEY (user_id)
			REFERENCES $users(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $entry_demo
			ADD FOREIGN KEY (user_id)
			REFERENCES $users(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_work
			ADD FOREIGN KEY (id_tag)
			REFERENCES $tag(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_work
			ADD FOREIGN KEY (id_work)
			REFERENCES $entry_work(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_course
			ADD FOREIGN KEY (id_tag)
			REFERENCES $tag(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_course
			ADD FOREIGN KEY (id_course)
			REFERENCES $entry_course(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_skill
			ADD FOREIGN KEY (id_tag)
			REFERENCES $tag(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_skill
			ADD FOREIGN KEY (id_skill)
			REFERENCES $entry_skill(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_demo
			ADD FOREIGN KEY (id_tag)
			REFERENCES $tag(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		$sql = "ALTER TABLE $tag_demo	
			ADD FOREIGN KEY (id_demo)
			REFERENCES $entry_demo(id)
			ON DELETE CASCADE
			ON UPDATE RESTRICT;";
		$wpdb->query($sql);

		add_option("doorplate_db_fk", 1);	
	}
}
register_activation_hook( __FILE__, 'doorplate_install_db' );

// setup plugin menus
require_once 'doorplate_admin_menu.php';
doorplate_install_menus();
?>