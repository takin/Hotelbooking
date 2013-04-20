<div class="save_property_dialog">
	<div class="title"><?php echo _('Save this property as favorite'); ?></div>
	<div>close</div>

	<form action="/cmain/save_favorite_property" method="post" id="save_fav">
		<input type="hidden" name="id" value="{{favoriteId}}" />
		<input type="hidden" name="propertyNumber" value="{{propertyNumber}}" />

		<div class="property_details">
			<div class="image">
				<img src="{{imageURL}}" />
			</div>

			<div class="property_name">{{propertyName}}</div>
			<div class="location">{{location}}</div>
		</div>

		<div class="schedule_details">
			<div class="date">
				<span><?php _('Arrival:'); ?></span>
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

			<textarea cols="30" name="notes" rows="5">{{notes}}</textarea>

			<div class="characters">
				<span class="num">{{characters}}</span>
				<span class="singular"><?php echo _('character left'); ?></span>
				<span class="plural"><?php echo _('characters left'); ?></span>
			</div>
		</div>

		<div class="actions">
			<input type="submit" name="submit" value="<?php echo _('Update Note'); ?>" />
			<a href=""><?php echo _('Remove from my favorites'); ?></a>
			<a href=""><?php echo _('Cancel'); ?></a>
		</div>
	</form>
</div>
