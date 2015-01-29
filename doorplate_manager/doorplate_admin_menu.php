<?php

function doorplate_install_menus() {
	require_once 'doorplate_entries.php';
	require_once 'doorplate_new_entry.php';
	add_action (admin_menu, doorplate_admin_menu);
	add_action (admin_menu, doorplate_entries_menu);
	add_action (admin_menu, doorplate_entry_creation_pages);
	do_action("doorplate_menus");
}

function doorplate_admin_menu() {
	add_object_page( 'Doorplate', 'Doorplate', 'publish_posts', 'doorplate', 'doorplate_homepage' );
}

function doorplate_homepage() {
	echo "<div>";
// menu items again
	echo "Welcome to Doorplate management panel! From here you can: <br/><ul>";
	echo "<li><a href = " . admin_url('admin.php?page=doorplate_jobs') . ">Manage jobs</a></li>";
	echo "<li><a href = " . admin_url('admin.php?page=doorplate_courses') . ">Manage courses</a></li>";
	echo "<li><a href = " . admin_url('admin.php?page=doorplate_skills') . ">Manage skills</a></li>";
	echo "<li><a href = " . admin_url('admin.php?page=doorplate_demos') . ">Manage demos</a></li>";
	echo "</ul></div>";
}

?>
