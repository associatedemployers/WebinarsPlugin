<?php 
/* return boolean on user exist */
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //wordpress vars for db connection
	global $wpdb;
	$email = $_POST['email'];
	if(!$email) {
		die("Error: Invalid Request.");	
	}
	$table_name = "ae_company"; 
	$second_table_name = "ae_user"; 
	$sql = $wpdb->prepare("SELECT company FROM " . $table_name . " WHERE email='". $email ."' and active=0");
	$sql2 = $wpdb->prepare("SELECT first_name FROM " . $second_table_name . " WHERE email='". $email ."' and active=0");
	$success = TRUE;
	
	//run the sql queries
	$firstSearch = $wpdb->get_var($sql);
	$first_error = $wpdb->print_error();
	$secondSearch = $wpdb->get_var($sql2);
	$second_error = $wpdb->print_error();
	
	if($firstSearch) {
		$company = $firstSearch;
		$isMember = TRUE;
	} else if($secondSearch) {
		$company = $secondSearch;
		$isMember = TRUE;
	} else {
		$isMember = FALSE;
		$company = NULL;
		$success = FALSE;
	}
	
	//get the results into an array for json encoding
	$result = array(
		'success' => $success,
		'member' => $isMember,
		'company' => $company,
		'parsed_email' => $email
	);
	
	echo json_encode($result);
?>