<div id="content" class="hostel-view">

  <?php
  //Availability form
  ?>
  <form id="avail-form" action="<?php echo site_url("ma/".$hostel->property_number);?>" method="post">
  <input type="hidden" name="propertyName"   value="<?php echo $hostel->property_name; ?>"/>
  <input type="hidden" name="propertyNumber" value="<?php echo $hostel->property_number; ?>"/>
  <input type="hidden" name="propertyCity" value="<?php echo $bc_city; ?>"/>
  <input type="hidden" name="propertyCountry" value="<?php echo $bc_country; ?>"/>
  </form>
	<div class="page-meta group">
		<?php /*?><a class="edit-search" href="">Modify Search</a><?php */?>
		<h1 class="text-shadow-wrapper dot-icon"><?php echo $hostel->property_name; ?></h1>
		<p class="sub-meta text-shadow-wrapper">
     <a href="<?php echo site_url($hostel->country."/".$hostel->city);?>"><?php echo $city_selected.", ".$country_selected;?></a> - <?php echo $this->Db_term_translate->get_term_translation($hostel->property_type,$this->site_lang); ?>
      <?php
      if(!empty($hostel->rating))
      {
        ?>
        - <?php echo _("Cote");?> <span class="rating"><?php echo $hostel->rating;?>%</span>
        <?php
      }
      ?>
      </p>
	</div>

	<div class="book-now">
		<a class="white green-button"  href="#" onClick="document.getElementById('avail-form').submit(); return false;"><span class="link"><?php echo _("Réserver Maintenant");?></span></a>
	</div>

	<div class="hostel-content border-bottom">
		<?php
		//Images settings
		if (!empty($hostel->PropertyImages))
    {
      $images_html = "";
      $image_count = 0;
      $main_image_index = 0;
			$thumburl = "";
      foreach($hostel->PropertyImages as $index => $image)
      {
        if ($image->imageType == 'Thumbnail'){
					$thumburl = $image->imageURL;
				}
				if ($image->imageType == 'Main')
        {
          //if($image_count == 0) $main_image_index = $index;
          $images_html.= "<a target=\"_blank\" class=\"openup\" rel=\"".$hostel->property_name."\" href=\"". $image->imageURL."\">";
          $images_html.= "<img width=\"54\" height=\"53\" class=\"border-img\" alt=\"\" src=\"". $thumburl."\">";
          $images_html.= "</a>\n";
          $image_count++;
        }
      }
    }
		?>
		<h2 class="trigger box-shadow-wrapper minus"><span class="text-shadow-white"><?php echo _("Hostel info"); ?></span></h2>
		<div class="trigger-content white-back" style="display:block;">
			<div class="group">
			<img class="alignleft border-img" width="54" height="53" alt="" src="<?php echo $hostel->PropertyImages[$main_image_index]->imageURL;?>">
			<h3><?php echo $hostel->property_name; ?></h3>
			<p class="no-margin gray line-height-reduce">
      <?php
      echo var_check($hostel->address1,"");
      echo ' '.var_check($hostel->address2,"");
      echo ', '.var_check($hostel->city,"");
      echo ', '.var_check($hostel->country,"") ;?>
      </p>
			<a class="marg5top block-a" href="<?php echo $this->mobile->map_link($hostel->property_name,$hostel->geolatitude,$hostel->geolongitude); ?>"><?php echo _("View On Map");?></a>
			</div>
		</div>

    <?php
    if (!empty($hostel->PropertyImages))
    {
      ?>
      <h2 class="trigger box-shadow-wrapper"><span class="text-shadow-white"><?php echo _("Pictures"); ?> <?php echo "($image_count)";?></span></h2>
      <div class="trigger-content white-back gallery">
        <div class="group">
        <?php
        echo $images_html;
        unset($images_html);
        ?>
        </div>
      </div>
      <?php
    }
    ?>

		<h2 class="trigger box-shadow-wrapper"><span><?php echo _("About"); ?></span></h2>
		<div class="trigger-content white-back">
			<div class="group">
      <?php
       if(!empty($hostel->descriptionTranslated))
       {
         ?>
         <?php echo strip_tags($hostel->descriptionTranslated, '<p>');?>
         <?php
       }
       else
       {
         ?>
         <?php echo nl2p($hostel->description,false,true);?>
         <?php
       }
       ?>
			</div>
		</div>

    <?php
    if(!empty($hostel->facilities))
    {
      ?>
      <h2 class="trigger box-shadow-wrapper"><span class="text-shadow-white"><?php echo _("Commodité");?></span></h2>
      <div class="trigger-content white-back">
        <div class="group">
          <ul class="float-list check-li">
          <?php
          $facilities = (array) $hostel->facilities;

          if(!empty($hostel->facilitiesTranslated))
          {
            $facilities = (array) $hostel->facilitiesTranslated;
          }
          $max_facilities_per_column = ceil(count($hostel->facilities)/2);
          $nb_facility = 0;

          if(!empty($facilities))
          {
            foreach ($facilities as $facility)
            {
              if(!empty($facility))
              {

                $nb_facility++;
                ?>
                <li><?php echo stripslashes($facility);?></li>

                <?php
                if ($nb_facility >= $max_facilities_per_column)
                {
                  ?>
                  </ul><ul class="float-list check-li">
                  <?php
                }
              }
            }
          }
          ?>
          </ul>
        </div>
      </div>
      <?php
    }

    if(!empty($user_reviews))
    {
      ?>
      <h2 class="trigger box-shadow-wrapper"><span class="text-shadow-white"><?php echo _("Commentaires");?> (<?php echo count($user_reviews); ?>)</span></h2>
      <div class="trigger-content white-back">
        <div id="comment-list-part" class="group">
          <span id="comment-translate-menu"></span>

          <?php
          foreach($user_reviews as $user_review)
          {
            ?>
            <div class="comment-list group">
              <div class="review">
                <?php
                if(!empty($user_review["review_rating"]))
                {
                  ?>
                  <p class="rating-user"><?php echo $user_review["review_rating"];?>%</p>
                  <?php
                }
                ?>

                <p>
                  <?php
                  if(!empty($user_review["review_translated"]))
                  {
                    echo nl2p($user_review["review_translated"],false,true);
                  }
                  else
                  {
                    echo nl2p($user_review["review"],false,true);
                  }
                  ?>
                </p>
                <img class="user-say" src="<?php echo site_url();?>images/mobile/user-say.png" alt="" />
              </div>
              <p class="author">
                <?php
                if (strcasecmp($user_review["author_name"], 'Anonymous')==0)
                {
                  printf(gettext("Le %s"), date_conv($user_review["review_date"], $this->wordpress->get_option('aj_date_format')));
                }
                else
                {
                  printf(gettext("Par %s | Le %s"), $user_review["author_name"],date_conv($user_review["review_date"], $this->wordpress->get_option('aj_date_format')));
                }
                ?>
              </p>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
      <?php
    }
    ?>

    <?php
    if(!empty($hostel->directions ))
    {
      ?>
      <h2 class="trigger box-shadow-wrapper"><span class="text-shadow-white"><?php echo _("Cartes et Directions");?></span></h2>
  		<div class="trigger-content white-back">
  			<div class="group">
  			<p class="address"><?php echo var_check($hostel->address1,""); echo ' '.var_check($hostel->address2,""); echo ', '.var_check($hostel->city,"");  echo ', '.var_check($hostel->country,"") ;?></p>

          <?php
          if(!empty($hostel->directionsTranslated))
          {
            echo strip_tags($hostel->directionsTranslated, '<p>');
          }
          else
          {
            echo nl2p(var_check($hostel->directions,""),false,true);
          }
          ?>

  			<p class="view-map"><a href="<?php echo $this->mobile->map_link($hostel->property_name,$hostel->geolatitude,$hostel->geolongitude); ?>"><?php echo _("View On Map");?></a></p>
  			</div>
  		</div>
      <?php
    }

    if (!empty($hostel->conditions))
    {
      ?>
      <h2 class="trigger box-shadow-wrapper"><span class="text-shadow-white"><?php echo _("Informations Importantes");?></span></h2>
      <div class="trigger-content white-back">
        <div class="group">
          <?php
          if(!empty($hostel->conditionsTranslated))
          {
            echo strip_tags($hostel->conditionsTranslated,'<p>');
          }
          else
          {
            echo nl2p(var_check($hostel->conditions,""),false,true);
          }
          ?>

        </div>
      </div>
      <?php
    }
    ?>

	</div>
	<div class="book-now">
		<a class="white green-button"  href="#" onClick="document.getElementById('avail-form').submit(); return false;"><span class="link"><?php echo _("Réserver Maintenant");?></span></a>
	</div>
	<div class="bottom-nav">
	<a href="javascript:history.back()" class="change-search text-shadow-wrapper">&laquo; <?php echo _("Back to results");?></a>
	</div>

</div>