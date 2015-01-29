<?php

function doorplate_get_tags($taglist) {
	$tags = explode(",", $taglist);
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_tag";
	foreach ($tags as &$tag) {
		$tag = trim($tag);
		if ($tag != "") {
			$sql = "SELECT id FROM $table WHERE name = '$tag';";
			$result = $wpdb->get_row($sql);
			if ($result == NULL) {
				$wpdb->insert ($table, array ("name" => $tag));
				$tag = $wpdb->insert_id;
			} else {
				$tag = $result->id;
			}
		}
	}
	return $tags;
}

function doorplate_choose_user_form($inputname) {
	echo '<select name="'.$inputname.'">';
	global $wpdb;
	$table = $wpdb->prefix . "doorplate_users";
	$sql = "SELECT id, name, surname FROM $table;";
	$results = $wpdb->get_results($sql);
	foreach($results as $row) {
		echo '<option value="'.$row->id.'">'.$row->name.' '.$row->surname.'</option>';
	}
	echo '</select>';
	return;
}

function doorplate_entry_creation_pages() {
	add_submenu_page( NULL, 'Create job', 'Jobs', 'publish_posts', 'doorplate_create_job', 'doorplate_create_job');
	add_submenu_page( NULL, 'Create course', 'Courses', 'publish_posts', 'doorplate_create_course', 'doorplate_create_course');
	add_submenu_page( NULL, 'Create skill', 'Skills', 'publish_posts', 'doorplate_create_skill', 'doorplate_create_skill');
	add_submenu_page( NULL, 'Create demo', 'Demos', 'publish_posts', 'doorplate_create_demo', 'doorplate_create_demo');
}

function get_user_usos_courses( $user_id, $provider, $hybridauth_user_profile ) {
	if( 'Usosweb' != $provider ) {
		return;
	}
    include_once( WORDPRESS_SOCIAL_LOGIN_ABS_PATH . '/hybridauth/Hybrid/Auth.php' );
    try {
		$provider = Hybrid_Auth::getAdapter( 'Usosweb' );
		global $wpdb;
		$doorplate_user = $wpdb->prefix . "doorplate_users";
		$doorplate_courses = $wpdb->prefix . "doorplate_entry_course";
		$usos_user = $provider->api()->get( 'https://usosapps.uw.edu.pl/services/users/user?fields=first_name|last_name' );
		$sql = "SELECT id FROM $doorplate_user WHERE name = '$usos_user->first_name' AND surname = '$usos_user->last_name';";
		$user = $wpdb->get_row($sql);
		if ($user != NULL) {
			$response = $provider->api()->get( 'https://usosapps.uw.edu.pl/services/courses/user?fields=course_editions&active_terms_only=false' );
			foreach($response->course_editions as $term) {
				foreach($term as $course) {
					//file_put_contents('dblog', var_export($course));
					// $grade = $provider->api()->get( "https://usosapps.uw.edu.pl/services/grades/course_edition?course_id=$course->course_id&term_id=$course->term_id&fields=value_symbol" );
					// //file_put_contents('dblog', var_export($grade) . "\n", FILE_APPEND);
					// if (!property_exists($grade, "error")) {
						//file_put_contents('dblog', $sql . "\n", FILE_APPEND);
						$course_name = $course->course_name->{'en'};
						$wpdb->insert($doorplate_courses, array('user_id' => "$user->id", 'name' => "$course_name"));
					// }				
				}
			}
		}
	}
	catch( Exception $e )
	{
		echo "Ooophs, we got an error: " . $e->getMessage();
	}
}
add_filter( 'wsl_hook_process_login_before_wp_set_auth_cookie', 'get_user_usos_courses', 10, 3 );

function doorplate_new_identity() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$name = $_POST["name"];
		$surname = $_POST["surname"];
		$birth_date = $_POST["birth_date"];
		$gender = $_POST["gender"];
		$marital_status = $_POST["marital_status"];
		$email = $_POST["email"];
		$phone = $_POST["phone"];
		$education = $_POST["education"];
		$picture = $_POST["picture"];
		$error = "";
		if ($name == "") 
			$error .="Name is a required field!<br/>";
		if ($surname == "")
			$error .="Surname is a required field!<br/>";
		if ($birth_date == "") 
			$error .="Birth date is a required field!<br/>";
		if ($gender == "")
			$error .="Gender is a required field!<br/>";
		if ($error == "") {
			global $wpdb;
			$table = $wpdb->prefix . "doorplate_users";
			$sql = "INSERT INTO $table (name, surname, birth_date, gender, marital_status, email, phone, education, picture)
			VALUES ('$name', '$surname', '$birth_date', '$gender'" . 
				(($marital_status == "") ? ", NULL" : ", '$marital_status'") . 
				(($email == "") ? ", NULL" : ", '$email'") . 
				(($phone == "") ? ", NULL" : ", '$phone'") . 
				(($education == "") ? ", NULL" : ", '$education'") . 
				(($picture == "") ? ", NULL" : ", '$picture'") . ");";
			if ($wpdb->query($sql)) {
				$name = $surname = $birth_date = $gender = $marital_status = $email = $phone = $education = "";
				echo '<p style="color: #00FF00">Created new identity!</p></br>';
			}
		}
		else {
			echo '<p style="color: #FF0000">'.$error.'</p>';
		}
	}
	
	echo 'Creating a new identity (required fields are marked with an asterisk *)<br/>';
	echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_identity')) . '">';
	echo 'Name * : <input type="text" name="name" value="'.$name.'"><br/>';
	echo 'Surname * : <input type="text" name="surname" value="'.$surname.'""><br/>';
	echo 'Birth date * (yyyy-mm-dd): <input type="date" name="birth_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="'.$birth_date.'"><br/>';
	echo 'Gender * : <input type="radio" name="gender" value="male">Male 
