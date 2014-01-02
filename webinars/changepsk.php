<?php
	/*get webinar information from ajax request*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //wordpress vars for db connection
	global $wpdb; //globalize wordpress db var
	
	/* ajax vars */
	$currentpsk = $_POST['psk_current'];
	$newpsk = $_POST['psk_new'];
	$transaction = $_POST['transaction_id'];
	$email = $_POST['email'];
	/* end ajax vars */
	
	if(!$newpsk || !$currentpsk || !$email || !$transaction) { //check for blank ajax vars
		die("Invalid Request.");
	}
	
	//sql strings
	$table_name = $wpdb->prefix . "webinar_code";
	$sql = $wpdb->prepare("UPDATE " . $table_name . " SET webinarpass='$newpsk' WHERE webinarpass='$currentpsk' and email='$email' and transaction_id='$transaction'");
	
	//run the sql query
	$wpdb->query($sql);
	$error = $wpdb->print_error();
	
	//get the results into an array for json encoding
	$result = array(
		'success' => true,
	);
	
	echo json_encode($result);
?>