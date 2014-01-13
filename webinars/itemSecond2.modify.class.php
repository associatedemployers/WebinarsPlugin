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
			<div title="Click to toggle" class="handlediv"><br /></div><h3 class="hndle"><span>Details <?php if($item) { echo 'for order '.$itm->transaction_id;}?></span></h3>
			<div class="inside">
				<p><label for="company"><b>Company</b></label><br /><input type="text" id="company" name="company" value="<?php if($item) { echo $itm->company; } ?>" /></p>
				<p><label for="orderinfo"><b>Order Information (Name, Address, City, State, Zip)</b></label><br /><textarea id="orderinfo" name="orderinfo" cols="50" rows="8"><?php if($item) { echo $itm->orderinfo; } ?></textarea></p>
				<p><label for="email"><b>Email Address</b></label><br /><input type="text" id="email" name="email" value="<?php if($item) { echo $itm->email; } ?>" /></p>
				<p><label for="expiration"><b>Expiration Date (Must be in this format: YYYY/MM/DD)</b></label><br /><input type="text" id="expiration" name="expiration" value="<?php if($item) { echo $itm->expiration; } ?>" /></p>
				<p><label for="accesstourlkey"><b>Each Webinar URL The Order Has Access To **SEPARATED BY COMMAS**</b><br />(ex. "52b1bff796c4d443169121,52b47add92b03104601429" Will give access to the two URLs - NO QUOTES)</label><br /><textarea id="accesstourlkey" name="accesstourlkey" cols="20" rows="4"><?php if($item) { echo $itm->accesstourlkey; } ?></textarea></p>
				<p><label for="webinarpass"><b>Passkey for Webinar</b></label><br /><input type="text" id="webinarpass" name="webinarpass" value="<?php if($item) { echo $itm->webinarpass; } ?>" /></p>
				<p><label for="total"><b>Order Amount</b></label><br />$<input type="text" id="total" name="total" value="<?php if($item) { echo $itm->total; } ?>" /></p>
			</div>
		</div>
	</div></div></div>
</div>
</form>
<?php
}

}
?>