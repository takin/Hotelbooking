<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/side_search_box'); ?>	
	<?php $this->load->view('includes/video-popup'); ?>
	<?php /*?><?php $this->load->view('includes/groupe'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/widget-cours'); ?>
	<?php $this->load->view('includes/year-10'); ?><?php */?>
</div>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
    <h1 class="content_title"><?php echo _("Hébergement pour l'").mb_ucwords($continent_selected_lang);?></h1>		

		<div class="content_block">
      <p><?php printf( gettext("Vous recherchez une auberge de jeunesse, un hôtel pas cher, un appartement, une chambre d'hôtes, un Bed and Breakfast B&B ou une Pension ? N’allez pas plus loin, tous les bons plans sont sur %s"),$this->config->item('site_name'));?></p>
      <p align="center" style="border:1px solid #EAA040;border-right:none;border-left:none;padding:5px 0 5px 0;"><strong style="font-size:1.3em;"><?php echo _("Prix à partir de $5 la nuit par personne !");?></strong></p>
      <p><?php echo _("Comme des milliers de jeunes et moins jeunes tous les mois, réservez-vous aussi en toute sécurité votre auberge idéale au meilleur prix dans toutes les villes listées ci-dessous.");?></p>
      
      <h2 class="dotted-line"><?php echo _("Pays d'").mb_ucwords($continent_selected_lang);?></h2>
    </div>
    
    <div class="content_block">
			<div id="map_wrap">
				<div id="map_canvas"></div>
			</div>
		</div>
        
		<div class="continent_block">
         
      <div class="group">
                	
					<?php
					$numloop = count($countries_of_continent);
					
          $breakloop = ceil($numloop/4);
					$totalloop =0;
					$loopcount = 0;
                    
					foreach($countries_of_continent->Country as $country)
          {
            $country_name = $country->countryName;
            if(!empty($country->countryNameTranslated))
            {
              $country_name = $country->countryNameTranslated;
            }
            
    				$totalloop++;
    				if ($loopcount == 0)
    				{
    				  ?>
    			   <ul class="alignleft">
              <?php
    				}
            $loopcount++;
            ?>
            <li><a href="<?php echo site_url(customurlencode($country->countryContinentTranslated)."/".customurlencode($country_name));?>"><?php echo $country_name;?>
            <?php if ($this->wordpress->get_option('aj_show_encity') == 'true'){ ?>
							<?php echo $country->countryName;?>
            <?php }?>
            </a></li>                        
            <?php
            if (($loopcount == $breakloop) || ($totalloop == $numloop))
            {
              ?>
              </ul>
              <?php
              $loopcount = 0;
            }
          }
          ?>			
                     
                    
			</div>

		</div>

	</div>

</div>