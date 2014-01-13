<?php
class ItemSecond2 {
/***************************************************/
/*********************GET ITEM*******************/
/***************************************************/
function get_items() {
	//MODIFY: ORDER BY column name
	global $wpdb;
	global $webgrain2;
	$itemArray = array();
	$sql = "SELECT * FROM " . $webgrain2->second_menu_table . " ORDER BY transaction_id ASC";
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
	$sql = "SELECT * FROM " . $webgrain2->second_menu_table . " WHERE id = $id";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) { return $r; } }
}


/****************************************************/
/*********************GET ITEMS******************/
/****************************************************/
function get_admin_page() { return get_item_second_admin_page2(); }
function get_admin_url($submit) { return get_item_second_admin_url2($submit); }
function get_new_button_title() { global $webgrain2; return $webgrain2->second_new_button_title; }
function get_page_menu_title() { global $webgrain2; return $webgrain2->second_menu_title; }



/**************************************************************************/
/*********************GET CATEGORY CHECKBOXES******************/
/**************************************************************************/
function getCategoriesCheckboxes($itm){
	global $wpdb;
	global $webgrain2;

	$checked_cateogries = '';
	if(!empty($itm->category)) {
		$checked_cateogries = explode(',', $itm->category);
	}

	//Script to automatically check the parent category and force the parent category if sub categories present
	$html = '<script>jQuery(document).ready(function() { 
					jQuery(".cat_checkbox_sub").bind("click", function() {
						var parent_cat = jQuery(this).parents(".top_cat").find(".cat_checkbox_top");
						jQuery(parent_cat).prop("checked", true);
						jQuery(this).parent().toggleClass("chkstyle");
						if(jQuery(parent_cat).prop("checked")) {
							jQuery(parent_cat).parent().addClass("chkstyle");
						}
					});

					jQuery(".cat_checkbox_top").bind("click", function() {
						var selected = jQuery(this).parents(".top_cat").find(".cat_checkbox_sub:checked");
						if(selected.length == 0 ) {
							if(!jQuery(this).prop("checked")) {
								jQuery(this).parent().removeClass("chkstyle");
								jQuery(this).prop("checked", false); 
							} else { 
								jQuery(this).prop("checked", true);
								jQuery(this).parent().addClass("chkstyle");
							}
						} else { 
							jQuery(this).prop("checked", true);
							jQuery(this).parent().addClass("chkstyle");
						}
					});
				});	</script>';

	$sql = "SELECT * FROM " . $webgrain2->product_category_table . " WHERE parent_id = 0";
	$result = $wpdb->get_results($wpdb->prepare($sql, 0));
	if(!empty($result)) { foreach($result as $r) {
		$html .= '<div class="top_cat">';

		$checked = '';
		$chkstyle = '';
		if(is_array($checked_cateogries)) { if(in_array($r->id, $checked_cateogries)) { $checked = 'checked="checked"';  $chkstyle = 'class="chkstyle"'; } }
		$html .= '<label ' . $chkstyle . '><input type="checkbox" class="cat_checkbox_top" name="category[]" value="' . $r->id . '" ' . $checked . ' /> <b>' . $r->name . '</b></label>';

		$sql2 = "SELECT * FROM " . $webgrain2->product_category_table . " WHERE parent_id = " .  $r->id;
		$result2 = $wpdb->get_results($wpdb->prepare($sql2, 0));
		if(!empty($result2)) { foreach($result2 as $r2) {
			$checked2 = '';
			$chkstyle2 = '';
			if(is_array($checked_cateogries)) { if(in_array($r2->id, $checked_cateogries)) { $checked2 = 'checked="checked"'; $chkstyle2 = 'class="chkstyle"'; } }
			$html .= '<div class="sub_cat"><label ' . $chkstyle2 . '><input type="checkbox" class="cat_checkbox_sub" name="category[]" value="' . $r2->id . '" ' . $checked2 . ' /> <b>' . $r2->name . '</b></label></div>';
		} }

		$html .= '</div>';
	} }

	return $html;
}



/*********************************************************/
/*********************GET ITEM BY ID******************/
/*********************************************************/
function render_list_table(){
	$list_table = new itemSecond_table2();
	$list_table->prepare_items();
	return $list_table;
}

/****************************************************/
/*********************SAVE ITEM******************/
/****************************************************/
function save_item($values) {
	global $wpdb;
	global $webgrain2;
	
	$id = $values['id'];
	$email = $values['email'];
	$total = $values['total'];
	$accesstourlkey = $values['accesstourlkey'];
	$accesstourlkey = str_replace(" ", "",$accesstourlkey); //strip whitespace
	$expiration = $values['expiration'];
	$orderinfo = $values['orderinfo'];
	$webinarpass = $values['webinarpass'];
	$transaction_id = "Manual Order";
	$company = $values['company'];

	//Setup data for database insert
	if($id) {
		$sql = "UPDATE " . $webgrain2->second_menu_table . " SET 
		email = '$email', total = '$total', accesstourlkey = '$accesstourlkey', expiration = '$expiration', orderinfo = '$orderinfo', webinarpass = '$webinarpass', company = '$company'
		WHERE id = $id";
		$wpdb->query($wpdb->prepare($sql, 0));
	} else {
		$sql = "INSERT INTO " . $webgrain2->second_menu_table . " 
		(email, total, accesstourlkey, expiration, orderinfo, webinarpass, transaction_id, company) VALUES 
		('$email', '$total', '$accesstourlkey', '$expiration', '$orderinfo', '$webinarpass', '$transaction_id', '$company')";
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

	$sql = "DELETE FROM " . $webgrain2->second_menu_table . " WHERE id = '$id'";
	if($id) { $wpdb->query($wpdb->prepare($sql)); } //Removes the item
}


}
?>