<input type="radio" name="gender" value="female">Female<br/>';
	echo 'Marital status:  <select name="marital_status">
				<option value="">--</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="widowed">Widowed</option>
        </select><br/>';
	echo 'Email: <input type="email" name="email"><br/>';
	echo 'Phone: <input type="text" name="phone"><br/>';
	echo 'Education: <input type="text" name="education"><br/>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

function doorplate_create_job() {
	//as if it was that easy, heh
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_id = $_POST["user_id"];
		$company = $_POST["company"];
		$job = $_POST["job"];
		$description = $_POST["description"];
		$start_date = $_POST["start_date"];
		$end_date = $_POST["end_date"];
		$taglist = $_POST["taglist"];
		$error = "";
		if ($company == "") 
			$error .="Company is a required field!<br/>";
		if ($job == "")
			$error .="Job is a required field!<br/>";
		if ($start_date == "") 
			$error .="Start date is a required field!<br/>";
		if ($end_date == "")
			$error .="End date is a required field!<br/>";
		if ($error == "") {
			global $wpdb;
			$table = $wpdb->prefix . "doorplate_entry_work";
			$sql = "INSERT INTO $table (user_id, company, job, start_date, end_date, description)
			VALUES ('$user_id', '$company', '$job', '$start_date', '$end_date'" . 
				(($description == "") ? ", NULL" : ", '$description'") . ");";
			if ($wpdb->query($sql)) {
				$id = mysql_insert_id();
				$tags = doorplate_get_tags($taglist);
				$tag_table = $wpdb->prefix . "doorplate_tag_work";
				foreach ($tags as $tag) {
					$wpdb->insert($tag_table, array( "id_tag" => "$tag", "id_work" => "$id"));
				}
				$company = $job = $start_date = $end_date = $description = $taglist = "";
				echo '<p style="color: #00FF00">Created new job entry!</p></br>';
			}
		}
		else {
			echo '<p style="color: #FF0000">'.$error.'</p>';
		}
	}
	echo 'Creating a new job entry (required fields are marked with an asterisk *)<br/>';
	echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_create_job')) . '">';
	echo 'Identity * :';
	doorplate_choose_user_form("user_id");
	echo '<br/>';
	echo 'Company * : <input type="text" name="company" value="'.$company.'"><br/>';
	echo 'Job * : <input type="text" name="job" value="'.$job.'"><br/>';
	echo 'Description (max 255 characters):<br/><input type="textarea" name="description" value="'.$description.'"><br/>';
	echo 'Start date * (yyyy-mm-dd): <input type="date" name="start_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="'.$start_date.'"><br/>';
	echo 'End date * (yyyy-mm-dd): <input type="date" name="end_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="'.$end_date.'"><br/>';
	echo 'Add tags separated with commas:<br/><input type="textarea" name="taglist" value="'.$taglist.'"><br/>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

function doorplate_create_course() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_id = $_POST["user_id"];
		$name = $_POST["name"];
		$description = $_POST["description"];
		$end_date = $_POST["end_date"];
		$taglist = $_POST["taglist"];
		$error = "";
		if ($name == "") 
			$error .="Course name is a required field!<br/>";
		if ($error == "") {
			global $wpdb;
			$table = $wpdb->prefix . "doorplate_entry_course";
			$sql = "INSERT INTO $table (user_id, name, description, end_date)
			VALUES ('$user_id', '$name'" . 
				(($description == "") ? ", NULL" : ", '$description'") .
				(($end_date == "") ? ", NULL" : ", '$end_date'") . ");";
			if ($wpdb->query($sql)) {
				$id = mysql_insert_id();
				$tags = doorplate_get_tags($taglist);
				$tag_table = $wpdb->prefix . "doorplate_tag_course";
				foreach ($tags as $tag) {
					$wpdb->insert($tag_table, array( "id_tag" => "$tag", "id_course" => "$id"));
				}
				$name = $end_date = $description = $taglist = "";
				echo '<p style="color: #00FF00">Created new course entry!</p></br>';
			}
		}
		else {
			echo '<p style="color: #FF0000">'.$error.'</p>';
		}
	}
	echo 'Creating a new course entry (required fields are marked with an asterisk *)<br/>';
	echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_create_course')) . '">';
	echo 'Identity * :';
	doorplate_choose_user_form("user_id");
	echo '<br/>';
	echo 'Course name * : <input type="text" name="name" value="'.$name.'"><br/>';
	echo 'Description (max 255 characters):<br/><input type="textarea" name="description" value="'.$description.'"><br/>';
	echo 'Date (yyyy-mm-dd): <input type="date" name="end_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="'.$end_date.'"><br/>';
	echo 'Add tags separated with commas:<br/><input type="textarea" name="taglist" value="'.$taglist.'"><br/>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

function doorplate_create_skill() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_id = $_POST["user_id"];
		$name = $_POST["name"];
		$description = $_POST["description"];
		$taglist = $_POST["taglist"];
		$error = "";
		if ($name == "") 
			$error .="Skill name is a required field!<br/>";
		if ($error == "") {
			global $wpdb;
			$table = $wpdb->prefix . "doorplate_entry_skill";
			$sql = "INSERT INTO $table (user_id, name, description)
			VALUES ('$user_id', '$name'" . 
				(($description == "") ? ", NULL" : ", '$description'") . ");";
			if ($wpdb->query($sql)) {
				$id = mysql_insert_id();
				$tags = doorplate_get_tags($taglist);
				$tag_table = $wpdb->prefix . "doorplate_tag_skill";
				foreach ($tags as $tag) {
					$wpdb->insert($tag_table, array( "id_tag" => "$tag", "id_skill" => "$id"));
				}
				$name = $description = $taglist = "";
				echo '<p style="color: #00FF00">Created new skill entry!</p></br>';
			}
		}
		else {
			echo '<p style="color: #FF0000">'.$error.'</p>';
		}
	}
	echo 'Creating a new skill entry (required fields are marked with an asterisk *)<br/>';
	echo '<form method="post" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_create_skill')) . '">';
	echo 'Identity * :';
	doorplate_choose_user_form("user_id");
	echo '<br/>';
	echo 'Skill name * : <input type="text" name="name" value="'.$name.'"><br/>';
	echo 'Description (max 255 characters):<br/><input type="textarea" name="description" value="'.$description.'"><br/>';
	echo 'Add tags separated with commas:<br/><input type="textarea" name="taglist" value="'.$taglist.'"><br/>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

