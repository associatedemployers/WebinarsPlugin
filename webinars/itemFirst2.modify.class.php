<?php
class ItemFirstModify2 {

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


<form id="post" method="post" action="<?php echo $item->get_admin_url(null); ?>" enctype="multipart/form-data" autocomplete="off">
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

		<div class="postbox" id="otherdiv">
			<div class="handlediv" title="Click to toggle"><br /></div><h3 class="hndle"><span>Pricing Options</span></h3>
			<div class="inside">
				<p><input type="text" size="20" id="duration" name="duration" value="<?php if($item) {if($itm->duration) {echo $itm->duration;} else {echo '90';}} ?>" /><br/><label for="duration"><b>Purchase Duration</b></label></p>
				<hr/>
				<p>
					<div style="float: left;">$<input type="text" size="20" id="price" name="price" value="<?php if($item) { echo $itm->price; } else {echo '70';} ?>" /><br/><label for="price"><b>Price</b></label><br />
					$<input type="text" size="20" id="memberprice" name="memberprice" value="<?php if($item) { echo $itm->member_price; } else {echo '45';} ?>" /><br/><label for="memberprice"><b>AE Member Price</b></label>
					</div>
				</p><div class="clear"></div>
				<br/>
			</div>
		</div>
	</div></div>

	<div class="has-sidebar" id="post-body"><div class="has-sidebar-content" id="post-body-content"><div class="meta-box-sortables ui-sortable" id="normal-sortables">
		<div class="postbox">
			<div title="Click to toggle" class="handlediv"><br /></div><h3 class="hndle"><span>Details</span></h3>
			<div class="inside">
				<p><input type="text" size="60" id="title" class="required" name="title" value="<?php if($item) { echo $itm->title; } ?>" /><br/><label for="title"><b>Webinar Title</b></label></p>
				<p><textarea rows="8" cols="60" id="description" name="description"><?php if($item) { echo $itm->description; } ?></textarea><br/><label for="description"><b>Description</b></label></p>
				<p><?php if($item && $itm->url) {  echo $itm->url; } else { ?><div style="float:left;"><h2>Local Video</h2><input type="file" size="60" id="url" name="url" value="" /><br/><label for="url"><b>Webinar Video File</b></label></div><div style="float:left; display:table-cell; vertical-align:middle; margin-right:45px;"><h2>OR</h2></div><div style="float:left;"><h2>Cloud Video</h2><label for="cloud">Use Amazon S3 or other Cloud Service URL? </label><input type="checkbox" id="cloud" name="cloud" value="enabled" /><br /><label for="cloudurl">Copy/paste cloud url (http://...video.mp4) </label><input type="text" id="cloudurl" name="cloudurl" /></div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><?php } ?></p>
				<!--<?php if($item && $itm->url) { ?> <video class="sublime" width="640" height="360" title="<?php echo $itm->title; ?>" data-uid="a240e92d" data-autoresize="fit" preload="none">
						  <source src="<?php echo $itm->url; ?>" />
				</video><?php }?>-->
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