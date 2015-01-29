<?php

require_once 'doorplate_new_entry.php';

function doorplate_entries_menu() {
	add_submenu_page( 'doorplate', 'New identity', 'New identity', 'publish_posts', 'doorplate_identity', 'doorplate_new_identity');
	add_submenu_page( 'doorplate', 'Manage jobs', 'Jobs', 'publish_posts', 'doorplate_jobs', 'doorplate_manage_jobs');
	add_submenu_page( 'doorplate', 'Manage courses', 'Courses', 'publish_posts', 'doorplate_courses', 'doorplate_manage_courses');
	add_submenu_page( 'doorplate', 'Manage skills', 'Skills', 'publish_posts', 'doorplate_skills', 'doorplate_manage_skills');
	add_submenu_page( 'doorplate', 'Manage demos', 'Demos', 'publish_posts', 'doorplate_demos', 'doorplate_manage_demos');
}

function doorplate_manage_jobs() {
	global $wpdb;
	$table = $wpdb->prefix . 'doorplate_entry_work';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$entry =$_POST["entry"];
		foreach($entry as $id => &$val)
			$val = $id;
		$entry_list = implode(",", $entry);
		if ($_POST["action"] == "show") {
			$sql = "UPDATE $table SET visible=1 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "hide") {
			$sql = "UPDATE $table SET visible=0 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "delete") {
			$sql = "DELETE FROM $table WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
	}
	echo '<div id="wrap"><a href="' . admin_url('admin.php?page=doorplate_create_job') . '">Add new job</a><br/>';
	echo "Entries not visible to visitors are greyed out.<br/>";
	$sql = "SELECT * FROM $table ORDER BY visible DESC, start_date;";
	if ($entries = $wpdb->get_results($sql)) {
		echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_jobs')) . '">';
		echo '<input type="radio" name="action" value="show">Show <input type="radio" name="action" value="hide">Hide <input type="radio" name="action" value="delete">Delete <input type="submit" value="Update">';
		echo '<table style="border-spacing : 1em">';
		foreach ($entries as $row) {
			echo '<tr' . (($row->visible) ? '' : ' style="color : #999999"') . '><td><input type="checkbox" name="entry[' . $row->id . ']">';
			echo '</td><td>' . $row->company . '</td>';
			echo '<td>' . $row->job . '</td>';
			echo '<td>from ' . $row->start_date . '</td>';
			echo '<td>to ' . $row->end_date . '</td>';
			echo '</tr>';
		}
		echo '</table></form></div>';
	}
}

function doorplate_manage_courses() {
	global $wpdb;
	$table = $wpdb->prefix . 'doorplate_entry_course';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$entry =$_POST["entry"];
		foreach($entry as $id => &$val)
			$val = $id;
		$entry_list = implode(",", $entry);
		if ($_POST["action"] == "show") {
			$sql = "UPDATE $table SET visible=1 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "hide") {
			$sql = "UPDATE $table SET visible=0 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "delete") {
			$sql = "DELETE FROM $table WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
	}
	echo '<div id="wrap"><a href="' . admin_url('admin.php?page=doorplate_create_course') . '">Add new course</a><br/>';
	echo "Entries not visible to visitors are greyed out.<br/>";
	$sql = "SELECT * FROM $table ORDER BY visible DESC, end_date;";
	if ($entries = $wpdb->get_results($sql)) {
		echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_courses')) . '">';
		echo '<input type="radio" name="action" value="show">Show <input type="radio" name="action" value="hide">Hide <input type="radio" name="action" value="delete">Delete <input type="submit" value="Update">';
		echo '<table style="border-spacing : 1em">';
		foreach ($entries as $row) {
			echo '<tr' . (($row->visible) ? '' : ' style="color : #999999"') . '><td><input type="checkbox" name="entry[' . $row->id . ']">';
			echo '</td><td>' . $row->name . '</td>';
			echo '<td>' . $row->end_date . '</td>';
			echo '</tr>';
		}
		echo '</table></form></div>';
	}
}

function doorplate_manage_skills() {
	global $wpdb;
	$table = $wpdb->prefix . 'doorplate_entry_skill';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$entry =$_POST["entry"];
		foreach($entry as $id => &$val)
			$val = $id;
		$entry_list = implode(",", $entry);
		if ($_POST["action"] == "show") {
			$sql = "UPDATE $table SET visible=1 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "hide") {
			$sql = "UPDATE $table SET visible=0 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "delete") {
			$sql = "DELETE FROM $table WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
	}
	echo '<div id="wrap"><a href="' . admin_url('admin.php?page=doorplate_create_skill') . '">Add new skill</a><br/>';
	echo "Entries not visible to visitors are greyed out.<br/>";
	$sql = "SELECT * FROM $table ORDER BY visible DESC;";
	if ($entries = $wpdb->get_results($sql)) {
		echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_skills')) . '">';
		echo '<input type="radio" name="action" value="show">Show <input type="radio" name="action" value="hide">Hide <input type="radio" name="action" value="delete">Delete <input type="submit" value="Update">';
		echo '<table style="border-spacing : 1em">';
		foreach ($entries as $row) {
			echo '<tr' . (($row->visible) ? '' : ' style="color : #999999"') . '><td><input type="checkbox" name="entry[' . $row->id . ']">';
			echo '</td><td>' . $row->name . '</td>';
			echo '</tr>';
		}
		echo '</table></form></div>';
	}
}

