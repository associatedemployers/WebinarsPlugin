<?php
/* login validation for webinars */
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //wordpress vars for db connection
	global $wpdb; //globalize wordpress db var
	
	/* ajax vars */
	$email = $_POST['email'];
	$passkey = $_POST['passkey'];
	/* end ajax vars */
	
	if(!$email || !$passkey) { //check for blank ajax vars
		die("Invalid Request.");
	}
	
	//sql strings
	$table_name = $wpdb->prefix . "webinar_code";
	$sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE webinarpass='$passkey' and email='$email'");
	
	//run the sql queries
	$sqlresult = $wpdb->get_row($sql); //gets a row from the login information
	$error = $wpdb->print_error();
	
	//get the results into an array for json encoding
	$result = array(
		'email' => $email,
		'passkey' => $passkey,
		'expiration' => $sqlresult->expiration, //row data
		'urlkey_list' => $sqlresult->accesstourlkey, //row data
		'order_info' => $sqlresult->orderinfo, //row data
		'transaction' => $sqlresult->transaction_id,
		'errors' => $error //collected errors
	);
	
	echo json_encode($result); //return the results
?>