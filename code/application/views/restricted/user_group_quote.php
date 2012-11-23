<?php
/*
NOTE: This is the HB form example for the book now.

<form name="frmOffer1" action="https://secure.hostelbookers.com/checkout/index.cfm" method="post">
<input type="hidden" name="uidOffer" value="ddb2dfb4-692a-4934-a64e-0cc37347d083" />
<input type="hidden" name="strOfferID" value="1FF7E50F-B42F-8009-0DE4FB7A00FED6AB" />
<input type="hidden" name="fuseaction" value="groupBookingCheckout.gateway" />
<input type="hidden" name="isDynamic" value="1" />
<input type="hidden" name="strSearchBy" value="property" />
<input type="hidden" name="dteArrival" value="16/02/2013" />
<input type="hidden" name="intPeople" value="25" />
<input type="hidden" name="dteArrival" value="" />
<input type="hidden" name="intNights" value="1" />
<input type="hidden" name="strProperty" value="YHA Bath" />
<input type="hidden" name="strDestination" value="Bath" />
<input type="hidden" name="strAccommodationType" value="" />
<input type="hidden" name="intDestinationID" value="-1" />
<input type="hidden" name="intPropertyID" value="4952" />
<input type="hidden" name="strTab" value="Book" />
<input type="hidden" name="strHostelCurrency"	value="GBP" />
<input type="hidden" name="strUsersCurrency" value="EUR" />
<input type="hidden" name="strOfferRate" value="0.6197" />
<input type="hidden" name="intEnquiryID" value="396446" />
<input type="hidden" name="language" value="en" />
<input type="hidden" name="strAffiliate" value="mcweb" />
<input type="hidden" name="firstName" value="Chris" />
<input type="hidden" name="lastName"	value="Auberges" />
<input type="hidden" name="phone" value="Canada +1: 778-848-2747" />
<input type="hidden" name="email" value="info@youth-hostels.co.uk" />
<input type="hidden" name="females" value="0" />
<input type="hidden" name="males" value="25" />
<input type="hidden" name="intRoomType" value="3" />
<input type="hidden" name="strUserRate" value="0.7626" />
<input type="hidden" name="intRoomId_7" value="7"	/>
<input type="hidden" name="isPrivate_7" value="0"	/>
<input type="hidden" name="intBedsToBook_7" value="25"	/>
<input type="hidden" name="intRoomId_8" value="8"	/>
<input type="hidden" name="isPrivate_8" value="0"	/>
<input type="hidden" name="intBedsToBook_8" value="15"	/>
<a href="#" onclick="document.forms.frmOffer1.submit(); return false;"><img src="http://en-img.hb-assets.com/r2/assets/lang/images/buttons/btnbooknowbluebg.png" class="grpBookNow"/></a>
</form>
 */

