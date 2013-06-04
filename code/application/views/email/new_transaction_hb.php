<?php
//variable
//$firstname
//$lastname
//Voici les autres variable qui que je nai pas ajoutee mais qui sont possible de te shipper
// laisse moi savoir ce que tu as de besoin
//$propertyNumber
//$propertyName
//$dateStart
//$numNights
//$bookCurrency
//$roomPreferences
//$nbPersons
//
//$nationality
//$gender
//$arrival_time
//$email_address
//$phone_number
//$sign_me_up



//Booking XML data

?>
<?php
$settlecur = currency_symbol($booking->payment->currencyPaymentTakenIn);
$customercursymbol = currency_symbol($booking->payment->currencies->customer->currency);
?>
<style type="text/css">
    body,td { color:#2f2f2f; font:12px/1.35em Arial, Helvetica, sans-serif; }
</style>
<html>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="#ffffff">

<table border=0 cellspacing=0 cellpadding=0 width="98%" style="width:98.0%; font-family:arial;">
 <tr>
  <td valign="top">
  <div align="center">
  <table border=0 cellspacing=0 cellpadding=0 width=650>
   <tr >
    <td valign=top >
    <p style="line-height:18px">

    <?php if ($this->wordpress->get_option('aj_api_ascii')==""){$csspath = $this->wordpress->get_option('aj_api_name');}else{$csspath = $this->wordpress->get_option('aj_api_ascii');} ?>
		<?php if ($this->wordpress->get_option('aj_api_site_data') == 'hb'){$filename = 'email-head-hb.gif';}else{$filename = 'email-head.gif';}?>
    <img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/<?php echo $csspath; ?>/<?php echo $filename; ?>" alt="<?php echo $this->config->item('site_name'); ?>" />

    </p>
    </td>
   </tr>
  </table>
  </div>
  <br>
  <div align="center">
  <table  border=0 cellspacing=0 cellpadding=0 width=650 style="width:487.5pt;">
   <tr>
    <td valign=top>
    <p style="line-height:18px"><strong>
    <span style="font-size:12px; color:#2F2F2F">

        <?php echo _("Bonjour");?> <?php echo $firstname;?> <?php echo $lastname;?>,

    </span>
    </strong>
    <br><br>
    <span style="font-size:12px;color:#2F2F2F">

   	<?php echo _("Merci d'avoir effectué votre réservation en ligne sur");?> <?php echo $this->config->item('site_name'); ?>. <?php echo _(" Ceci est votre confirmation de réservation ainsi que votre reçu.");?> <strong><?php echo _("Veuillez imprimer cet email et assurez vous de l'avoir avec vous lors de votre arrivée à l'établissement.");?></strong><br /><br />

		<?php if ($this->wordpress->get_option('aj_red_email') != true){ ?>

    <font color="#a90000"><?php printf(gettext("Pour tout changement ou annulation, veuillez communiquer directement avec l'établissement en envoyant un email en anglais avec le numero de confirmation (%s) à l'adresse suivante : %s"),'HB-'.$booking->bookingRef,'<a style="color:blue" href="mailto:'.$booking->property->address->email.'">'.$booking->property->address->email.'</a>');?></font>

    <?php }else{?>

    <?php printf(gettext("Pour tout changement ou annulation, veuillez communiquer directement avec l'établissement en envoyant un email en anglais avec le numero de confirmation (%s) à l'adresse suivante : %s"),'HB-'.$booking->bookingRef,$booking->property->address->email);?>

    <?php } ?>

    <br><br>

    </span>

    <h3 style="line-height:18px;border:none;">
    <span style="font-size:12px; color:#2F2F2F">

    	<?php printf(gettext("Votre numéro de réservation: #%s"),' HB-'.$booking->bookingRef);?>

    </span>
    <span style="font-size:11px;color:#2F2F2F">
      <?php printf(gettext("(validé le %s)"),strftime($this->wordpress->get_option('aj_date_format'),strtotime('today'))); ?>
    </span>
    </h3>

    <?php printf(gettext('Arrivée: %s à %s'),"<b>".date_conv($dateStart, $this->wordpress->get_option('aj_date_format'))."</b>","<b>".$book_arrival_time.":00</b>");?> &nbsp; &nbsp; <?php printf(gettext('Nombre de Nuits: %s'),"<b>".$numNights."</b>");?>
    <br>
    <br>
    <table border=1 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;background:#F8F7F5; border:solid #BEBCB7 1px;">
     <thead>
      <tr>
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Date");?>

       </span></b></p>
       </td>
      <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Chambre");?>

       </span></b></p>
       </td>
      <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Prix (lit)");?>

       </span></b></p>
       </td>
       <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Nb. Personne");?>

       </span></b></p>

       </td>

      <td align="right" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Total");?>

       </span></b></p>

       </td>

      </tr>
     </thead>

     <!-- Loop start here -->

     <?php $total =0; foreach($roomsBookedSorted as $room): ?>

      <tr>
      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

      		<?php $roomdate = new Datetime($room->date);echo date_conv($roomdate->format("Y-m-d"), $this->wordpress->get_option('aj_date_format'));?>

      </span></strong></p>
      </td>
      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">

      		<?php if(!empty($room->name_translated)){?>
					<?php echo $room->name_translated;?>
          <?php } else{?>
          <?php echo $room->name;?>
          <?php }
							if($breakfast_included == 1){
									echo '<br><span class="free-breakfast">';
									echo _('Breakfast Included');
									echo '</span>';
							}?>

      </span></p>
      </td>
      <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p align=center style="text-align:center;line-height:
      16.2pt"><span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php echo number_format(floatval($room->priceCustomerCurrency),2,'.','');?>

      </span></p>
      </td>
      <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p style="line-height:18px">
      <span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $room->beds;?>

      </span></p>
      </td>

      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px">
      <span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php $total = $total + number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.',''); echo number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.','');?>

      </span></p>
      </td>

     </tr>

     <?php endforeach; ?>

     <!-- Loop finish -->

     <tr>
      <td colspan=4 style="border:none;border-top:#dddddd 1px solid;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F"><strong>

      		<?php echo _("Total:");?>

      </strong></span></p>
      </td>
      <td style="border:none;border-top:#dddddd 1px solid;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php echo number_format($total,2,'.','');?>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F">

      		<?php
					if($isCustomCurrency){
						printf(gettext("10%% Arrhes facturé en %s:"),"(". $settlecur." ".number_format(floatval($booking->payment->currencies->gbp->amountTaken),2,'.',''). ")");
					}else{
						printf(gettext("10%% Arrhes facturé en %s:"),$booking->payment->currencyPaymentTakenIn);
					}?>

      </span></p>
      </td>
      <td style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">

       <?php
			 if($isCustomCurrency)
       {
         echo $customercursymbol;?> <?php echo number_format(floatval($booking->payment->currencies->customer->amountTaken),2,'.','');
       }else{
				 echo $settlecur." ";
				 echo number_format(floatval($booking->payment->currencies->gbp->amountTaken),2,'.','');
			 }
       ?>
      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px; font-size:13px;">
      <strong>

      		<?php printf(gettext("Montant restant à payer lors de votre arrivée (%s):"),$booking->payment->currencies->property->currency);?>

      </strong>
      </p>
      </td>
      <td style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><b>
      <span style="font-size:10.0pt;color:#2F2F2F">

      		<?php echo currency_symbol($booking->payment->currencies->property->currency);?> <?php echo number_format(floatval($booking->payment->currencies->property->payableOnArrival),2,'.','');?>

      </span></b>
      <?php if($isCustomCurrencyProperty):?>
      <span class="custom-cur">(~ <?php echo $customercursymbol;?><?php echo number_format(floatval($booking->payment->currencies->customer->payableOnArrival),2,'.','');?>)</span>
      <?php endif;?>
      </p>
      </td>
     </tr>

    </table>

    <?php if ($this->wordpress->get_option('aj_email_english') != true){ ?>

    <br>
    <strong>English Version:</strong>
    <br>
    <h3 style="line-height:18px;border:none;">
    <span style="font-size:12px; color:#2F2F2F">

    	Reservation number: # HB-<?php echo $booking->bookingRef;?>

    </span>
    <span style="font-size:11px;color:#2F2F2F">
      (validated <?php echo date('jS F Y');?>)
    </span>
    </h3>
    <?php // English Table ?>
    Arrival: <b><?php echo $dateStart;?></b> at <b><?php echo $book_arrival_time;?>:00</b> &nbsp; &nbsp; Number of Nights: <b><?php echo $numNights;?></b>
    <br>
    <br>
    <table border=1 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;background:#F8F7F5; border:solid #BEBCB7 1px;">
     <thead>
      <tr>
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo "Date";?>

       </span></b></p>
       </td>
      <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo "Room";?>

       </span></b></p>
       </td>
      <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo "Price";?>

       </span></b></p>
       </td>
       <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo "Beds";?>

       </span></b></p>

       </td>

      <td align="right" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo "Total";?>

       </span></b></p>

       </td>

      </tr>
     </thead>

     <!-- Loop start here -->

     <?php $total =0; foreach($roomsBookedSorted as $room): ?>

      <tr>
      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

      		<?php $roomdate = new Datetime($room->date);echo ($roomdate->format("Y-m-d"));?>

      </span></strong></p>
      </td>
      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">


          <?php echo $room->name;?>
					<?php
					if($breakfast_included == 1){
							echo '<br><span class="free-breakfast">';
							echo 'Breakfast Included';
							echo '</span>';
					}?>


      </span></p>
      </td>
      <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p align=center style="text-align:center;line-height:
      16.2pt"><span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php echo number_format(floatval($room->priceCustomerCurrency),2,'.','');?>

      </span></p>
      </td>
      <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p style="line-height:18px">
      <span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $room->beds;?>

      </span></p>
      </td>

      <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px">
      <span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php $total = $total + number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.',''); echo number_format((float)($room->priceCustomerCurrency)*($room->beds),2,'.','');?>

      </span></p>
      </td>

     </tr>

     <?php endforeach; ?>

     <!-- Loop finish -->

     <tr>
      <td colspan=4 style="border:none;border-top:#dddddd 1px solid;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F"><strong>

      		<?php echo "Total:";?>

      </strong></span></p>
      </td>
      <td style="border:none;border-top:#dddddd 1px solid;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">

      		<?php echo $customercursymbol;?> <?php echo number_format($total,2,'.','');?>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F">

			<?php
			if($isCustomCurrency){?>
				Amount already paid (<?php echo $settlecur." ".number_format(floatval($booking->payment->currencies->gbp->amountTaken),2,'.','');?>) :
			<?php }else{?>
				Amount already paid <?php echo $booking->payment->currencyPaymentTakenIn;?> :
			<?php }?>

      </span></p>
      </td>
      <td style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">

			<?php
			 if($isCustomCurrency)
       {
         echo $customercursymbol;?> <?php echo number_format(floatval($booking->payment->currencies->customer->amountTaken),2,'.','');
       }else{
				 echo $settlecur." ";
				 echo number_format(floatval($booking->payment->currencies->gbp->amountTaken),2,'.','');
			 }
       ?>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px; font-size:13px;">
      <strong>

      		Balance to be paid upon arrival (<?php echo $booking->payment->currencies->property->currency;?>):

      </strong>
      </p>
      </td>
      <td style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><b>
      <span style="font-size:10.0pt;color:#2F2F2F">

      		<?php echo currency_symbol($booking->payment->currencies->property->currency);?> <?php echo number_format(floatval($booking->payment->currencies->property->payableOnArrival),2,'.','');?>

      </span></b>
      <?php if($isCustomCurrencyProperty):?>
      <span class="custom-cur">(~ <?php echo $customercursymbol;?> <?php echo number_format(floatval($booking->payment->currencies->customer->payableOnArrival),2,'.','');?>)</span>
      <?php endif;?>
      </p>
      </td>
     </tr>

    </table>

    <?php }?>

    <br>
		<?php if (!empty($important_info->original)){?>
		<table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Informations Importantes");?> :

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">
       <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

       		<?php
					 if(!empty($important_info->translation))
					 {
						 echo nl2p($important_info->translation,false,true).'<br />';
						 echo '<p><strong>'._("Version Originale").'</strong></p>';
						 echo nl2p($important_info->original,false,true);
					 }
					 else
					 {
						 echo nl2p($important_info->original,false,true);
					 }
      	 ?>
					
       </span></p>

      </td>

     </tr>

    </table>

   	<br>
		<?php }?>
    <table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Information sur l'établissement");?>:

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">

       	<table border="0" cellpadding="0" cellspacing="0" width="100%">

        <tr>
        	<td colspan="2" style="padding:0 0 10px 0;">
            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Nom de l'établissement");?>:</strong><br>

                        <?php echo $booking->property->name;?><br>
                        <?php echo site_url($this->Db_links->get_link("info").'/'.url_title($booking->property->name).'/'.$propertyNumber);?>

       				</span>
                </p>
            </td>

        </tr>


        <tr>
        	<td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Phone number")?>:</strong><br>

                        <?php echo $booking->property->address->tel;?>

       				</span>
                </p>

            </td>

            <td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Adresse");?>:</strong><br>
                    <span><?php echo $booking->property->address->street1;?></span>
                    <span><?php echo $booking->property->address->street2;?></span>
                    <span><?php echo $booking->property->address->street3;?></span>
                    <span><?php echo $booking->property->address->city;?>, <?php echo $booking->property->address->state;?></span>
                    <span><?php echo $booking->property->address->country;?></span>
                    <span><?php echo $booking->property->address->zip;?></span>

                  </span>
              </p>

            </td>


        </tr>

        <tr>
        	<td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Email");?>:</strong><br>

                        <a href="mailto:<?php echo $booking->property->address->email;?>"><?php echo $booking->property->address->email;?></a>

       				</span>
                </p>

            </td>

            <td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Fax")?>:</strong><br>

                        <?php echo $booking->property->address->fax;?>

       				</span>
                </p>

            </td>

        </tr>

     	</table>

      </td>

     </tr>

    </table>

    <br>

    <table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Directions");?> :

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">
       <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

       		<?php if(!empty($booking->property->address->directions_translated)){?>
						<?php echo $booking->property->address->directions_translated;?>	<br />
            <strong><?php echo _("Version Originale:");?></strong><br />
            <?php echo nl2p($booking->property->address->directions,false,true);?>
          <?php } else{?>
            <?php echo nl2p($booking->property->address->directions,false,true);?>
          <?php }?>

       </span></p>

      </td>

     </tr>

    </table>

   	<br>


		<?php if(!empty($booking->property->importantInfo->cancellationPolicy)){?>
		<table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Cancellation Policy");?> :

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">
       <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

       		<?php if(!empty($booking->property->importantInfo->cancellationPolicy_translated)){?>
						<?php echo $booking->property->importantInfo->cancellationPolicy_translated;?>	<br />
            <strong><?php echo _("Version Originale:");?></strong><br />
            <?php echo nl2p($booking->property->importantInfo->cancellationPolicy,false,true);?>
          <?php } else{?>
            <?php echo nl2p($booking->property->importantInfo->cancellationPolicy,false,true);?>
          <?php }?>

       </span></p>

      </td>

     </tr>

    </table>

   	<br>
		<?php }?>

		<?php if(!empty($booking->property->importantInfo->taxes)){?>
		<table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Taxes");?> :

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">
       <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

       		<?php if(!empty($booking->property->importantInfo->taxes_translated)){?>
						<?php echo $booking->property->importantInfo->taxes_translated;?>	<br />
            <strong><?php echo _("Version Originale:");?></strong><br />
            <?php echo nl2p($booking->property->importantInfo->taxes,false,true);?>
          <?php } else{?>
            <?php echo nl2p($booking->property->importantInfo->taxes,false,true);?>
          <?php }?>

       </span></p>

      </td>

     </tr>

    </table>

   	<br>
		<?php }?>

		<?php if(!empty($booking->property->importantInfo->extraInfo)){?>
		<table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Extra Information");?> :

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">
       <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

       		<?php if(!empty($booking->property->importantInfo->extraInfo_translated)){?>
						<?php echo $booking->property->importantInfo->extraInfo_translated;?>	<br />
            <strong><?php echo _("Version Originale:");?></strong><br />
            <?php echo nl2p($booking->property->importantInfo->extraInfo,false,true);?>
          <?php } else{?>
            <?php echo nl2p($booking->property->importantInfo->extraInfo,false,true);?>
          <?php }?>

       </span></p>

      </td>

     </tr>

    </table>

   	<br>
		<?php }?>

    <table border=0 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;">
     <thead>
      <tr >
       <td style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
       <p  style="line-height:18px"><b><span style="font-size:
       12px;color:#ffffff">

       		<?php echo _("Conditions");?>:

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">


        <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

				<?php echo _("Vous devez payer pour votre chambre à votre arrivée. Veuillez, s'il vous plaît, vous assurer que votre carte de crédit est valide. Les cartes sont vérifiées la veille des arrivées et le fait de réserver à l'aide d'une carte invalide annulera votre réservation. Si vous avez réservé à l'aide de votre carte bancaire ou si vous doutez de la validité de votre carte de crédit, veuillez contacter l'établissement au moins 24 heures avant votre arrivée afin de confirmer votre réservation.");?>   <br>
        <ul>
          <?php if ($this->wordpress->get_option('aj_red_email') != true){ ?>

            <li><?php echo _("Le montant restant (après déduction du dépôt) est dû à votre arrivée.");?> </li>
            <li><font color="#a90000"><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></font></li>
            <li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
            <li><font color="#a90000"><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></font></li>
            <li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
            <li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
            <li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
            <li><?php echo _("Pour toute question, consultez notre");?> <?php /*?><a href="<?php echo $this->wordpress->get_option('aj_page_faq');?>"><?php */?><?php echo _("Centre d'aide en ligne");?><?php /*?></a><?php */?>.</li>

          <?php }else{?>

            <li><?php echo _("Le montant restant (après déduction du dépôt) est dû à votre arrivée.");?> </li>
            <li><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></li>
            <li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
            <li><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></li>
            <li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
            <li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
            <li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
            <li><?php echo _("Pour toute question, consultez notre");?> <?php /*?><a href="<?php echo $this->wordpress->get_option('aj_page_faq');?>"><?php */?><?php echo _("Centre d'aide en ligne");?><?php /*?></a><?php */?>.</li>

          <?php }?>
        </ul>

       </span></p>



      </td>

     </tr>

    </table>

   	<br>
    <p style="line-height:18px">
        <span style="font-size:12px;color:#2F2F2F">
            <?php echo _("Merci et bon séjour");?>,<br>
            <strong>
                <?php echo $this->config->item('site_name');?>
            </strong>
    	</span>
    </p>
    </td>
   </tr>
  </table>
  </div>
  </td>
 </tr>
</table>

<p>&nbsp;</p>

</body>
</html>
