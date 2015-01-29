<?php
	session_start();
	$upload_dir = $_SESSION['upload_dir'];
	$filename = $_SESSION['filename'] . ".pdf";
	$user = $_SESSION['user'];
	$work = $_SESSION['work'];
	$course = $_SESSION['course'];
	$skill = $_SESSION['skill'];
	$demo = $_SESSION['demo'];
	require_once("fpdf.php");
	$CV = new FPDF();
	$CV->AddPage();
// print user
	$CV->SetFont('Arial', 'B', 12);
	if ($user->picture != NULL){
		$CV->Image($user->picture, 10, 10);
	}
	$CV->Cell(0,6, $user->name . " " . $user->surname, 0, 1, 'R');
	$CV->SetFont('Arial', '', 12);
	$CV->Cell(0,6, "Birth date: " . $user->birth_date, 0, 1, 'R');
	if ($user->email != NULL){
		$CV->Cell(0,6, "E-mail: " . $user->email, 0, 1, 'R');
	}
	if ($user->phone != NULL){
		$CV->Cell(0,6, "Phone number: " . $user->phone, 0, 1, 'R');
	}
	if ($user->marital_status != NULL){
		$CV->Cell(0,6, "Marital status: " . $user->marital_status, 0, 1, 'R');
	}
	if ($user->education != NULL){
		$CV->Cell(0,6, "Education: " . $user->education, 0, 1, 'R');
	}
// print work
	if ($work != NULL) {
		$CV->SetFont('Arial', 'UB', 12);
		$CV->Cell(0,7, "PAST EMPLOYMENTS", 0, 1, 'C');
		foreach($work as $entry) {
			$CV->SetFont('Arial', 'UB', 10);
			$CV->SetTextColor( 0 );
			$CV->Write(5, "$entry->job at $entry->company");
			$CV->SetFont('Arial', '', 10);
			$CV->SetTextColor( 100 );
			$CV->Write(5, " from $entry->start_date to $entry->end_date");
			$CV->Ln();
			if ($entry->description != NULL) {
				$CV->SetFont('Arial', 'I', 10);
				$CV->SetTextColor( 0 );
				$CV->Cell(0,5, "$entry->description", 0, 1);
			}
		}
		$CV->Ln();
	}
// print courses
	if ($course != NULL) {
		$CV->SetFont('Arial', 'UB', 12);
		$CV->SetTextColor( 0 );
		$CV->Cell(0,7, "FINISHED COURSES", 0, 1, 'C');
		foreach($course as $entry) {
			$CV->SetFont('Arial', 'UB', 10);
			$CV->SetTextColor( 0 );
			$CV->Write(5, "$entry->name");
			if ($entry->end_date != NULL) {
				$CV->SetFont('Arial', 'U', 10);
				$CV->SetTextColor( 100 );
				$CV->Write(5, " $entry->end_date");
			}
			$CV->Ln();
			if ($entry->description != NULL) {
				$CV->SetFont('Arial', 'I', 10);
				$CV->SetTextColor( 0 );
				$CV->Cell(0,5, "$entry->description", 0, 1);
			}
		}
		$CV->Ln();
	}
// print skills
	if ($skill != NULL) {
		$CV->SetFont('Arial', 'UB', 12);
		$CV->SetTextColor( 0 );
		$CV->Cell(0,7, "ACQUIRED SKILLS", 0, 1, 'C');
		foreach($skill as $entry) {
			$CV->SetFont('Arial', 'UB', 10);
			$CV->SetTextColor( 0 );
			$CV->Write(5, "$entry->name");
			$CV->Ln();
			if ($entry->description != NULL) {
				$CV->SetFont('Arial', 'I', 10);
				$CV->SetTextColor( 0 );
				$CV->Cell(0,5, "$entry->description", 0, 1);
			}
		}
		$CV->Ln();
	}
// print demos
	if ($demo != NULL) {
		$CV->SetFont('Arial', 'UB', 12);
		$CV->SetTextColor( 0 );
		$CV->Cell(0,7, "PROJECT DEMOS", 0, 1, 'C');
		foreach($demo as $entry) {
			$CV->SetFont('Arial', 'UB', 10);
			$CV->SetTextColor( 0 );
			$CV->Write(5, "$entry->name");
			$CV->SetFont('Arial', 'U', 10);
			$CV->SetTextColor( 100, 100, 255 );
			$CV->Write(5, " link to file", "$entry->file");
			$CV->Ln();
			if ($entry->description != NULL) {
				$CV->SetFont('Arial', 'I', 10);
				$CV->SetTextColor( 0 );
				$CV->Cell(0,5, "$entry->description", 0, 1);
			}
		}
		$CV->Ln();
	}
	$CV->Output($upload_dir['path']  . '/cv_' . $filename, 'F');
	header('Location: '.$upload_dir['url']  . '/cv_' .$filename);
	exit();
?>