/*
<div class="grid_4" id="sidebar">
	<div class="box_content group box_round site-info-box">
	<ul class="site-info group">
	 <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
	 <li class="first" id="rules"><img height="38px" src="<?php echo secure_site_url();?>images/<?php echo $csspath; ?>/sideinfo-rules.png" alt="" /><span><?php printf(gettext("%s est réglementé par l'Union Européenne."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
	 <li id="secure"><img height="38px" src="<?php echo secure_site_url();?>images/GandiSSL2.png" alt="" /><span><strong><?php printf(gettext("100%% sécurisé."));?></strong> <?php echo _("Paiements sécurisés et encryptés pour votre sécurité.");?></span></li>
	 <li id="bestprice"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-10percent.png" alt="" /><span><?php echo _("Seulement 10% pour garantir votre réservation.");?></span></li>
	 <li id="support"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-support.png" alt="" /><br /><span><?php echo _('Text/SMS (FREE)')?></span></li>
	 <li id="forall"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-forall.png" alt="" /><span><?php echo _('Check your reservation on your Mobile')?></span></li>
	 <li class="last" id="member"><img height="38px" src="<?php echo secure_site_url();?>images/sideinfo-member.png" alt="" /><span><?php printf(gettext("%s Pas besoin de carte de membre pour recevoir les meilleurs prix du Net."),"<strong>".$this->config->item('site_name')."</strong>");?></span></li>
	</ul>
	</div>
</div>
*/
?>
<div id="main" class="grid_16 group_quote_page">
 <div class="box_content box_round group booking">
 <h1 class="content_title"><?php echo _('Your personalized group booking quote - Here is your offer!');?></h1>


 	<h2 class="booking_section_title box_round green_gradient_faded" id="step1-title"><span>1. <?php echo _('Details.');?></span></h2>
	<div class="group_trip_details booking_section_content">
	<table cellpadding="0" cellspacing="0" style="width:auto;">
		<tr>
			<td class="first"><?php echo _("Numéro de référence");?>:</td>
			<td><strong><?php echo $group_request->req_custom_ref;?></strong></td>
		</tr>
		<tr>
			<td class="first"><?php echo _('Nom');?>:</td>
			<td><strong><?php echo $group_request->firstname.' '.$group_request->lastname;?></strong></td>
		</tr>
		<tr>
			<td class="first"><?php echo _('Country');?>:</td>
			<td><strong><?php echo $group_request->country;?></strong></td>
		</tr>
		<tr>
			<td class="first"><?php echo _('City');?>:</td>
			<td><strong><?php echo $group_request->city;?></strong></td>
		</tr>
		<tr>
			<td class="first"><?php echo _('Arrivée');?>:</td>
			<td><strong><?php echo $group_request->arrival_date;?></strong></td>
		</tr>
		<tr>
			<td class="first"><?php echo _('Nombre de Nuits');?>:</td>
			<td><strong><?php echo $quotes[0]->rooms[0]->nights;?></strong></td>
		</tr>
		<?php
		if(!empty($group_request->total_people))
		{
  		?>
  		<tr>
  			<td class="first"><?php echo _('Total Number of people in your group');?>:</td>
  			<td><strong><?php echo $group_request->total_people;?></strong></td>
  		</tr>
  		<?php
		}
    ?>
	</table>

 </div>

	<?php if($quote_type == 'budget'){?>

	<form action="" method="" class="group_booking extra_info_group box_round">
		<p><?php echo _('Please see below for some provisional examples of accommodation we have available. If you can answer the following questions, we will do our best to find you the perfect property:');?></p>
		<div class="group">
			 <div class="two_col">
				<div class="radiogroup">
				<label for="">1. <?php echo _('Are you happy with the types of rooms suggested below?');?></label>
				<?php /*?><input type="radio" class="radio" name="happy" value="Yes"> <span class="radio_span"><?php echo _('yes');?></span>
				<input type="radio" class="radio" name="happy" value="No" checked="checked"> <span class="radio_span"><?php echo _('no');?></span>
				<?php */?>
				</div>
			</div>
			<div class="two_col">
				<label for="">2. <?php echo _('What is your budget?');?></label>
				<?php /*?><input type="text" name="budget" />
				<?php */?>
			</div>
		</div>
		<div class="group">
			<div class="two_col">
				<div class="radiogroup">
				<label for="">3. <?php echo _('Are the dates and number of people confirmed or will they change?');?></label>
				<?php /*?><input type="radio" class="radio" name="changes" value="Yes"> <span class="radio_span"><?php echo _('yes');?></span>
				<input type="radio" class="radio" name="changes" value="No" checked="checked"> <span class="radio_span"><?php echo _('no');?></span>
				<?php */?>
				</div>
			</div>
			<div class="two_col">
				<label for="">4. <?php echo _('When will you be ready to make a payment to secure your booking with a 20% deposit?');?></label>
				<?php /*?><input type="text" name="whenready" />
				<?php */?>
			</div>
		</div>
    <?php /*?>
		<input type="button" value="<?php echo _('Yes I am ready, Contact me');?>" id="submit" class="box_round button-green side_submit hoverit" name="submit" onfocus="this.blur()">
		<?php */?>
		</li>
	</ul>
	</form>
	<?php }?>

 <h2 class="booking_section_title box_round green_gradient_faded" id="step1-title"><span>2. <?php echo _('Group booking quote information');?></span></h2>
 <div class="group_quote_info booking_section_content">
	<h3 class="not_confirmed"><strong><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING');?></strong></h3>
	<p><?php echo _('Please have a look at the offer below and check our website to get more information about the property.');?></p>
	<?php
	if($quote_type == 'regular')
	{
	  ?>
	  <p>
		<?php
	  echo _('Beds have been put aside for your group until the following date:')." <strong>". $quotes[0]->expiry_date."</strong>";
	  ?>
	  </p>
	  <p>
	  <?php
	  echo _('After this date, these beds will be released to other groups, so do not wait to book!');
	  ?>
	  <p>
	  <?php

	}
	?>
 </div>

 <h2 class="booking_section_title box_round green_gradient_faded" id="step1-title"><span>3. <?php echo _('Your offers');?></span></h2>

 <div class="group_quote_offers booking_section_content">

 <?php
 $i = 0;
 foreach($quotes as $quote):$i++?>
 <div class="quote_offer group box_round">
 	<h3><a target="_blank" href="<?php echo $quote->property_url;?>"><?php echo $quote->property->property_name;?></a><span class="quote_offer_nb"></span></h3>
	<div class="quote_hostel_info">
	<?php
  $thumb_url = site_url('images/V2/no_pictures.png');
	if(!empty($quote->property->thumb))
	{
	  $thumb_url = $quote->property->thumb;
	}
	?>
		<a href="<?php echo $quote->property_url;?>" target="_blank"><img src="<?php echo $thumb_url;?>" width="75px" alt="" /></a>
		<p><?php echo $quote->property->desc;?></p>

	</div>
	<div class="quote_hotel_includes">
	<p class="includes"><strong><?php echo _("What's Included");?>:</strong></p>
		<?php if(!empty($quote->includes)){?>
		<ul class="green-li">
			<?php foreach($quote->includes as $include):?>
			<li><?php echo $include->desc;?></li>
			<?php endforeach;?>
		</ul>
		<?php }?>
	</div>
	<div class="quote_room_list">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th class="first"><?php echo _('Room description'); ?></th>
				<th><?php echo $quote->col2head; ?></th>
				<th><?php echo $quote->col3head; ?></th>
				<th class="last"><?php echo _('Price per Person per Night'); ?></th>
			</tr>
			<?php foreach($quote->rooms as $room):?>
			<tr>
				<?php if (!empty($room->trans_desc)){?>
				<td class="first"><span title="<?php echo _('VERSION ORIGINALE :');?> <?php echo $room->desc;?>"><?php echo $room->trans_desc;?></span></td>
				<?php }else{?>
				<td class="first"><?php echo $room->desc;?></td>
				<?php }?>
				<td><?php echo $room->people;?></td>
				<td><?php echo $room->nights;?></td>
				<td class="last">
				<?php
				if(!empty($room->book_price))
				{
				echo $quote->total_book_cur." ";
				echo $room->book_price;
				}
				if (!(empty($room->book_price)) && !(empty($room->custom_price)))
				{
				  ?>
				   /
				  <?php
				}
				if(!empty($room->custom_price))
				{
  				echo $quote->total_custom_cur." ";
  				echo $room->custom_price;
				}
				?>
				</td>
			</tr>
			<?php endforeach;?>

			</tr>
		</table>
		<div class="quote_payment box_round group">
		<h4><?php echo _('Total'); ?>:
		<?php
		if(!empty($quote->total_book))
		{
		  echo $quote->total_book_cur." ".$quote->total_book;
		}
		if (!(empty($quote->total_book)) && !(empty($quote->total_custom)))
		{
		?> / <?php
		}
		if(!empty($quote->total_custom))
		{
		  echo $quote->total_custom_cur." ".$quote->total_custom;
		}
		?>
		</h4>
		<?php if($quote_type == 'regular'){?>
		<ul>
			<li><?php echo _('Offer Expiry date');?>:<strong> <?php echo $quote->expiry_date;?></strong></li>
			<li><?php echo _('Non-refundable and non-transferable deposit to secure this booking');?>:
			<strong>
			<?php
			if(!empty($quote->down_pay_book))
			{
  			echo $quote->total_book_cur." ";
  			echo $quote->down_pay_book;
			}
			if (!(empty($quote->down_pay_book)) && !(empty($quote->down_pay_custom)))
			{
			?> / <?php
      }
      if(!empty($quote->down_pay_custom))
      {
  			echo $quote->total_custom_cur." ";
  			echo $quote->down_pay_custom;
      }
			?>
			</strong>
			</li>
			<li><?php printf(gettext('If the offer is accepted, the balance must be paid on %s'),"<strong>".$quote->balance_payment_date.'</strong>');?></li>
			<?php /*?><a href="" title="" class="city_uplink button-green hoverit box_round group_book_now"><?php echo _('Book now');?> »</a><?php */?>
		</ul>
		<?php }?>

	</div>
	</div>

 </div>
 <?php endforeach;?>

 </div>
 <h2 class="booking_section_title box_round green_gradient_faded" id="step1-title"><span>4. <?php echo _('Special Notes');?></span></h2>
 <div class="booking_section_content">
 <p><?php echo $quote_notes;?></p>
 </div>
 <h2 class="booking_section_title box_round green_gradient_faded" id="step1-title"><span>5. <?php echo _('Important Notes');?></span></h2>
 <div class="booking_section_content">
		<?php if($quote_type == 'regular'){?>
		<p><?php echo _('The deposit is to be paid in British pounds as our Worldwide group center is located in London, England.');?></p>
		<?php }?>
		<p><?php echo _('The Average price per person per night is for the requested arrival date and length of stay, actual day-by-day prices may vary.');?></p>
		<?php if($quote_type == 'regular'){?>
		<p><?php echo _("We act as agent only in respect of all bookings we take or make on your behalf. For all bookings your contract will be with the supplier of the accommodation or other service concerned. The suppliers' booking conditions will apply to your contract as well as our own Terms and conditions and any terms and conditions of any other booking intermediary involved in our booking.");?></p>

		<p><?php echo _('Copies of the other applicable terms and conditions are available from us on request.');?></p>

		<p><?php echo _('Exchange rates are calculated using the typical ATM rate on www.oanda.com. Final balance payments will be subject to the exchange rate available 49 days prior to arrival date.');?></p>

		<p><?php echo _('All card payments incur a 2% fee and all bank fees are to be paid by the booking party.');?></p>

		<p><?php echo _('As this is a group booking, group leader will need to be present at check in and the property may require	a security bond at check-in.');?></p>

    <?php /*?>
    		<p><?php echo _('Balance is required 36 days prior to arrival.');?></p>

    		<p><?php echo _('Balance not received by 36 days prior to arrival may result in cancellation of booking.');?></p>
    <?php */ ?>
    <p><?php echo _('Unpaid fees or transfer of insufficient funds may lead to cancellation of your booking.');?></p>
    <?php /*?>
    		<p><?php echo _('Cancellation policy: 36 days prior to arrival 100% loss of booking value.');?></p>
    <?php */?>
		<?php }?>
 </div>
<?php /*?> <?php debug_dump($quote);
debug_dump($group_request);?><?php */?>
 </div>
</div>
