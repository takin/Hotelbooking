<div id="sidebar" class="grid_4">
    <?php $this->load->view('includes/side_search_box'); ?>	
		<?php $this->load->view('includes/video-popup'); ?>
		<?php $this->load->view('includes/popular_city'); ?>
    <?php $this->load->view('includes/testimonials'); ?>
		<?php $this->load->view('includes/siteinfo'); ?>	  
		<?php //$this->load->view('includes/widget-cours'); ?>
    <?php //$this->load->view('includes/year-10'); ?>
    <?php //$this->load->view('includes/groupe'); ?>
    <?php //$this->load->view('includes/facebook'); ?>		
</div>

<div id="main" class="grid_12">
	<div class="box_content box_round group">
			<h1 class="content_title"><?php echo _("30,000 Auberges et logements pas chers");?></h1>
                            
      <div class="content_block">
      <h2><?php echo _("Plus de 30,000 Logements dernière minute pas chers accessibles en seulement quelques clics.");?></h2>
        <p><?php echo _("Bienvenue sur le répertoire le plus complet d'Auberges De Jeunesse, Hôtels pas chers, appartements, chambre d’hôtes, Bed and Breakfast B&B et Pensions en français. Veuillez sélectionner un continent afin de voir tous les pays ou vous pourrez trouver des logements dernière minute pas chers.. Vous pouvez aussi directement choisir le pays dans la liste ci-dessous:");?></p>
                       
      </div>
      <div class="content_block">
				<div id="map_wrap">
						<div id="map_canvas"></div>
				</div>
			</div>
      
      <?php /*?><ul class="continent tabing group">
				<li><a href="#europe"><?php echo $tr_EU;?></a></li>
				<li><a href="#amerique"><?php echo $tr_NA;?></a></li>
				<li><a href="#asie"><?php echo $tr_AS;?></a></li>
				<li><a href="#amerique-sud"><?php echo $tr_SA;?></a></li>
				<li><a href="#oceanie"><?php echo $tr_OC;?></a></li>
				<li><a href="#afrique"><?php echo $tr_AF;?></a></li>
      </ul><?php */?>
      
      <div class="continent_block tabdiv" id="europe">
       <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_EU);?>"><?php echo $tr_EU;?> &raquo;</a></h3>
          
            <?php 
            $continent_total = count($eu_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($eu_country_list as $eu_country)
            {
              $country_name = $eu_country->countryName;
              if(!empty($eu_country->countryNameTranslated))
              {
                $country_name = $eu_country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_EU)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
            
        </div>
      </div>
      
      <div class="continent_block tabdiv ui-tabs-hide" id="amerique">
        
        <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_NA);?>"><?php echo $tr_NA;?> &raquo;</a></h3>
          
            <?php 
            $continent_total = count($na_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($na_country_list as $country)
            {
              $country_name = $country->countryName;
              if(!empty($country->countryNameTranslated))
              {
                $country_name = $country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_NA)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
                
                        
        </div>
      </div>
      
       
      <div class="continent_block tabdiv ui-tabs-hide" id="asie">
        
        <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_AS);?>"><?php echo $tr_AS;?> &raquo;</a></h3>
            <?php 
            $continent_total = count($as_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($as_country_list as $country)
            {
              $country_name = $country->countryName;
              if(!empty($country->countryNameTranslated))
              {
                $country_name = $country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_AS)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
      
      
        </div>
      </div>
      
      
      <div class="continent_block tabdiv ui-tabs-hide" id="amerique-sud">
        
        <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_SA);?>"><?php echo $tr_SA;?> &raquo;</a></h3>
      
            <?php 
            $continent_total = count($sa_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($sa_country_list as $country)
            {
              $country_name = $country->countryName;
              if(!empty($country->countryNameTranslated))
              {
                $country_name = $country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_SA)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
      
      
        </div>
      </div>
      
      
      <div class="continent_block tabdiv ui-tabs-hide" id="oceanie">
        
        <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_OC);?>"><?php echo $tr_OC;?> &raquo;</a></h3>
            <?php 
            $continent_total = count($oc_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($oc_country_list as $country)
            {
              $country_name = $country->countryName;
              if(!empty($country->countryNameTranslated))
              {
                $country_name = $country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_OC)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
      
      
      
        </div>
      </div>
      
      
      <div class="continent_block tabdiv ui-tabs-hide" id="afrique">
        
        <div class="group">
          <h3><a href="<?php echo site_url().customurlencode($tr_AF);?>"><?php echo $tr_AF;?> &raquo;</a></h3>
            <?php 
            $continent_total = count($af_country_list);
            $numOfColumn = 4;
            $country_in_column = 0;
            $columnMaxCount = ceil($continent_total/$numOfColumn);
            $countryCount = 0;
            ?>
            <ul class="alignleft">
            <?php
            foreach($af_country_list as $country)
            {
              $country_name = $country->countryName;
              if(!empty($country->countryNameTranslated))
              {
                $country_name = $country->countryNameTranslated;
              }
              ?>
              <li><a href="<?php echo site_url(customurlencode($tr_AF)."/".customurlencode($country_name));?>"><?php echo $country_name;?></a></li>
              <?php
              $country_in_column++;
              $countryCount++;
              
              if($countryCount >= $continent_total)
              {
                ?>
                </ul>
                <?php
              }
              elseif($country_in_column >= $columnMaxCount)
              {
                ?>
                </ul>
                <ul class="alignleft">
                <?php 
                $country_in_column = 0;
              }
            }
            
            ?>
					
			</div>
		</div>
	</div>
</div>