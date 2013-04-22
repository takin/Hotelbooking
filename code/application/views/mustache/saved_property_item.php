<div id="prop_tab_box_{{propertyNumber}}" class="hostel_list search_list" rel="{{propertyNumber}}">
	<div class="box_content box_round ui-tabs" id="prop_box_{{propertyNumber}}">
		<div class="city_hostel group" id="city_info_{{propertyNumber}}">
			<div class="info">
				<div class="left info_pic">
					<div class="picture_number" id="{{propertyNumber}}">0</div>
						<a href="{{property_page_url}}" style="position:relative;">
						{{#PropertyImages}}
							<img alt="" src="{{#PropertyImage}}{{imageListURL}}{{/PropertyImage}}" />
						{{/PropertyImages}}
						</a>

						<span class="info_type">{{propertyType}}</span>
					</div>

					<div class="info_indent">
						<h2>
							<a href="{{property_page_url}}" style="vertical-align: middle">
								{{propertyName}}
								<span style="color: #3087C9; font-size:0.7em; vertical-align: middle">
									({{propertyTypeTranslate}})
								</span>
							</a>
						</h2>

						<p class="address">{{city}} - {{country}}</p>
					</div>
				</div>
			</div>

			<div class="city_hostel ui-tabs-hide city_map_tab" id="city_map_{{propertyNumber}}">
				<h3><a class="city_link_hostel" href="{{property_page_url}}">{{propertyName}}</a>, {{address1}}</h3>

				<a href="{{property_page_url}}" class="reserve button-green hoverit" title="<?php echo _("Plus sur ce logement");?>"><?php echo _("Réserver");?></a>
			</div>
		</div>
	</div>
</div>
