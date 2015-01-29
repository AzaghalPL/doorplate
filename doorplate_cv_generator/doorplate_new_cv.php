<?php

global $wpdb;

function doorplate_new_cv_menu() {
	add_submenu_page( 'doorplate_cvs', 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv', 'doorplate_new_cv');
	add_submenu_page( NULL, 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv_work', 'doorplate_new_cv_work');
	add_submenu_page( NULL, 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv_course', 'doorplate_new_cv_course');
	add_submenu_page( NULL, 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv_skill', 'doorplate_new_cv_skill');
	add_submenu_page( NULL, 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv_demo', 'doorplate_new_cv_demo');
	add_submenu_page( NULL, 'Create CV', 'Create CV', 'publish_posts', 'doorplate_new_cv_prep', 'doorplate_new_cv_prep');
}

function doorplate_new_cv() {
	global $wpdb;
	$tag_table = $wpdb->prefix . "doorplate_tag";
	$cv_table = $wpdb->prefix . "doorplate_cv";
	$sql = "SELECT * FROM $tag_table;";
	$tags = $wpdb->get_results($sql);
	echo '<form method="post" action="' . admin_url('admin.php?page=doorplate_new_cv_work') . '">';
	doorplate_choose_user_form("user_id");
	echo '<br/>Choose a name for your CV document: <input type="text" name="filename">.pdf<br/>';
	echo 'Choose tags for CV generation:<br/><table style="border-spacing : 1em">';
	foreach ($tags as $row) {
		echo '<tr><td><input type="checkbox" name="tag[' . $row->id . ']">';
		echo '</td><td>' . $row->name . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="Next"></form>';
}

function doorplate_new_cv_work() {
	global $wpdb;
	session_start();
	$_SESSION['filename'] = $_POST['filename'];
	var_dump($_SESSION['filename']);
	$_SESSION['user_id'] = $_POST['user_id'];
	$_SESSION['tag'] = $_POST['tag'];
	$user_id = $_SESSION['user_id'];
	foreach($_SESSION['tag'] as $id => &$tag) {
		$tag = $id;
	}
	$tag_table = $wpdb->prefix . "doorplate_tag_work";
	$work_table = $wpdb->prefix . "doorplate_entry_work";
	$sql = "SELECT id_work FROM $tag_table WHERE id_tag IN (" . implode(",", $_SESSION['tag']) . ");";
	$tagged_col = $wpdb->get_col($sql);
	foreach ($tagged_col as &$id) {
		$tagged[$id] = $id;
	}
	echo '<form method="post" action="' . admin_url('admin.php?page=doorplate_new_cv_course') . '">';
	echo 'Selected work entries:<br/><table style="border-spacing : 1em">';
	$sql = "SELECT * FROM $work_table WHERE user_id='$user_id';";
	$work = $wpdb->get_results($sql);
	foreach ($work as $row) {
		echo '<tr><td><input type="checkbox" name="work[' . $row->id . ']"'. ($tagged[$row->id]? ' checked' : '') . '>';
		echo '</td><td>' . $row->company . '</td>';
		echo '</td><td>' . $row->job . '</td>';
		echo '</td><td>from ' . $row->start_date . '</td>';
		echo '</td><td>to ' . $row->end_date . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="Next"></form>';
}

function doorplate_new_cv_course() {
	global $wpdb;
	session_start();
	$_SESSION['work_id'] = $_POST['work'];
	$user_id = $_SESSION['user_id'];
	$tag_table = $wpdb->prefix . "doorplate_tag_course";
	$course_table = $wpdb->prefix . "doorplate_entry_course";
	$sql = "SELECT id_course FROM $tag_table WHERE id_tag IN (" . implode(",", $_SESSION['tag']) . ");";
	$tagged_col = $wpdb->get_col($sql);
	foreach ($tagged_col as &$id) {
		$tagged[$id] = $id;
	}
	echo '<form method="post" action="' . admin_url('admin.php?page=doorplate_new_cv_skill') . '">';
	echo 'Selected course entries:<br/><table style="border-spacing : 1em">';
	$sql = "SELECT * FROM $course_table WHERE user_id='$user_id';";
	$course = $wpdb->get_results($sql);
	foreach ($course as $row) {
		echo '<tr><td><input type="checkbox" name="course[' . $row->id . ']"'. ($tagged[$row->id]? ' checked' : '') . '>';
		echo '</td><td>' . $row->name . '</td>';
		echo '</td><td>' . $row->end_date . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="Next"></form>';
}

function doorplate_new_cv_skill() {
	global $wpdb;
	session_start();
	$_SESSION['course_id'] = $_POST['course'];
	$user_id = $_SESSION['user_id'];
	$tag_table = $wpdb->prefix . "doorplate_tag_skill";
	$skill_table = $wpdb->prefix . "doorplate_entry_skill";
	$sql = "SELECT id_skill FROM $tag_table WHERE id_tag IN (" . implode(",", $_SESSION['tag']) . ");";
	$tagged_col = $wpdb->get_col($sql);
	foreach ($tagged_col as &$id) {
		$tagged[$id] = $id;
	}
	echo '<form method="post" action="' . admin_url('admin.php?page=doorplate_new_cv_demo') . '">';
	echo 'Selected skill entries:<br/><table style="border-spacing : 1em">';
	$sql = "SELECT * FROM $skill_table WHERE user_id='$user_id';";
	$skill = $wpdb->get_results($sql);
	foreach ($skill as $row) {
		echo '<tr><td><input type="checkbox" name="skill[' . $row->id . ']"'. ($tagged[$row->id]? ' checked' : '') . '>';
		echo '</td><td>' . $row->name . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="Next"></form>';
}

function doorplate_new_cv_demo() {
	global $wpdb;
	session_start();
	$_SESSION['skill_id'] = $_POST['skill'];
	$user_id = $_SESSION['user_id'];
	$tag_table = $wpdb->prefix . "doorplate_tag_demo";
	$demo_table = $wpdb->prefix . "doorplate_entry_demo";
	$sql = "SELECT id_demo FROM $tag_table WHERE id_tag IN (" . implode(",", $_SESSION['tag']) . ");";
	$tagged_col = $wpdb->get_col($sql);
	foreach ($tagged_col as &$id) {
		$tagged[$id] = $id;
	}
	echo '<form method="post" action="' . admin_url('admin.php?page=doorplate_new_cv_prep') . '">';
	echo 'Selected demo entries:<br/><table style="border-spacing : 1em">';
	$sql = "SELECT * FROM $demo_table WHERE user_id='$user_id';";
	$demo = $wpdb->get_results($sql);
	foreach ($demo as $row) {
		echo '<tr><td><input type="checkbox" name="demo[' . $row->id . ']"'. ($tagged[$row->id]? ' checked' : '') . '>';
		echo '</td><td>' . $row->name . '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<input type="submit" value="Next"></form>';
}

function doorplate_new_cv_prep() {
	global $wpdb;
	session_start();
	$_SESSION['demo_id'] = $_POST['demo'];
	$user_id = $_SESSION['user_id'];
	foreach($_SESSION['work_id'] as $id => $val) {
		$work_id[$id] = $id;
	}
	foreach($_SESSION['course_id'] as $id => $val) {
		$course_id[$id] = $id;
	}
	foreach($_SESSION['skill_id'] as $id => $val) {
		$skill_id[$id] = $id;
	}
	foreach($_SESSION['demo_id'] as $id => $val) {
		$demo_id[$id] = $id;
	}
	$user_id = $_SESSION['user_id'];
	$work_table = $wpdb->prefix . "doorplate_entry_work";
	$course_table = $wpdb->prefix . "doorplate_entry_course";
	$skill_table = $wpdb->prefix . "doorplate_entry_skill";
	$demo_table = $wpdb->prefix . "doorplate_entry_demo";
	$user_table = $wpdb->prefix . "doorplate_users";
	$cv_table = $wpdb->prefix . "doorplate_cv";
	$_SESSION['work'] = $wpdb->get_results("SELECT * FROM $work_table WHERE id IN (" . implode(",", $work_id) . ") AND user_id='$user_id';");
	$_SESSION['course'] = $wpdb->get_results("SELECT * FROM $course_table WHERE id IN (" . implode(",", $course_id) . ") AND user_id='$user_id';");
	$_SESSION['skill'] = $wpdb->get_results("SELECT * FROM $skill_table WHERE id IN (" . implode(",", $skill_id) . ") AND user_id='$user_id';");
	$_SESSION['demo'] = $wpdb->get_results("SELECT * FROM $demo_table WHERE id IN (" . implode(",", $demo_id) . ") AND user_id='$user_id';");
	$_SESSION['user'] = $wpdb->get_row("SELECT * FROM $user_table WHERE id='". $user_id. "';");
	$upload_dir = wp_upload_dir();
	$_SESSION['upload_dir'] = $upload_dir;
	$filename = $_SESSION['filename'];
	$fileurl = $upload_dir['url']  . "/cv_" . $filename . ".pdf";
	$sql = "INSERT INTO $cv_table (user_id, file) VALUES ('$user_id', '$fileurl');";
	$wpdb->query($sql);
	echo "Overview:<br/>". count($_SESSION['work']) . " work entries,<br/>". count($_SESSION['course']) . " course entries,<br/>". count($_SESSION['skill']) . " skill entries,<br/>". count($_SESSION['demo']) . " demo entries<br/>";
	echo '<form method="post" action="' . plugins_url('render_cv.php', __FILE__) . '">';
	echo '<br/><input type="submit" value="Generate!"></form>';
}

?>