<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/side_search_box'); ?>	
	<?php $this->load->view('includes/video-popup'); ?>
	<?php /*?><?php $this->load->view('includes/groupe'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
	<?php $this->load->view('includes/widget-cours'); ?>
	<?php $this->load->view('includes/year-10'); ?><?php */?>
</div>
<div id="main" class="grid_12">
	<script>
	var count = true; 
	$(document).ready(function() {
			appendBootstrap();    									 
	});			
	</script>
	<?php //$this->load->view('includes/breadcrumbs'); ?>	
	<div class="box_content box_round group">
	 	<h1 class="content_title"><?php echo _("Hébergement")." - ".mb_ucwords($country_selected);?></h1>
			
    <div class="content_block">
      <p><?php printf( gettext("Vous recherchez une auberge de jeunesse, un hôtel pas cher, un appartement, une chambre d'hôtes, un Bed and Breakfast B&B ou une Pension ? N’allez pas plus loin, tous les bons plans sont sur %s"),$this->config->item('site_name'));?></p>
      <p align="center" style="border:1px solid #EAA040;border-right:none;border-left:none;padding:5px 0 5px 0;"><strong style="font-size:1.3em;"><?php echo _("Prix à partir de $5 la nuit par personne !");?></strong></p>
      <p><?php echo _("Comme des milliers de jeunes et moins jeunes tous les mois, réservez-vous aussi en toute sécurité votre auberge idéale au meilleur prix dans toutes les villes listées ci-dessous.");?></p>
      <h2 class="dotted-line"><?php echo _("Les Villes")." - ".mb_ucwords($country_selected);?></h2>      
    </div>
    
		<div class="content_block">
			<div id="map_wrap">
				<div id="map_canvas"></div>
			</div>
		</div>
		<div class="continent_block">
     
        <?php
               
        $numloop = count($cities_of_country->Country->Cities);
        $breakloop = ceil($numloop/4);
        $totalloop = 0;
        $loopcount = 0;
        ?>
        <div class="group">        
				<?php
				
				foreach($cities_of_country->Country->Cities as $city)
				{
				
					$cityen = $city->cityName;
					$citydisplay = $city->cityName;
					if(!empty($city->cityNameTranslated))
					{
						$citydisplay = $city->cityNameTranslated;
					}
					$totalloop++;
					if ($loopcount == 0){?>
					<ul class="alignleft">
					<?php }
					$loopcount++; ?>
					
					<li>
					<a href="<?php echo site_url(customurlencode($country_selected)."/".customurlencode($citydisplay))?>"> <?php echo $citydisplay;?>
					
					<?php if ($this->wordpress->get_option('aj_show_encity') == 'true'){ ?>
					<?php echo $cityen;?>
					<?php }?>
					</a>
					</li>
					
					<?php if (($loopcount == $breakloop) || ($totalloop == $numloop)){?>
					
					</ul>
					
					<?php $loopcount = 0; }
				
				}
				?>
				
			</div>

		</div>
        
        <a class="return" href="<?php echo site_url(stripAccents($continent_lang));?>">&laquo; <?php echo _("Retour à tous les pays d'");?><?php echo $continent_lang;?></a>

	</div>

</div>