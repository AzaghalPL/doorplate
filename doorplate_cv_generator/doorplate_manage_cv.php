<?php

function doorplate_cv_manage_menu() {
	add_submenu_page( 'doorplate', 'Manage CVs', 'CVs', 'publish_posts', 'doorplate_cvs', 'doorplate_cvs');
}

function doorplate_cvs() {
	global $wpdb;
	$table = $wpdb->prefix . 'doorplate_cv';
	$users = $wpdb->prefix . 'doorplate_users';
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
	echo '<div id="wrap"><a href="' . admin_url('admin.php?page=doorplate_new_cv') . '">Create new CV</a><br/>';
	echo "Entries not visible to visitors are greyed out.<br/>";
	$sql = "SELECT * FROM $table ORDER BY visible DESC;";
	if ($entries = $wpdb->get_results($sql)) {
		echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_cvs')) . '">';
		echo '<input type="radio" name="action" value="show">Show <input type="radio" name="action" value="hide">Hide <input type="radio" name="action" value="delete">Delete <input type="submit" value="Update">';
		echo '<table style="border-spacing : 1em">';
		foreach ($entries as $row) {
			echo '<tr><td><input type="checkbox" name="entry[' . $row->id . ']">';
			echo '</td><td><a href="' . $row->file . '"'. (($row->visible) ? '' : ' style="color : #999999"') . '>' . basename($row->file) . '</a></td>';
			$sql = "SELECT name, surname FROM $users WHERE id='$user_id';";
			$user = $wpdb->get_row($sql);
			echo '<td>' . $user->name . ' ' . $user->surname . '</td>';
			echo '</tr>';
		}
		echo '</table></form></div>';
	}
}

function doorplate_display_cv() {
	$display = "<table>";
	global $wpdb;
	$users = $wpdb->prefix . "doorplate_users";
	$table = $wpdb->prefix . "doorplate_cv";
	$sql = "SELECT * FROM $table WHERE visible=1;";
	$rows = $wpdb->get_results($sql);
	foreach($rows as $row) {
		$display .= "<tr>";
		$display .= "<td><a href=\"' . $row->file . '\">" . basename($row->file) . "</a></td>";
		$sql = "SELECT name, surname FROM $users WHERE id='".$row->user_id."';";
		$user = $wpdb->get_row($sql);
		$display .= "<td>$user->name $user->surname</td>";
		$display .= "</tr>";
	}
	$display .= "</table>";
	return $display;
}

?>