function doorplate_create_demo() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$user_id = $_POST["user_id"];
		$name = $_POST["name"];
		$description = $_POST["description"];
		$taglist = $_POST["taglist"];
		// check required fields
		$error = "";
		if ($name == "") {
			$error .="Demo name is a required field!<br/>";
		}
		if (isset( $_FILES["file-upload"] )) {
			$file = wp_handle_upload( $_FILES["file-upload"], array('test_form' => false));
			$file = $file["url"];
		}
		else
			$error .="File is a required field!<br/>";
		if ($error == "") {
			global $wpdb;
			$table = $wpdb->prefix . "doorplate_entry_demo";
			$sql = "INSERT INTO $table (user_id, name, file, description)
			VALUES ('$user_id', '$name', '$file'" . 
				(($description == "") ? ", NULL" : ", '$description'") . ");";
			if ($wpdb->query($sql)) {
				$id = mysql_insert_id();
				$tags = doorplate_get_tags($taglist);
				print_r($tags);
				$tag_table = $wpdb->prefix . "doorplate_tag_demo";
				foreach ($tags as $tag) {
					$wpdb->insert($tag_table, array( "id_tag" => "$tag", "id_demo" => "$id"));
				}
				$name = $file = $description = $taglist = "";
				echo '<p style="color: #00FF00">Created new demo entry!</p></br>';
			}
		}
		else {
			echo '<p style="color: #FF0000">'.$error.'</p>';
		}
	}
	echo 'Creating a new demo entry (required fields are marked with an asterisk *)<br/>';
	echo '<form method="post" enctype="multipart/form-data" action="' . htmlspecialchars(admin_url('admin.php?page=doorplate_create_demo')) . '">';
	echo 'Identity * :';
	doorplate_choose_user_form("user_id");
	echo '<br/>';
	echo 'Demo name * : <input type="text" name="name" value="'.$name.'"><br/>';
	echo 'Description (max 255 characters):<br/><input type="textarea" name="description" value="'.$description.'"><br/>';
	echo 'File * : <input type="file" name="file-upload"><br/>';
	echo 'Add tags separated with commas:<br/><input type="textarea" name="taglist" value="'.$taglist.'"><br/>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
}

?>