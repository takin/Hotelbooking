<form action="/cmain/save_favorite_property" method="post" id="save_fav">
	<input type="hidden" name="id" value="{{favoriteId}}" />
	<input type="hidden" name="propertyNumber" value="{{propertyNumber}}" />

	<input type="hidden" name="nights" value="{{nights}}" />
	<input type="hidden" name="date" value="{{date}}" id="date" />

	<div class="property_details">
		<div class="image">
			<img src="{{imageURL}}" />
		</div>

		<div class="property_name">{{propertyName}}</div>
		<div class="location">{{city}} - {{country}}</div>
	</div>

	<div class="schedule_details">
		<div class="date">
			<span><?php echo _('Arrival:'); ?></span>
			<input type="text" name="date_show" class="date" value="{{date}}" />
			<span class="num">{{date}}</span>
		</div>

		<div class="nights">
			<span><?php echo _('Number of nights:'); ?></span>
			<span class="num">{{nights}}</span>
			<a href="">[<?php echo _('Change Dates'); ?>]</a>
		</div>
	</div>

	<div class="fav_details">
		<label><?php echo _('Your private notes (optional)'); ?></label>

		<textarea cols="30" name="notes" rows="5" class="notes" onkeyup="return SaveProperty.countRemainingChars(this, '#save_fav .characters .num');">{{notes}}</textarea>

		<div class="characters">
			<span class="num">{{characters}}</span>
			<span class="singular"><?php echo _('character left'); ?></span>
			<span class="plural"><?php echo _('characters left'); ?></span>
		</div>
	</div>

	<div class="actions">
		{{#isUpdate}}
			<input type="submit" name="submit" value="<?php echo _('Update Note'); ?>" />
			<a href="#"><?php echo _('Remove from my favorites'); ?></a>
		{{/isUpdate}}

		{{#isNew}}
			<input type="submit" name="submit" value="<?php echo _('Save Note'); ?>" />
		{{/isNew}}

		<a href="#" onclick="$('#save_property_dialog').hide(); return false;"><?php echo _('Cancel'); ?></a>
	</div>
</form>
