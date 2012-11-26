<?php
/*variables
$user['email']
$user['id']
$bookings
each booking data once in foreach with booking->result():
stdClass Object
(
    [transaction_id] => 42
    [booking_time] => 2010-03-22 14:52:36
    [email] => technical@mcwebmanagement.com
    [first_name] => xzxczx
    [last_name] => 1231
    [gender] => Male
    [home_country] => Denmark
    [phone_number] => 1231231
    [customer_booking_reference] => 509-TEST
    [arrival_date_time] => 2010-03-26 11:00:00
    [property_number] => 509
    [property_name] => St Christopher's Inn
    [num_nights] => 1
    [property_grand_total] => 67.62
    [amount_charged] => 8.26
    [amount_charged_currency] => EUR
    [property_amount_due] => 52.20
    [property_currency] => GBP
)

*/
?>
<div id="sidebar" class="grid_4">
	<?php $this->load->view('includes/navuser'); ?> 
</div>
<div id="main" class="grid_12">
	<div class="box_content box_round group">
		<h1 class="content_title"><?php echo _('Vos Réservations');?></h1>        
		<p><?php printf(gettext("Voici la liste de toutes les transactions effectuées sur %s. Si vous avez des questions ou avez besoin d'aide n'hesitez pas à visiter notre centre d'aide en ligne."),$this->config->item('site_name'));?></p>     
       
		<?php if(count($bookings->result()) != 0){ ?>
		<div id="booking-table">		
		 <table cellpadding="0" cellspacing="0">
				 <thead>
				 <tr valign="middle" align="center">
						<th class="title"><?php echo _("Numéro de référence");?></th>
						<th><?php echo _("Établissement");?></th>
						<th><?php echo _("Réservé le");?></th>
						<th><?php echo _("Arrive le");?></th>
						<th><?php echo _("Nuits");?></th>
						
						<th><?php echo _("Total");?></th>
						<th><?php echo _("Arrhes et frais de service");?></th>
					 <th class="last"><?php echo _("Total du à l'arrivée");?></th>
						
				 </tr>
				 </thead>
				 
				 <tbody>
																	 
				 <?php 
		/**
		* Transactions lists and link to reviews
		*/
		 // $bookings = $this->db->get('user');
		
		foreach ($bookings->result() as $row)
		{
			echo "<tr>";
			echo "<td class=\"first\">";
			echo $row->customer_booking_reference;
			echo "</td>";
			echo "<td align=\"center\">";
			echo "<a href=\"".$this->Db_links->build_property_page_link($row->property_type,$row->property_name,$row->property_number,$this->site_lang)."\">".$row->property_name."</a>";
			echo "</td>";
			echo "<td align=\"center\">";
			echo date_conv($row->booking_time, $this->wordpress->get_option('aj_date_format'));
			echo "</td>";
			echo "<td align=\"center\">";
			echo date_conv($row->arrival_date_time, $this->wordpress->get_option('aj_date_format'));
			echo "</td>";
			echo "<td align=\"center\">";
			echo $row->num_nights;
			echo "</td>";
			
			echo "<td align=\"center\">";
			echo currency_symbol($row->amount_charged_currency).$row->property_grand_total;
			echo "</td>";
			echo "<td align=\"center\">";
			echo currency_symbol($row->amount_charged_currency).$row->amount_charged;
			echo "</td>";
			echo "<td align=\"center\">";
			echo currency_symbol($row->property_currency).$row->property_amount_due;
			echo "</td>";
			echo "</tr>";
			
		}
		?>
						
				 </tbody>
		 
		 </table>
		</div>
		<?php }else{?>
		<p><strong><?php echo _("Vous n'avez aucune réservation à ce jour.");?></strong></p>
		<?php }?>
    </div>
</div>
