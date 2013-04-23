<div id="prop_tab_box_{{property_number}}" class="hostel_list search_list" rel="{{property_number}}">
	<div class="box_content box_round ui-tabs" id="prop_box_{{property_number}}">
		<div class="city_hostel group" id="city_info_{{property_number}}">
			<div>
				<div class="left">
					<p><strong>{{city}} - {{country}}</strong></p>
					<p><?php echo _('Arrival:'); ?> <strong>{{arrival_date}}</strong> <?php echo _('Number of nights:'); ?> <strong>{{nights}}</strong></p>
					<a href="{{property_page_url}}">{{name}}</a>
					<strong>{{notes}}</strong>

					<a href=""><?php echo _('Edit'); ?></a> or <a href=""><?php echo _('Remove'); ?></a>
				</div>
			</div>

			<a href="{{property_page_url}}" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
		</div>
	</div>
</div>
