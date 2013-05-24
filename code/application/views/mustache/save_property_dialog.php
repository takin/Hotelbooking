<?php 
$nights = range(1, 30);
$nightsOptions = '';
foreach ($nights as $night) {
	$nightsOptions .= '<option value="' . $night . '">' . $night . '</option>';
}
?>

<form action="<?php echo site_url(); ?>cmain/ajax_save_favorite_property" method="post" id="save_fav" onsubmit="SaveProperty.handleSaveForm(this); return false;">
	<input type="hidden" name="id" value="{{id}}" />
	<input type="hidden" name="propertyNumber" value="{{propertyNumber}}" />

	<input type="hidden" name="date" value="{{dateVal}}" id="date" />

	<div class="property_details">
		<div class="image">
			<img src="{{imageURL}}" style="width: 100px; height: 100px" />
		</div>

		<div class="property_name">{{propertyName}}</div>
		<div class="location">{{city}} - {{country}}</div>
	</div>

	<div class="schedule_details">
		<div class="box_content box_round group side_search">
			<div class="content_block" style="margin-bottom:0px;">
				<div class="group">
					<div class="left">
						<label for="search-date"><?php echo _('Arrivée le:');?></label>
						<input type="text" id="date_show" name="date_show" class="date_show" value="{{date}}" readonly="readonly" />
					</div>
					<div class="left">
						<?php
						$hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
						select_nights(_('Nuits:'), "nights", "nights", 2, $hb_api_used);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="fav_details">
		<label><?php echo _('Your private notes (optional)'); ?></label>

		<textarea cols="30" name="notes" rows="5" class="notes" onkeyup="return SaveProperty.countRemainingChars(this, '#save_fav .characters .num');">{{notes}}</textarea>

		<div class="characters">
			<span class="singular"><?php echo _('Characters left:'); ?></span>
			<span class="plural"><?php echo _('Characters left:'); ?></span>
			<span class="num">{{characters}}</span>
		</div>
	</div>

	<br />
	<div class="actions">
		{{#isUpdate}}
			<input type="submit" name="submit" value="<?php echo _('Save Note'); ?>" />
			<a href="#" class="remove" onclick="SavedProperty.remove({{id}}, this); return false;"><?php echo _('Remove from my favorites'); ?></a>
		{{/isUpdate}}

		{{#isNew}}
			<input type="submit" name="submit" value="<?php echo _('Save Note'); ?>" />
		{{/isNew}}

		<a href="#" onclick="$('#save_property_dialog').hide(); return false;"><?php echo _('Cancel'); ?></a>
	</div>
</form>
