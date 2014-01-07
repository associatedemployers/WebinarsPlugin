<?php
class ItemFirst2 {
/***************************************************/
/*********************GET ITEM*******************/
/***************************************************/
function get_items() {
	//MODIFY: ORDER BY column name
	global $wpdb;
	global $webgrain2;
	$itemArray = array();
	$sql = "SELECT * FROM " . $webgrain2->first_menu_table . " ORDER BY title ASC";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { $itemArray[$r->id] = $r; } }
	return $itemArray;
}


/*********************************************************/
/*********************GET ITEM BY ID******************/
/*********************************************************/
function get_item_by_id($id) {
	global $wpdb;
	global $webgrain2;
	$sql = "SELECT * FROM " . $webgrain2->first_menu_table . " WHERE id = $id";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { return $r; } }
}

/****************************************************/
/*********************GET ITEMS******************/
/****************************************************/
function get_admin_page() { return get_item_first_admin_page2(); }
function get_admin_url($submit) { return get_item_first_admin_url2($submit); }
function get_new_button_title() { global $webgrain2; return $webgrain2->first_new_button_title; }
function get_page_menu_title() { global $webgrain2; return $webgrain2->first_menu_title; }



/*********************************************************/
/*********************GET ITEM BY ID******************/
/*********************************************************/
function render_list_table(){
	$list_table = new itemFirst_table2();
	$list_table->prepare_items();
	return $list_table;
}

/****************************************************/
/*********************SAVE ITEM******************/
/****************************************************/
function save_item($values, $file) {
	/**
	 * ExtractThumb, extracts a thumbnail from a video
	 *
	 * This function loads a video and extracts an image from a frame 4 
	 * seconds into the clip
	 * @param $in string the input path to the video being processed
	 * @param $out string the path where the output image is saved
	 */
	function ExtractThumb($in, $out) {
		$thumb_stdout;
		$errors;
		$retval = 0;
		// Delete the file if it already exists
		if (file_exists($out)) { unlink($out); }
		// Use ffmpeg to generate a thumbnail from the movie
		$cmd = "ffmpeg -i $in -ss 00:00:01 -f image2 -vframes 1 -s 300x200 $out";
		exec($cmd, $thumb_stdout, $retval);
		// Queue up the error for processing
		if ($retval != 0) { $errors[] = "FFMPEG thumbnail generation failed"; }
		if (!empty($thumb_stdout)) {
			foreach ($thumb_stdout as $line) {
				echo $line . "\n";
			}
		}
		if (!empty($errors)) {
			foreach ($errors as $error) {
				echo $error . "\n";
			}
			echo $out . "\n";
			echo $in . "\n";
		} else {
			echo "Thumbnail Generated Successfully <br />";	
		}
	}
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	global $wpdb;
	global $webgrain2;
	
	$cloud = $values['cloud'];
	$cloudurl = $values['cloudurl'];
	
	if($cloud == "enabled" && $cloudurl) {
		$url = $cloudurl;
	} else {
		$tempfile = $file['url'];
		// required for wp_handle_upload() to upload the file
		$upload_overrides = array( 'test_form' => FALSE );
		$attachment = wp_handle_upload($tempfile, $upload_overrides);
		$url = $attachment['url'];
	}
	
	$urlkey =       uniqid('',TRUE);
	$urlkey =       str_replace(".","",$urlkey); //unique id with more_entropy enabled -- removes "." from more entropy
	$id =			$values['id'];
	$title =		$values['title'];
	$description =	$values['description'];
	$duration =		$values['duration'];
	$price =		$values['price'];
	$memberprice =  $values['memberprice'];
	
	//ffmpeg vars
	$upload_dir = wp_upload_dir();
	$video = $url;
	$thumbnail = $upload_dir['basedir'] . "/webinar-thumbnails/" . $urlkey . ".png";
	
	//ffmpeg execution here
	ExtractThumb($video, $thumbnail);
	
	//Setup data for database insert
	if($id) {
		$sql = "UPDATE " . $webgrain2->first_menu_table . "
		SET title = '$title', description = '$description', url = '$url', duration = '$duration', price = '$price', member_price = $memberprice
		WHERE id = $id";
		$wpdb->query($wpdb->prepare($sql, 0));
		echo "Updated Webinar! <br />";	
	} else {
		$sql = "INSERT INTO " . $webgrain2->first_menu_table . " 
		(title, description, url, duration, price, urlkey, member_price) VALUES 
		('$title', '$description', '$url', '$duration', '$price', '$urlkey', $memberprice)";
		$wpdb->query($wpdb->prepare($sql, 0));
		$id = $wpdb->insert_id;
		echo "Created Webinar! <br />";	
	}
}


/****************************************************/
/*********************DELETE ITEM****************/
/****************************************************/
function delete_item($id, $brd) {
	global $wpdb;
	global $webgrain2;

	$sql = "DELETE FROM " . $webgrain2->first_menu_table . " WHERE id = '$id'";
	if($id) { $wpdb->query($wpdb->prepare($sql)); } //Removes the item
}


}
?>