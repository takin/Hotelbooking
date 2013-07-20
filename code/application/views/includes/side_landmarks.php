<div class="box_content box_round">
	<?php if(isset($filters ['landmark'] -> landmark_name)): ?>
		<?php
		if(isset($filters ['landmark'] -> landmark_name_ts)) {
			$filters ['landmark'] -> landmark_name = $filters ['landmark'] -> landmark_name_ts;
		}
		?>
	<?php if(!isset($filters['type'])): ?>		
		<ul>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/' . url_title($filters ['landmark'] -> landmark_name) .'/type/'. $category['property'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties close to %s'), $filters ['landmark'] -> landmark_name), array('title' => sprintf( gettext('Properties close to %s'), $filters ['landmark'] -> landmark_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/' . url_title($filters ['landmark'] -> landmark_name) .'/type/'. $category['hostel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hostels close to %s'), $filters ['landmark'] -> landmark_name), array('title' => sprintf( gettext('Hostels close to %s'), $filters ['landmark'] -> landmark_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/' . url_title($filters ['landmark'] -> landmark_name) .'/type/'. $category['apartment'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Apartments close to %s'), $filters ['landmark'] -> landmark_name), array('title' => sprintf( gettext('Apartments close to %s'), $filters ['landmark'] -> landmark_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/' . url_title($filters ['landmark'] -> landmark_name) .'/type/'. $category['hotel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hotels close to %s'), $filters ['landmark'] -> landmark_name), array('title' => sprintf( gettext('Hotels close to %s'), $filters ['landmark'] -> landmark_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/' . url_title($filters ['landmark'] -> landmark_name) .'/type/'. $category['campsite'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Camping close to %s'), $filters ['landmark'] -> landmark_name), array('title' => sprintf( gettext('Camping close to %s'), $filters ['landmark'] -> landmark_name)));  ?>
			</li>			
		</ul>
	<?php endif; ?>
	<?php endif; ?>	
</div>


<div class="box_content box_round">
	<p>
		<strong>
			<?php echo _('Landmarks'); ?>
		</strong>
	</p>
	<?php if(is_array($city_landmarks)): ?>
		<ul>					
	<?php foreach ($city_landmarks as $city_landmark): ?>
		<li>
			<?php
			if(isset($filters['type']) && $this->uri->segment(6) != null) {
				
				switch ($filters['type']) {
					case 'campsite':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name) .'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Camping close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Camping close to %s'), $city_landmark -> landmark_name))); 
						break;
					
					case 'guesthouse':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name).'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Rooms close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Rooms close to %s'), $city_landmark -> landmark_name))); 
						break;
						
					case 'apartment':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name).'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Apartments close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Apartments close to %s'), $city_landmark -> landmark_name))); 
						break;
					
					case 'hotel':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name).'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hotels close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Hotels close to %s'), $city_landmark -> landmark_name))); 
						break;
					
					case 'hostel':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name).'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hostels close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Hostels in %s'), $city_landmark -> landmark_name))); 
						break;
					
					case 'property':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark -> landmark_name).'/type/'. $this->uri->segment(6).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties close to %s'), $city_landmark -> landmark_name), array('title' => sprintf( gettext('Properties close to %s'), $city_landmark -> landmark_name))); 
						break;
						
					default:
						
						break;
				}
				 
			} else {
				echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/landmark/'.url_title($city_landmark->landmark_name).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties close to %s'),$city_landmark->landmark_name), array('title' => sprintf( gettext('Properties close to %s'),$city_landmark->landmark_name)));
			}
			?>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
<script type="text/javascript">

	$( "div.box_content" ).each(function() {
		if(!$.trim( $(this).html() ).length) {
			$(this).hide(); 
		}		 		
	});

</script>