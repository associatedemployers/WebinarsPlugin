<?php
class ItemSecondList2 {

function ItemSecondList2() {
	$item = new ItemSecond2(); //Basic Functions
	$item_functions = new ItemSecondModify2(); //Form Display for editing item

	if(isset($_POST['Save'])) { $item->save_item($_POST); } //Saves item
	if(isset($_POST['Delete'])) { //Deletes item
		$itm_id = $_POST['id'];
		$itm = $item->get_item_by_id($itm_id);
		$item->delete_item($itm_id, $itm);
	}

	if(isset($_GET['new'])) { $new_item = $_GET['new']; } else { $new_item = ''; }
	if(isset($_GET['edit'])) { $edit_item = $_GET['edit']; } else { $edit_item = ''; }
	if(isset($_GET['delete'])) { $delete_item = $_GET['delete']; } else { $delete_item = ''; }
	?>

	<div class="wrap wg">
		<h2><?php echo $item->get_page_menu_title(); ?></h2>
		<?php
		if($new_item) { $item_functions->item_populate(null, $item); }
		if($edit_item) { $item_functions->item_populate($item->get_item_by_id($edit_item), $item); }
		if($delete_item) { $this->delete_item_layout($item->get_item_by_id($delete_item), $item); }

		if(empty($new_item) && empty($edit_item) && empty($delete_item)) {
			echo '<form method="post"><input type="hidden" name="page" value="' . $item->get_admin_page() . '" />';
			$list_table = $item->render_list_table();
			$list_table->search_box('search', 'search_id');
			echo '</form>';
			echo '<div id="button_bar"><div>
					<a class="button-primary" href="' . $item->get_admin_url(null) . '&new=1" title="' . $item->get_new_button_title() . '">' . $item->get_new_button_title() . '</a>
					</div></div><div style="clear: both;"></div>';
			$list_table->display();
		} ?>
	</div><?php
}


/******************************************************/
/*********************DELETE ITEM******************/
/******************************************************/
function delete_item_layout($itm, $item) {
	//MODIFY: Change $item_title to the name of the item being deleted
	$item_title = $itm->transaction_id;
	?>

	<form method="POST" action="<?php echo $item->get_admin_url(true); ?>">
		<input id="action" name="action" type="hidden" value="Delete" />
		<input id="id" name="id" type="hidden" value="<?php echo $itm->id; ?>" />

		<h3>Deleting: <em><?php echo $item_title; ?></em></h3>
		<p>Are you sure you want to <b>DELETE</b> <?php echo $item_title; ?>?</p>

		<input class="button-primary" type="submit" name="Delete" value="Delete" id="submitbutton"/>
		<a class="button-secondary" href="<?php echo $item->get_admin_url(true); ?>" title="Cancel">Cancel</a>
	</form>

	<?php
}

}
?>