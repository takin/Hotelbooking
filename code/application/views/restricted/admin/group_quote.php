<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?>
</div>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
	  <div id="group_booking_quote">
		<h1 class="content_title">Quote tool</h1>
      <form id="group_quote" action="<?php echo site_url('group_quote');?>" method="post">
			<?php
			$this->Db_links->select_domains("Domain","domain","domain","www.aubergesdejeunesse.com");
			?>
			<br />
			<label for="api">API</label>
			<select name="api" id="api">
  			<option selected="selected" value="HB">HB</option>
  			<option value="HW">HW</option>
			</select>
			<br />
			<label for="api">Quote Type</label>
			<select name="quote_type" id="quote_type">
  			<option selected="selected" value="regular">Regular</option>
  			<option value="budget">Budget</option>
  			<option value="no_avail">No availability</option>
			</select>
			<br />
			<label for="expiry_d">Expiry date</label>
			<?php
			select_day("expiry_d","expiry_d");
			select_month_year("expiry_my","expiry_my","",0,12);
			?>
			<br />
			<br />
			<label for="quote_notes">Quotes message</label>
			<textarea name="quote_notes" id="quote_notes" rows="5" cols="50"></textarea>
			<br />
			<br />
			<h1 class="content_title">Request info</h1>
			<label for="req_firstname">Reference</label>
			<input type="text" name="req_custom_ref" id="req_custom_ref" value="GRPmcweb" />
			<br />
			<label for="req_firstname">Firstname</label>
			<input type="text" name="req_firstname" id="req_firstname" />
			<br />
			<label for="req_lastname">Lastname</label>
			<input type="text" name="req_lastname" id="req_lastname" />
			<br />
			<label for="req_email">Email</label>
			<input type="text" name="req_email" id="req_email" />
			<br />
			<br />
			<script>
		jQuery(document).ready(function(){
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'req_co_en','req_ci_en','','');
		});

		</script>
			<label for="req_co_en">Country</label>
			<select name="req_co_en" id="req_co_en" autocomplete="off" onchange="setCities('<?php echo _('Choisir la ville'); ?>','req_co_en','req_ci_en');">
				<option value=""><?php echo _('Choisir le pays'); ?></option>
				</select>
				<br />
			<label for="req_ci_en">City</label>
			<select name="req_ci_en" id="req_ci_en" autocomplete="off">
				<option value=""><?php echo _('Choisir la ville'); ?></option>
				</select>
			<br />
			<label for="expiry_d">Arrival date</label>
			<?php
			select_day("arrival_d","arrival_d");
			select_month_year("arrival_my","arrival_my","",0,24);
			?>
			<br />
			<label for="req_num_nights">Nights</label>
			<input type="text" name="req_num_nights" id="req_num_nights" />
			<br />
			<label for="req_total_people">Total people</label>
			<input type="text" name="req_total_people" id="req_total_people" />
			<br />


			<h1 class="content_title">Quote 1</h1>
      <?php
			$this->load->view('restricted/admin/group_quote_property',array('quote_id' => 'quote1'));
			?>
			<h1 class="content_title">Quote 2</h1>
      <?php
			$this->load->view('restricted/admin/group_quote_property',array('quote_id' => 'quote2'));
			?>
			<h1 class="content_title">Quote 3</h1>
      <?php
			$this->load->view('restricted/admin/group_quote_property',array('quote_id' => 'quote3'));
			?>
			<br />
			<p align="right"><input  type="submit" /></p>
      </form>
    </div>
  </div>
</div>