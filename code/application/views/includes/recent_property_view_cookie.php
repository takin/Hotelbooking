 <?php 
	// if cookies set show Recent viewed widget///
	if(!empty($_COOKIE['last_review_property']))
	{ ?>
    <div class="box_content box_round group rating_bars">	       
        <span class="title" style="margin-bottom: 12px;"><?php echo _('Recently Viewed')?></span>
        <div class="bar-rating">        
        <?php 
         $cookieArray = explode(",", $_COOKIE['last_review_property']);
		foreach($cookieArray as $key => $value)
		{			
      	 // get the hostel info from db like name and overall rating         
		 $hostel_db_info = $this->Db_hb_hostel->get_hostel($value);
		 if( $hostel_db_info) // we found the hostel record
		 {
            $this->load->model('Db_links');
			$property_url_link = $this->Db_links->build_property_page_link($hostel_db_info->property_type, $hostel_db_info->property_name, $hostel_db_info->property_number,$this->site_lang);
		?>
        <div class="bar-back group" id="remove_property_<?php echo $value;?>">
        	<div class="bar-top"></div>	
            <a href="javascript:void(0);" title="<?php echo $value;?>" rel="remove_it_property"><img src="<?php echo base_url();?>images/na-book.png" alt=""/></a>		
			<span class="rating-cat"><a href="<?php echo $property_url_link;?>"><?php echo $hostel_db_info->property_name;?></a> </span>
			<span class="rating-value">	
			<?php if(!empty($hostel_db_info->rating_overall)){
					echo ceil($hostel_db_info->rating_overall).' %';
				}else{
					echo '0 %';
				}?></span>
		</div>       
     <?php } //---- end of if statement
	 }?> 
     </div>
     <script type="text/javascript">
			$(document).ready(function(){
								
				$('a[rel*=remove_it_property]').click(function(){
										
					$property_id = $(this).attr('title');
					
					$.ajax({
							type:"POST",
							url:'<?php echo site_url("cmain/ajax_review_remove_cookie/");?>',
							data:'property_id='+$property_id,
							dataType:'json',
							success:function(response)
							{								
								if(response.status)
								{
									$('#remove_property_'+$property_id).hide();
								}else
								{
									return false;
								}
							}
						});
					
				});
				
		
			});
			</script> 
    </div>
    <?php }?>