function doorplate_manage_demos() {
	global $wpdb;
	$table = $wpdb->prefix . 'doorplate_entry_demo';
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$entry =$_POST["entry"];
		foreach($entry as $id => &$val)
			$val = $id;
		$entry_list = implode(",", $entry);
		if ($_POST["action"] == "show") {
			$sql = "UPDATE $table SET visible=1 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "hide") {
			$sql = "UPDATE $table SET visible=0 WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
		else if ($_POST["action"] == "delete") {
			$sql = "DELETE FROM $table WHERE id IN ($entry_list);";
			$wpdb->query($sql);
		}
	}
	echo '<div id="wrap"><a href="' . admin_url('admin.php?page=doorplate_create_demo') . '">Add new demo</a><br/>';
	echo 'Entries not visible to visitors are greyed out.<br/>';
	$sql = "SELECT * FROM $table ORDER BY visible DESC;";
	if ($entries = $wpdb->get_results($sql)) {
		echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_demos')) . '">';
		echo '<input type="radio" name="action" value="show">Show <input type="radio" name="action" value="hide">Hide <input type="radio" name="action" value="delete">Delete <input type="submit" value="Update">';
		echo '<table style="border-spacing : 1em;">';
		foreach ($entries as $row) {
			echo '<tr' . (($row->visible) ? '' : ' style="color : #999999"') . '><td><input type="checkbox" name="entry[' . $row->id . ']">';
			echo '</td><td>' . $row->name . '</td>';
			echo '<td><a href=' . $row->file . '>Show file</a></td>';
			echo '</tr>';
		}
		echo '</table></form></div>';
	}
}

function doorplate_display_work() {
	$display = "<table>";
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_entry_work";
	$sql = "SELECT * FROM $table WHERE visible=1 ORDER BY start_date DESC;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$display .= "<tr>";
		$display .= "<td>$row->company</td>";
		$display .= "<td>$row->job</td>";
		$display .= "<td>from $row->start_date</td>";
		$display .= "<td>to $row->end_date</td>";
		$display .= "</tr>";
		if ($row->description != NULL) {
			$display .= "<tr>";
			$display .= "<td colspan=4 style=\"text-align:right\">$row->description</td>";
			$display .= "</tr>";
		}
	}
	$display .= "</table>";
	return $display;
}
add_shortcode('doorplate_work', 'doorplate_display_work');

function doorplate_display_course() {
	$display = "<table>";
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_entry_course";
	$sql = "SELECT * FROM $table WHERE visible=1 ORDER BY end_date DESC;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$display .= "<tr>";
		$display .= "<td>$row->name</td>";
		if ($row->end_date != NULL) {
			$display .= "<td>finished $row->end_date</td>";
		}
		$display .= "</tr>";
		if ($row->description != NULL) {
			$display .= "<tr>";
			$display .= "<td colspan=2 style=\"text-align:right\">$row->description</td>";
			$display .= "</tr>";
		}
	}
	$display .= "</table>";
	return $display;
}
add_shortcode('doorplate_course', 'doorplate_display_course');

function doorplate_display_skill() {
	$display = "<table>";
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_entry_skill";
	$sql = "SELECT * FROM $table WHERE visible=1;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$display .= "<tr>";
		$display .= "<td>$row->name</td>";
		$display .= "</tr>";
		if ($row->description != NULL) {
			$display .= "<tr>";
			$display .= "<td colspan=1 style=\"text-align:right\">$row->description</td>";
			$display .= "</tr>";
		}
	}
	$display .= "</table>";
	return $display;
}
add_shortcode('doorplate_skill', 'doorplate_display_skill');

function doorplate_display_demo() {
	$display = "<table>";
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_entry_demo";
	$sql = "SELECT * FROM $table WHERE visible=1;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$display .= "<tr>";
		$display .= "<td>$row->name</td>";
		$display .= "<td><a href=\"$row->file\">Download file</a></td>";
		$display .= "</tr>";
		if ($row->description != NULL) {
			$display .= "<tr>";
			$display .= "<td colspan=2 style=\"text-align:right\">$row->description</td>";
			$display .= "</tr>";
		}
	}
	$display .= "</table>";
	return $display;
}
add_shortcode('doorplate_demo', 'doorplate_display_demo');

?>
