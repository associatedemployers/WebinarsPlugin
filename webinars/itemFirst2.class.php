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
function save_item($values) {
	global $wpdb;
	global $webgrain2;
	$id =									$values['id'];
	$title =								$values['title'];
	$description =						$values['description'];
	$url =								$values['url'];
	$duration =							$values['duration'];
	$price =								$values['price'];
	$unlimited =				$values['unlimited'];

	if($unlimited == 'on') { $unlimited = 1; } else { $unlimited = 0; }

	//Setup data for database insert
	if($id) {
		$sql = "UPDATE " . $webgrain2->first_menu_table . "
		SET title = '$title', description = '$description', url = '$url', duration = '$duration', price = '$price', unlimited = $unlimited
		WHERE id = $id";
		$wpdb->query($wpdb->prepare($sql, 0));
	} else {
		$sql = "INSERT INTO " . $webgrain2->first_menu_table . " 
		(title, description, url, duration, price, unlimited) VALUES 
		('$title', '$description', '$url', '$duration', '$price', $unlimited)";
		$wpdb->query($wpdb->prepare($sql, 0));
		$id = $wpdb->insert_id;
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