{{#property}}
<div class="mapbubble">
<a href="{{property_page_url}}">
  {{#PropertyImages}}<img class="alignleft" alt="{{propertyName}}" src="{{#PropertyImage}}{{imageURL}}{{/PropertyImage}}" />{{/PropertyImages}}
</a>
<h2>
<a href="{{property_page_url}}">{{propertyName}}</a>
</h2>
<p class="price">
<?php echo _('à partir de'); ?><span> {{display_price_formatted}}</span> {{display_currency}}
{{#overall_rating}}
 - <?php echo _("évaluation moyenne"); ?> {{overall_rating}}%
{{/overall_rating}}
</p>
<a href="{{property_page_url}}" class="more-info"><?php echo _("Plus d'information"); ?> &raquo;</a>
<div class="clear"></div>
</div>
{{/property}}