<?php
class ItemSecondModify2 {

function item_populate($itm, $item) {
?>
<script>
jQuery(document).ready(function() {
	jQuery("#post").validate({
		errorPlacement: function(label, element){ 
			real_label = label.clone();
			real_label.insertAfter(element);
			element.mouseover(function(){ jQuery(this).next('label.error').fadeOut(200); });
		}
	});
});
</script>


<form id="post" method="post" action="<?php echo $item->get_admin_url(true); ?>" enctype="multipart/form-data" autocomplete="off">
<div class="metabox-holder has-right-sidebar" id="poststuff">
	<input id="id" name="id" type="hidden" value="<?php echo $itm->id; ?>" />

	<div id="side-info-column" class="inner-sidebar"><div id="side-sortables" class="meta-box-sortables ui-sortable">
		<div class="postbox " id="submitdiv">
			<div class="handlediv" title="Click to toggle"><br /></div><h3 class="hndle"><span>&nbsp;</span></h3>
			<div class="inside"><div id="major-publishing-actions">
				<input class='button-primary' type='submit' name='Save' value='Save' id='submitbutton' />
				<a class='button-secondary' href='<?php echo $item->get_admin_url(null); ?>' title='Cancel'>Cancel</a>
			</div></div>
		</div>
	</div></div>

	<div class="has-sidebar" id="post-body"><div class="has-sidebar-content" id="post-body-content"><div class="meta-box-sortables ui-sortable" id="normal-sortables">
		<div class="postbox">
			<div title="Click to toggle" class="handlediv"><br /></div><h3 class="hndle"><span>Details <?php if($item) { echo 'for order '.$itm->transaction_id.' webinar '.$itm->title;}?></span></h3>
			<div class="inside">
				<p><input type="text" size="60" id="first" name="first" value="<?php if($item) { echo $itm->first; } ?>" /><br/><label for="first"><b>First Name</b></label></p>
				<p><input type="text" size="60" id="last" name="last" value="<?php if($item) { echo $itm->last; } ?>" /><br/><label for="last"><b>Last Name</b></label></p>
				<p><input type="email" size="60" id="email" name="email" value="<?php if($item) { echo $itm->email; } ?>" /><br/><label for="email"><b>Email</b></label></p>
				<p>$<input type="text" size="59" id="total" name="total" value="<?php if($item) { echo $itm->total; } ?>" /><br/><label for="total"><b>Order Total</b></label></p>
				<?php if($item) { ?><p><b>Webinar Access Code:</b> <?php echo $itm->webinar_code; ?></p><?php } ?>
				<?php if($item) { ?><p><b>Expiration Date:</b> <?php if($itm->expiration_date != NULL) {echo $itm->expiration_date;} else {echo 'never';} ?></p><?php } ?>
			</div>
		</div>
	</div>


	</div></div>
</div>
</form>
<?php
}

}
?>