<?php
	/*add order to webinar orders via local ajax request*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //wordpress vars for db connection
	global $wpdb; //globalize wordpress db var
	
	/* ajax vars */
	$transaction_id = $_POST['transaction_id'];
	$items = $_POST['items'];
	$expiration = $_POST['expiration'];
	$email = $_POST['email'];
	$total = $_POST['total'];
	$orderinfo = $_POST['order_info'];
	$company = $_POST['company'];
	/* end ajax vars */
	
	$webinarpass = generateKey(); //generated var
	
	if(!$transaction_id || !$items || !$expiration || !$email || !$total || !$orderinfo) { //check for blank ajax vars
		die("Invalid Request.");
	} else if(!$webinarpass) { //check for blank passkey
		die("The server encountered a internal error when generating a passkey");
	}
	
	//sql strings
	$table_name = $wpdb->prefix . "webinar_code";
	$sql = $wpdb->prepare("INSERT INTO " . $table_name . "(email, total, accesstourlkey, expiration, orderinfo, webinarpass, transaction_id, company) VALUES ('$email', '$total', '$items', '$expiration', '$orderinfo', '$webinarpass', '$transaction_id', '$company')");
	
	//run the sql queries
	$insertOrder = $wpdb->query($sql);
	$error = $wpdb->print_error();
	$id = $wpdb->insert_id;
	
	//get the results into an array for json encoding
	$result = array(
		'email' => $email,
		'passkey' => $webinarpass,
		'expiration' => $expiration
	);
	
	echo json_encode($result);
	
	function generateKey() {
    	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    	$pass = array(); //remember to declare $pass as an array
    	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    	for ($i = 0; $i < 10; $i++) {
        	$n = rand(0, $alphaLength);
        	$pass[] = $alphabet[$n];
    	}
    	return implode($pass); //turn the array into a string
	}
?>