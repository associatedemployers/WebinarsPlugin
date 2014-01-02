<?php
	/*get webinar information from ajax request*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); //wordpress vars for db connection
	global $wpdb; //globalize wordpress db var
	
	/* ajax vars */
	$urlkey = $_GET['urlkey'];
	/* end ajax vars */
	
	if(!$urlkey) { //check for blank ajax vars
		die("Invalid Request.");
	}
	
	//sql strings
	$table_name = $wpdb->prefix . "webinars";
	$sql = $wpdb->prepare("SELECT * FROM " . $table_name . " WHERE urlkey='$urlkey'");
	
	//run the sql queries
	$row = $wpdb->get_row($sql);
	$error = $wpdb->print_error();
	
	//set the variables from sql return
	$title = $row->title;
	$description = $row->description;
	$videourl = $row->url;
	
	//get the results into an array for json encoding
	$result = array(
		'key_retrieved' => $urlkey,
		'title' => $title,
		'description' => $description,
		'video' => $videourl
	);
	
	echo json_encode($result);
?>