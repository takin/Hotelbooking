<div id="prop_tab_box_{{id}}" class="hostel_list search_list" rel="{{property_number}}">
	<div class="box_content box_round ui-tabs" id="prop_box_{{property_number}}">
		<div class="city_hostel group" id="city_info_{{property_number}}">
			<div>
				<div class="left">
					<div class="info_pic" style="display: none">
						<img src="{{imageURL}}" style="width: 100px; height: 100px" />
					</div>

					<p><strong><span class="city">{{city}}</span> - <span class="country">{{country}}</span></strong></p>
					<p><?php echo _('Arrival:'); ?> <strong class="dateShow">{{arrival_date_show}}</strong> <?php echo _('Number of nights:'); ?> <strong class="nights">{{nights}}</strong></p>
					<span class="date" style="display: none">{{arrival_date}}</span>
					<a href="{{property_page_url}}" class="propertyName">{{name}}</a>
					<strong class="notes">{{notes}}</strong>

					<a href="#" onclick="SavedProperty.edit({{id}}, this); return false;"><?php echo _('Edit or remove'); ?></a>
				</div>
			</div>

			<a href="{{property_page_url}}" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
		</div>
	</div>
</div>
