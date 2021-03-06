<div class="box_content box_round">
	<?php if(isset($filters ['district'] -> district_name)): ?>
	<?php if(!isset($filters['type'])): ?>		
		<ul>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/' . $filters ['district'] -> slug .'/type/'. $category['hostel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hostels in the %s district'), $filters ['district'] -> district_name), array('title' => sprintf( gettext('Hostels in the %s district'), $filters ['district'] -> district_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/' . $filters ['district'] -> slug .'/type/'. $category['apartment'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Apartments in the %s district'), $filters ['district'] -> district_name), array('title' => sprintf( gettext('Apartments in the %s district'), $filters ['district'] -> district_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/' . $filters ['district'] -> slug .'/type/'. $category['hotel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hotels in the %s district'), $filters ['district'] -> district_name), array('title' => sprintf( gettext('Hotels in the %s district'), $filters ['district'] -> district_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/' . $filters ['district'] -> slug .'/type/'. $category['campsite'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Camping in the %s district'), $filters ['district'] -> district_name), array('title' => sprintf( gettext('Camping in the %s district'), $filters ['district'] -> district_name)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/' . $filters ['district'] -> slug .'/type/'. $category['property'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties in the %s district'), $filters ['district'] -> district_name), array('title' => sprintf( gettext('Properties in the %s district'), $filters ['district'] -> district_name)));  ?>
			</li>
		</ul>
	<?php endif; ?>
	<?php else: ?>
		<ul>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2) . '/type/'. $category['hostel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hostels in the %s district'), $city_info->display_city), array('title' => sprintf( gettext('Hostels in the %s district'), $city_info->display_city)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2) . '/type/'. $category['apartment'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Apartments in the %s district'), $city_info->display_city), array('title' => sprintf( gettext('Apartments in the %s district'), $city_info->display_city)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2) . '/type/'. $category['hotel'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hotels in the %s district'), $city_info->display_city), array('title' => sprintf( gettext('Hotels in the %s district'), $city_info->display_city)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2) . '/type/'. $category['campsite'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Camping in the %s district'), $city_info->display_city), array('title' => sprintf( gettext('Camping in the %s district'), $city_info->display_city)));  ?>
			</li>
			<li>
			<?php echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2) . '/type/'. $category['property'].(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties in the %s district'), $city_info->display_city), array('title' => sprintf( gettext('Properties in the %s district'), $city_info->display_city)));  ?>
			</li>
		</ul>
	<?php endif; ?>	
</div>

<?php if( count($city_districts) > 0 ): ?>
<div class="box_content box_round">
	<p>
	<strong>
		<?php echo _('Districts'); ?>
	</strong>
	</p>
	<?php if(is_array($city_districts)): ?>
		<ul>					
	<?php foreach ($city_districts as $city_district): ?>
		<li>
			<?php
			if( isset($filters['type']) ) {
				
				switch ($filters['type']) {
					case 'campsite':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug .'/type/'. $category['campsite'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Camping in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Camping in the %s district'), $city_district -> district_name))); 
						break;
					
					case 'guesthouse':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.'/type/'. $category['guesthouse'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Rooms in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Rooms in the %s district'), $city_district -> district_name))); 
						break;
						
					case 'apartment':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.'/type/'. $category['apartment'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Apartments in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Apartments in the %s district'), $city_district -> district_name))); 
						break;
					
					case 'hotel':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.'/type/'. $category['hotel'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hotels in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Hotels in the %s district'), $city_district -> district_name))); 
						break;
					
					case 'hostel':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.'/type/'. $category['hostel'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Hostels in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Hostels in the %s district'), $city_district -> district_name))); 
						break;
					
					case 'property':
						echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.'/type/'. $category['campsite'] .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Properties in the %s district'), $city_district -> district_name))); 
						break;
						
					default:
						
						break;
				}
				 
			} else {
				echo anchor($this->uri->segment(1).'/'.$this->uri->segment(2).'/district/'.$city_district -> slug.(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''), sprintf( gettext('Properties in the %s district'), $city_district -> district_name), array('title' => sprintf( gettext('Properties in the %s district'), $city_district -> district_name)));  
			}
			?>
			</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?>