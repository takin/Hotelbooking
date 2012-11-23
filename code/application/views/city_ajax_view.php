              <?php /*?><textarea><?php print_r($property_list);?></textarea><?php
              var propertiesdata = <?php echo $json; ?>;
              var propertiesdata = [
                          {"id": 1, "propertyName": "Hello world!"},
                          {"id": 2, "propertyName": "Table of Contents"},
                          {"id": 7, "propertyName": "Getting Started"},
                          {"id": 8, "propertyName": "Introduction"},
                          {"id": 9, "propertyName": "What is jOrder?"},
                          {"id": 10, "propertyName": "What can jOrder do?"},
                          {"id": 11, "propertyName": "A simple tutorial"},
                          {"id": 12, "propertyName": "What do I need?"}
                      ];
              */?>
              <script type="text/javascript" src="<?php echo base_url();?>js/markerclusterer_packed.js"></script>
              <script type="text/javascript">


              var propertiesdata = <?php echo $json; ?>;

                function initializeCityMap() {
                  var latlng = new google.maps.LatLng(0, 0);
                  var myOptions = {
                    zoom: 14,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                  };

                  map = new google.maps.Map(document.getElementById(map_div), myOptions);

                  var hostelMarker = Array();
              	  var infocontent = Array();
              	  specific_index = null;

                  InfoW.init();

              		var bounds = new google.maps.LatLngBounds();

              		var image = new google.maps.MarkerImage('<?php echo site_url('images/map-marker.png'); ?>',
              	      new google.maps.Size(28, 28),
              	      new google.maps.Point(0,0),
              	      new google.maps.Point(0, 29));

              	  var iconshadow = new google.maps.MarkerImage('<?php echo site_url('images/map-marker-shadow.png'); ?>',
              	          new google.maps.Size(43, 28),
              	          new google.maps.Point(0,0),
              	          new google.maps.Point(0, 28));

                	<?php
                	if(!empty($property_list) && (isset($property_list["property_count"]))&&($property_list["property_count"]>0))
                	{

                	  foreach($property_list as $prop_type)
                	  {
                      if(is_array($prop_type))
                      foreach($prop_type as $property)
                      {
                        $rating_text = "";

                        if(($this->api_used == HW_API) &&($property->Geo->Latitude!=0)&&($property->Geo->Longitude!=0))
                        {
                          if(!empty($property->overallHWRating))
                          {
                            $rating_text = _("évaluation moyenne"). " ".$property->overallHWRating."%";
                          }

                          $thumbnail_url = site_url('images/na_small.jpg');
                          $cur = currency_symbol($property->BedPrices->BedPrice->currency);

                          if (!empty($property->PropertyImages->PropertyImage->imageURL))
                          {
                            $thumbnail_url = $property->PropertyImages->PropertyImage->imageURL;
                          }

                          $property_url = $this->Db_links->build_property_page_link($property->propertyType,$property->propertyName,$property->propertyNumber[0],$this->site_lang);

                          if(isset($property->BedPrices->BedPrice->price))
                          {
                            if(!empty($rating_text)) $rating_text = " - ".$rating_text;
                            $infoHTML = "<div class=\"mapbubble\">";
                            $infoHTML.= "<a href=\"".$property_url."\">";
                            $infoHTML.= "<img class=\"alignleft\" src=\"".$thumbnail_url."\" alt=\"".addslashes($property->propertyName)."\" />";
                            $infoHTML.= "</a>";
                            $infoHTML.= "<h2>";
                            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property->propertyName)."</a>";
                            $infoHTML.= "</h2>";
                            $infoHTML.= "<p class=\"price\">". _('à partir de')."<span> ". $property->BedPrices->BedPrice->price."</span> ". $cur .$rating_text."</p>";
                            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
                            $infoHTML.= "<div class=\"clear\"></div>";
                            $infoHTML.= "</div>";
                    		  }
                    		  else
                      		{
                      		  if(!empty($rating_text)) $rating_text = "<p>".$rating_text."</p>";
                      		  $infoHTML = "<div class=\"mapbubble\">";
                      		  $infoHTML.= "<a href=\"".$property_url."\">";
                            $infoHTML.= "<img class=\"alignleft\" src=\"".$thumbnail_url."\" alt=\"".addslashes($property->propertyName)."\" />";
                            $infoHTML.= "</a>";
                      		  $infoHTML.= "<h2>";
                            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property->propertyName)."</a>";
                            $infoHTML.= "</h2>";
                      		  $infoHTML.= $rating_text;
                      		  $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
                      		  $infoHTML.= "<div class=\"clear\"></div>";
                      		  $infoHTML.= "</div>";
                          }
                          ?>
                          addPropertyToMap(infocontent,hostelMarker,<?php echo $property->Geo->Latitude; ?>, <?php echo $property->Geo->Longitude; ?>, bounds, image, iconshadow, '<?php echo addslashes($property->propertyName); ?>', <?php echo (string)$property->propertyNumber; ?>, '<?php echo $infoHTML; ?>');
                         <?php
                        } //if HW_API AND lat !=0 AND lng !=0

                        if(($this->api_used == HB_API) && isset($property["geo_latitude"]) && isset($property["geo_longitude"]) && ($property["geo_latitude"]!=0) && ($property["geo_longitude"]!=0))
                        {

                          $property_url = $this->Db_links->build_property_page_link($property["type"],$property["name"],$property["id"],$this->site_lang);

                          if(!empty($property["rating"]))
                          {
                            $rating_text = _("évaluation moyenne"). " ".$property["rating"]."%";
                          }

                          if(!empty($property["prices"]["customer"]["minprice"]))
                          {
                            $cur = currency_symbol($property["prices"]["customer"]["currency"]);
                            if(!empty($rating_text)) $rating_text = " - ".$rating_text;
                            $infoHTML = "<div class=\"mapbubble\">";
                            $infoHTML.= "<a href=\"".$property_url."\">";
                            $infoHTML.= "<img class=\"alignleft\" src=\"".$property["image_thumbnail"]."\" alt=\"".addslashes($property["name"])."\" />";
                            $infoHTML.= "</a>";
                            $infoHTML.= "<h2>";
                            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property["name"])."</a>";
                            $infoHTML.= "</h2>";
                            $infoHTML.= "<p class=\"price\">". _('à partir de')."<span> ". $property["prices"]["customer"]["minprice"]."</span> ". $cur .$rating_text."</p>";
                            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
                            $infoHTML.= "<div class=\"clear\"></div>";
                            $infoHTML.= "</div>";
                          }
                          else
                          {
                            if(!empty($rating_text)) $rating_text = "<p>".$rating_text."</p>";
                            $infoHTML = "<div class=\"mapbubble\">";
                            $infoHTML.= "<a href=\"".$property_url."\">";
                            $infoHTML.= "<img class=\"alignleft\" src=\"".$property["image_thumbnail"]."\" alt=\"".addslashes($property["name"])."\" />";
                            $infoHTML.= "</a>";
                            $infoHTML.= "<h2>";
                            $infoHTML.= "<a href=\"". $property_url ."\">". addslashes($property["name"])."</a>";
                            $infoHTML.= "</h2>";
                            $infoHTML.= $rating_text;
                            $infoHTML.= "<a href=\"". $property_url ."\" class=\"more-info\">".addslashes(_("Plus d'information"))." &raquo;</a>";
                            $infoHTML.= "<div class=\"clear\"></div>";
                            $infoHTML.= "</div>";
                          }
                          ?>
                          addPropertyToMap(infocontent,hostelMarker,<?php echo $property["geo_latitude"]; ?>, <?php echo $property["geo_longitude"]; ?>, bounds, image, iconshadow, '<?php echo addslashes($property["name"]); ?>', <?php echo (string)$property["id"]; ?>, '<?php echo $infoHTML; ?>');
                          <?php
                         } //if lat !=0 and lng !=0
                       } // foreach $prop_type
                    } //endforeach property
                  }
                  ?>
                  if(specific_index == null)
                  {
                    InfoW.map.fitBounds(bounds);
                  }
                  else
                  {
                    InfoW.map.setZoom(16);
                    InfoW.map.setCenter(hostelMarker[specific_index].position);
                    InfoW.openInfoWindow(hostelMarker[specific_index],infocontent[specific_index]);
                    specific_index = null;
                    hostel_target_id = 0;
                  }

                  var styles =  [{
                                  url: '<?php echo site_url("images/map-cluster.png"); ?>',
                                  height: 50,
                                  width: 50,
                                  opt_anchor: [25, 0],
                                  opt_textColor: '#ffffff',
                                  opt_textSize: 14
                                 },
                                 {
                                   url: '<?php echo site_url("images/map-cluster.png"); ?>',
                                   height: 50,
                                   width: 50,
                                   opt_anchor: [25, 0],
                                   opt_textColor: '#ffffff',
                                   opt_textSize: 14
                                  },
                                  {
                                    url: '<?php echo site_url("images/map-cluster.png"); ?>',
                                    height: 50,
                                    width: 50,
                                    opt_anchor: [25, 0],
                                    opt_textColor: '#ffffff',
                                    opt_textSize: 14
                                   }
                                 ];

                  var mcOptions = {maxZoom: 15,styles: styles};
                  markerCluster = new MarkerClusterer(InfoW.map, hostelMarker, mcOptions);
                }
								var count = true;
								$(function(){

									$("button#togglemap-side").click(function ()
						                    {
						  										$.fancybox({
						    		              						'content'         : '<div id="map_box_canvas"></div><h2 class="gradient-back">'+"<?php echo htmlspecialchars(_("Commentaires et évaluations de voyageurs"));?>"+'</h2><span id="comment-translate-menu"></span><div id="map_box_reviews"></div>',
						  	      										    'onComplete' : function(){
						  	      										                               appendBootstrap("map_box_canvas",'initializeCityMap');
						    		                                                   }
						    		              					  });

						  		      					return false;

						  									  });

									$("a.city-map-popup").click(function ()
                      {
    									//$.fancybox.showActivity();
                        hostel_target_id = $(this).attr('rel');
                        $.fancybox({
              						'content'         : '<div id="map_box_canvas"></div><h2 class="gradient-back">'+"<?php echo htmlspecialchars(_("Commentaires et évaluations de voyageurs"));?>"+'</h2><span id="comment-translate-menu"></span><div id="map_box_reviews"></div>',
      										'onComplete' : function(){
      											                        appendBootstrap("map_box_canvas",'initializeCityMap');
                                                    update_review_box(hostel_target_id);
                                                   }
              					});

      									return false;
  								  });
								});

              </script>

              <h1 class="city-title"><?php printf( gettext('Liste des logements pas chers à %s'),$city_selected);?> - <?php echo $country_selected;?></h1>
              <div class="clearfix city-menu">
								<ul class="tabing view-menu clearfix">
									<?php /*?><li><a id="#tous" href="#tous-list"><span><?php echo _("Tous")?></span></a></li><?php */?>
									<?php if($property_list["hostel_count"] != 0):?><li><a id="#hostel" href="#hostel-list"><span><?php echo _("Auberges de jeunesse")?> (<?php echo $property_list["hostel_count"];?>)</span></a></li><?php endif;?>
									<?php if($property_list["hotel_count"] != 0):?><li><a id="#hotel" href="#hotel-list"><span><?php echo _("Hôtels pas chers")?> (<?php echo $property_list["hotel_count"];?>)</span></a></li><?php endif;?>
									<?php if($property_list["apartment_count"] != 0):?><li><a id="#apart" href="#apart-list"><span><?php echo _("Appartements")?></span> (<?php echo $property_list["apartment_count"];?>)</a></li><?php endif;?>
									<?php if($property_list["guesthouse_count"] != 0):?><li><a id="#guest" href="#guest-list"><span><?php echo _("Chambres - B&B - Pensions")?></span> (<?php echo $property_list["guesthouse_count"];?>)</a></li><?php endif;?>
								</ul>
							</div>

              <?php

              if($property_list["property_count"] < 1)
              {
                ?>
                <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                <?php
              }
              /*?><div id="tous-list" class="tabdiv ui-tabs-hide">

              <div class="paging" id="paging1" style="clear:both;">

                <a rel="prev" href="#" class="page-prev action1"><?php echo _("précédente");?></a>
                <div class="state"><span id="count1">1</span> <?php echo _("de");?> <span id="total1">1</span></div>
                <a rel="next" href="#" class="page-next action1"><?php echo _("suivante");?></a>


                <span class="sort-label"><?php echo _("Classer par:");?> </span>
                <a class="sorting" id="sortname-tous" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>
                <a class="sorting activesort" id="sortprice-tous" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
                <a class="sorting" id="sortcote-tous" href="#"><span class="asc"><?php echo _("Cote");?></span></a>

                <select name="perpage1">
									<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
									<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
									<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
                </select>


               </div>

              <div id="tous-page">


                <?php
                if(false)
  							{
                  $count = 0;

                  foreach ($hostel_list as &$hostel)
                  {
                    $count++;
                    if($this->api_used == HB_API)
                    {
                      if(isset($hostel["type"]) && !empty($hostel["type"]))
                      {
                        $hostel["type"] = trim($hostel["type"]);
                      }
                      else
                      {
                        $hostel["type"] = $this->Db_hb_hostel->get_property_type($hostel["id"]);
                      }

                      if(empty($hostel["type"])) $hostel["type"] = "property";

                      $this->load->view("hb/property_list",array("hostel" => $hostel,"property_type" => $hostel["type"],"date_selected" => $date_selected,"numnights_selected" => $numnights_selected));
                    }
                    else
                    {
                      $this->load->view("hw/property_list",array("hostel" => $hostel,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected));
                    }
                  }

                  if ($count == 0)
                  {
                    ?>
                    <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                    <?php
                  }
  							}
                ?>
              </div>


              <div class="paging" id="paging1-2" style="clear:both;">

              <a rel="prev" href="#" class="page-prev action1"><?php echo _("précédente");?></a>
              <div class="state"><span id="count12">1</span> <?php echo _("de");?> <span id="total12">1</span></div>
              <a rel="next" href="#" class="page-next action1"><?php echo _("suivante");?></a>



              <select name="perpage1">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>


               </div>

               <?php
               if ($count == 0)
               {
                 ?>
              	<script>
        					$("#paging1").hide();
        					$("#paging1-2").hide();
        				</script>
                <?php
               }
               ?>

              </div><?php */?>

              <?php if($property_list["hostel_count"] != 0):?>
              <div id="hostel-list" class="tabdiv ui-tabs-hide">

              <div class="paging paging2" id="paging2" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action2"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action2"><?php echo _("précédente");?></a>
              <div class="state"><span id="count2">1</span> <?php echo _("de");?> <span id="total2">1</span></div>
              <a rel="next" href="#" class="page-next action2"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action2"><?php echo _("dernière");?></a><?php */?>

              <span class="sort-label"><?php echo _("Classer par:");?> </span>
              <a class="sorting" id="sortname-hostel" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>
              <a class="sorting activesort" id="sortprice-hostel" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
              <a class="sorting" id="sortcote-hostel" href="#"><span class="asc"><?php echo _("Cote");?></span></a>

              <select name="perpage2">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>


              <div id="hostel-page">
  							<?php
  							if(true)
  							{
    							$count = 0;
    							foreach ($property_list["hostel_list"] as $hostel)
    							{
                    if($this->api_used == HB_API)
                    {
                      if (strcasecmp($hostel["type"], 'hostel') == 0)
                      {
                        $count++;

                        $this->load->view("hb/property_list",array("hostel" => $hostel,'property_type' => $hostel["type"], "date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));

                      }
                    }
                    else
                    {
                      if ($hostel->propertyType == 'Hostel')
                      {
                        $count++;

                        $this->load->view("hw/property_list",array("hostel" => $hostel,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));

                      }
                    }
                  }

                  if ($count==0)
                  {
                    ?>
                    <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                    <?php
                  }
  							}
                ?>
              </div>

              <div class="paging" id="paging2-2" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action2"><?php echo _("première");?></a><?php */?>
              <a rel="prev" href="#" class="page-prev action2"><?php echo _("précédente");?></a>
              <div class="state"><span id="count22">1</span> <?php echo _("de");?> <span id="total22">1</span></div>
              <a rel="next" href="#" class="page-next action2"><?php echo _("suivante");?></a>
               <?php /*?><a rel="last" href="#" class="page-last action2"><?php echo _("dernière");?></a><?php */?>

              <select name="perpage2">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <?php if ($count==0){?>
              	<script>

									$("#paging2").hide();
									$("#paging2-2").hide();

								</script>

              <?php }?>

             </div>

              <?php endif;?>
              <?php if($property_list["hotel_count"] != 0):?>
              <?php //Hotel ?>

              <div id="hotel-list" class="tabdiv ui-tabs-hide">

              <div class="paging" id="paging3" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action3"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action3"><?php echo _("précédente");?></a>
              <div class="state"><span id="count3">1</span> <?php echo _("de");?> <span id="total3">1</span></div>
              <a rel="next" href="#" class="page-next action3"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action3"><?php echo _("dernière");?></a><?php */?>

              <span class="sort-label"><?php echo _("Classer par:");?> </span>
              <a class="sorting" id="sortname-hotel" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>
              <a class="sorting activesort" id="sortprice-hotel" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
              <a class="sorting" id="sortcote-hotel" href="#"><span class="asc"><?php echo _("Cote");?></span></a>

              <select name="perpage3">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

              <div id="hotel-page">
                <?php

                if(true)
  							{
                  $count = 0;
                  foreach ($property_list["hotel_list"] as $hotel)
                  {
                    if($this->api_used == HB_API)
                    {
                      if (strcasecmp($hotel["type"], 'hotel') == 0)
                      {
                        $count++;
                        $this->load->view("hb/property_list",array("hostel" => $hotel,'property_type' => $hotel["type"], "date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                    else
                    {
                      if ($hotel->propertyType == 'Hotel')
                      {
                        $count++;
                        $this->load->view("hw/property_list",array("hostel" => $hotel,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                  }

                  if ($count==0)
                  {
                    ?>
                    <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                    <?php
                  }
  							}
                ?>
              </div>

              <div class="paging" id="paging3-3" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action3"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action3"><?php echo _("précédente");?></a>
              <div class="state"><span id="count32">1</span> <?php echo _("de");?> <span id="total32">1</span></div>
              <a rel="next" href="#" class="page-next action3"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action3"><?php echo _("dernière");?></a><?php */?>

              <select name="perpage3">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <?php if ($count==0){?>
              	<script>

									$("#paging3").hide();
									$("#paging3-3").hide();

								</script>

              <?php }?>

              </div>
              <?php endif;?>
              <?php if($property_list["apartment_count"] != 0):?>
              <?php //Apart?>

              <div id="apart-list" class="tabdiv ui-tabs-hide">

              <div class="paging" id="paging4" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action4"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action4"><?php echo _("précédente");?></a>
              <div class="state"><span id="count4">1</span> <?php echo _("de");?> <span id="total4">1</span></div>
              <a rel="next" href="#" class="page-next action4"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action4"><?php echo _("dernière");?></a><?php */?>
              <span class="sort-label"><?php echo _("Classer par:");?> </span>
              <a class="sorting" id="sortname-apart" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>
              <a class="sorting activesort" id="sortprice-apart" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
              <a class="sorting" id="sortcote-apart" href="#"><span class="asc"><?php echo _("Cote");?></span></a>

              <select name="perpage4">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <div id="apart-page">
                <?php
                if(true)
  							{
                  $count = 0;
                  foreach ($property_list["apartment_list"] as $apartment)
                  {
                    if($this->api_used == HB_API)
                    {
                      if (strcasecmp($apartment["type"], 'apartment') == 0)
                      {
                        $count++;
                        $this->load->view("hb/property_list",array("hostel" => $apartment,'property_type' => $apartment["type"], "date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                    else
                    {
                      if ($apartment->propertyType == 'Apartment')
                      {
                        $count++;
                        $this->load->view("hw/property_list",array("hostel" => $apartment,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                  }

                  if ($count==0)
                  {
                    ?>
                    <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                    <?php
                  }
  							}
                ?>
              </div>


              <div class="paging" id="paging4-2" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action4"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action4"><?php echo _("précédente");?></a>
              <div class="state"><span id="count42">1</span> <?php echo _("de");?> <span id="total42">1</span></div>
              <a rel="next" href="#" class="page-next action4"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action4"><?php echo _("dernière");?></a><?php */?>

              <select name="perpage4">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <?php if ($count==0){?>
              	<script>

									$("#paging4").hide();
									$("#paging4-2").hide();

								</script>

              <?php }?>

              </div>

              <?php endif;?>
              <?php if($property_list["guesthouse_count"] != 0):?>
							<?php //CGuesthouse?>

              <div id="guest-list" class="tabdiv ui-tabs-hide">

              <div class="paging" id="paging5" style="clear:both;">
              <?php /*?><a rel="first" href="#" class="page-first action5"><?php echo _("première");?></a> <?php */?>
              <a rel="prev" href="#" class="page-prev action5"><?php echo _("précédente");?></a>
              <div class="state"><span id="count5">1</span> <?php echo _("de");?> <span id="total5">1</span></div>
              <a rel="next" href="#" class="page-next action5"><?php echo _("suivante");?></a>
              <?php /*?><a rel="last" href="#" class="page-last action5"><?php echo _("dernière");?></a><?php */?>
              <span class="sort-label"><?php echo _("Classer par:");?> </span>
              <a class="sorting" id="sortname-guest" href="#"><span class="asc"><?php echo _("Hostel Name");?></span></a>
              <a class="sorting activesort" id="sortprice-guest" href="#"><span class="asc"><?php echo _("Prix");?></span></a>
              <a class="sorting" id="sortcote-guest" href="#"><span class="asc"><?php echo _("Cote");?></span></a>

              <select name="perpage5">
								<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
								<option selected="selected" value="25"><?php printf(gettext("%s par page"),"25");?></option>
								<option value="50"><?php printf(gettext("%s par page"),"50");?></option>
              </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <div id="guest-page">
                <?php

                if(true)
  							{
                  $count = 0;
                  foreach ($property_list["guesthouse_list"] as $guesthouse)
                  {
                    if($this->api_used == HB_API)
                    {
                      if (strcasecmp($guesthouse["type"], 'guesthouse') == 0)
                      {
                        $count++;
                        $this->load->view("hb/property_list",array("hostel" => $guesthouse,'property_type' => $guesthouse["type"],"date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                    else
                    {
                      if ($guesthouse->propertyType == 'Guesthouse')
                      {
                        $count++;
                        $this->load->view("hw/property_list",array("hostel" => $guesthouse,"date_selected" => $date_selected,"numnights_selected" => $numnights_selected, "count_hostel" => $count));
                      }
                    }
                  }

                  if ($count==0)
                  {
                    ?>
                    <p class="no-result dotted-line-top"><?php echo _('Désolé aucun résultat pour ce critère');?></p>
                    <?php
                  }
  							}
                ?>

              </div>

               <div class="paging" id="paging5-2" style="clear:both;">
                <?php /*?><a rel="first" href="#" class="page-first action5"><?php echo _("première");?></a> <?php */?>
                <a rel="prev" href="#" class="page-prev action5"><?php echo _("précédente");?></a>
                <div class="state"><span id="count52">1</span> <?php echo _("de");?> <span id="total52">1</span></div>
                <a rel="next" href="#" class="page-next action5"><?php echo _("suivante");?></a>
                <?php /*?><a rel="last" href="#" class="page-last action5"><?php echo _("dernière");?></a><?php */?>

                <select name="perpage5">
									<option value="1"><?php printf(gettext("%s par page"),"1");?></option>
									<option selected="selected" value="5"><?php printf(gettext("%s par page"),"5");?></option>
									<option value="10"><?php printf(gettext("%s par page"),"10");?></option>
                </select>
                <?php /*?><span class="show"><?php echo _('résultats');?> :</span><?php */?>

               </div>

               <?php if ($count==0){?>
              	<script>

									$("#paging5").hide();
									$("#paging5-2").hide();

								</script>

              <?php }?>

              </div>
							<?php endif;?>

							<script type="text/javascript" src="<?php echo base_url();?>js/tabs.js"></script>
              <script type="text/javascript" src="<?php echo base_url();?>js/sort.js"></script>
              <script type="text/javascript" src="<?php echo base_url();?>js/paginateview.js"></script>
							<?php /*?><script type="text/javascript" src="<?php echo base_url();?>js/first-sort.js"></script> <?php */?>
