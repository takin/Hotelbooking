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
//Ex. numero de confirmation: $booking->CustomerReference->value
/*
 * $booking->
SimpleXMLElement Object
(
    [@attributes] => Array
        (
            [size] => 11
        )

    [BookingRequestResult] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [message] => Booking Confirmed
            [detail] => Your booking is now confirmed! We advise you to print and keep this confirmation. You will need to present this confirmation upon arrival at Colombe Hotel.
        )

    [CustomerReference] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [note] => This is your Customer Booking Reference. Please keep a record of it.
            [value] => 30462-TEST
        )

    [RoomDetails] => Array
        (
            [0] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [size] => 7
                        )

                    [date] => 2010-03-21
                    [beds] => 1
                    [roomNumber] => Sin1-0
                    [roomType] => 1 Bed Private Ensuite (TEST)
                    [price] => 70.00
                    [priceUSD] => 100.60
                    [priceSettle] => 100.60
                )

            [1] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [size] => 7
                        )

                    [date] => 2010-03-22
                    [beds] => 1
                    [roomNumber] => Sin1-0
                    [roomType] => 1 Bed Private Ensuite (TEST)
                    [price] => 70.00
                    [priceUSD] => 100.60
                    [priceSettle] => 100.60
                )

            [2] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [size] => 7
                        )

                    [date] => 2010-03-23
                    [beds] => 1
                    [roomNumber] => Sin1-0
                    [roomType] => 1 Bed Private Ensuite (TEST)
                    [price] => 70.00
                    [priceUSD] => 100.60
                    [priceSettle] => 100.60
                )

            [3] => SimpleXMLElement Object
                (
                    [@attributes] => Array
                        (
                            [size] => 7
                        )

                    [date] => 2010-03-24
                    [beds] => 1
                    [roomNumber] => Sin1-0
                    [roomType] => 1 Bed Private Ensuite (TEST)
                    [price] => 70.00
                    [priceUSD] => 100.60
                    [priceSettle] => 100.60
                )

        )

    [AmountCharged] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [note] => Amount Charged to your card in USD
            [value] => 42.24
        )

    [ChargedCurrency] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [note] => Currency used to charge your card
            [value] => USD
        )

    [USDAmountDue] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [note] => Amount due on arrival in USD
            [value] => 362.16
        )

    [PropertyAmountDue] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 2
                )

            [note] => Amount due on arrival in EUR
            [value] => 252.00
        )

    [PropertyDetails] => SimpleXMLElement Object
        (
            [@attributes] => Array
                (
                    [size] => 10
                )

            [propertyName] => Colombe Hotel
            [address] => 6 Bd Zabour Larbi Hai Khaldia
            [postCode] => , 31000
            [city] => Oran
            [phone] => +213 41 466162
            [fax] => +213 41 453479
            [email] => hotelcolombe@yahoo.fr
            [currency] => EUR
            [depositPercent] => 10.000
            [directions] => Close to bus station , taxi station and private rent a car service
        )

)

 */
?>
<?php $cur = currency_symbol($booking->ChargedCurrency->value);?>
<style type="text/css">
    body,td { color:#2f2f2f; font:12px/1.35em Arial, Helvetica, sans-serif; }
</style>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
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

    <font color="#a90000"><?php printf(gettext("Pour tout changement ou annulation, veuillez communiquer directement avec l'établissement en envoyant un email en anglais avec le numero de confirmation (%s) à l'adresse suivante : %s"),'HW- '.$booking->CustomerReference->value,'<a style="color:blue" href="mailto:'.$booking->PropertyDetails->email.'">'.$booking->PropertyDetails->email.'</a>');?></font>

    <?php }else{?>

    <?php printf(gettext("Pour tout changement ou annulation, veuillez communiquer directement avec l'établissement en envoyant un email en anglais avec le numero de confirmation (%s) à l'adresse suivante : %s"),'HW-'.$booking->CustomerReference->value,$booking->PropertyDetails->email);?>

    <?php } ?>

    <br><br>

    </span>

    <h3 style="line-height:18px;border:none;">
    <span style="font-size:12px; color:#2F2F2F">

    	<?php printf(gettext("Votre numéro de réservation: #%s"),' HW-'.$booking->CustomerReference->value);?>

    </span>
    <span style="font-size:11px;color:#2F2F2F">
      <?php printf(gettext("(validé le %s)"),strftime($this->wordpress->get_option('aj_date_format'),strtotime('today'))); ?>
    </span>
    </h3>

    <?php printf(gettext('Arrivée: %s à %s'),"<b>".date_conv($dateStart_calculated, $this->wordpress->get_option('aj_date_format'))."</b>","<b>".$book_arrival_time.":00</b>");?> &nbsp; &nbsp; <?php printf(gettext('Nombre de Nuits: %s'),"<b>".$numNights_calculated."</b>");?>
    <br>
    <br>
    <table border=1 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;background:#F8F7F5; border:solid #BEBCB7 1px;">
     <!-- Loop start here -->

     <?php
     $total = 0;
     $dormroomcount = 0;
     foreach($booking->RoomDetails as $room)
     {
       if(substr_count($room->roomType,"Private") <= 0)
       {
         if($dormroomcount == 0)
         {
           ?>
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

                <?php echo _("Chambres partagées - Dortoirs");?>

             </span></b></p>
             </td>
            <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p  style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo _("Prix");?>

             </span></b></p>
             </td>
             <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p  style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo _("Personnes");?>

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
           <?php
         }
         $dormroomcount++;
         ?>
          <tr>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

          		<?php echo date_conv($room->date, $this->wordpress->get_option('aj_date_format'));?>

          </span></strong></p>
          </td>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">

          		<?php if(!empty($room->roomTypeDescriptionTranslated)){?>
    					<?php echo $room->roomTypeDescriptionTranslated;?>
              <?php } else{?>
              <?php echo $room->roomTypeDescription;?>
              <?php }?>

          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p align=center style="text-align:center;line-height:
          16.2pt"><span style="font-size:12px;
          color:#2F2F2F">

          		<?php echo $room->priceSettle;?> <?php echo $cur;?>

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

          		<?php $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.',''); echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>

         </tr>

         <?php
       }
     }

     $privateroomcount = 0;
     foreach($booking->RoomDetails as $room)
     {
       if(substr_count($room->roomType,"Private") > 0)
       {
         if($privateroomcount == 0)
         {
           ?>
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

                <?php echo _("Chambres privées");?>

             </span></b></p>
             </td>
            <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p  style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo _("Prix");?>

             </span></b></p>
             </td>
             <td align="center" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p  style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo _("Rooms");?>

             </span></b></p>

             </td>

            <td align="right" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo _("Total");?>

             </span></b></p>

             </td>

            </tr>
           <?php
         }
         $privateroomcount++;
         ?>
          <tr>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

          		<?php echo date_conv($room->date, $this->wordpress->get_option('aj_date_format'));?>

          </span></strong></p>
          </td>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">

          		<?php if(!empty($room->roomTypeDescriptionTranslated)){?>
    					<?php echo $room->roomTypeDescriptionTranslated;?>
              <?php } else{?>
              <?php echo $room->roomTypeDescription;?>
              <?php }?>

          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p align=center style="text-align:center;line-height:
          16.2pt"><span style="font-size:12px;
          color:#2F2F2F">

          		<?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px">
          <span style="font-size:12px;
          color:#2F2F2F">

          		1<?php //echo $room->beds;?>

          </span></p>
          </td>

          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  align=right style="text-align:right;line-height:18px">
          <span style="font-size:12px;
          color:#2F2F2F">

          		<?php $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.',''); echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>

         </tr>

         <?php
       }
     }
     ?>

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

      		<?php echo number_format($total,2,'.','');?> <?php echo $cur;?>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F">

                <?php echo _('Amount already paid:');?>

      </span></p>
      </td>
      <td style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">


       <b><?php echo $booking->AmountCharged->value;?> <?php echo $cur;?></b>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px; font-size:13px;">
      <strong>

      		<?php printf(gettext("Montant restant à payer lors de votre arrivée (%s):"),$booking->PropertyDetails->currency);?>

      </strong>
      </p>
      </td>
      <td style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><b>
      <span style="font-size:10.0pt;color:#2F2F2F">

      		<?php echo $booking->PropertyAmountDue->value;?> <?php echo currency_symbol($booking->PropertyDetails->currency);?>

      </span></b>
      <?php if($isCustomCurrency):?>
      <span class="custom-cur">(~<?php echo $booking->$bookAmountDueField->value;?><?php echo $bookCurrency;?>)</span>
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

    	Reservation number: # HW-<?php echo $booking->CustomerReference->value;?>

    </span>
    <span style="font-size:11px;color:#2F2F2F">
      (validated <?php echo date('jS F Y');?>)
    </span>
    </h3>
    <?php // English Table ?>
    Arrival: <b><?php echo $dateStart_calculated;?></b> at <b><?php echo $book_arrival_time;?>:00</b> &nbsp; &nbsp; Number of Nights: <b><?php echo $numNights_calculated;?></b>
    <br>
    <br>
    <table border=1 cellspacing=0 cellpadding=0 width="100%" style="width:100.0%;background:#F8F7F5; border:solid #BEBCB7 1px;">

     <!-- Loop start here -->

     <?php
     $total = 0;
     $dormroomcount = 0;

     foreach($booking->RoomDetails as $room)
     {
       if(substr_count($room->roomType,"Private") <= 0)
       {
         if($dormroomcount == 0)
         {
           ?>
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

                <?php echo "Shared Rooms - Dorms";?>

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
           <?php
         }
         $dormroomcount++;
         ?>

         <tr>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

          		<?php echo ($room->date);?>

          </span></strong></p>
          </td>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">


              <?php echo $room->roomTypeDescription;?>


          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p align=center style="text-align:center;line-height:
          16.2pt"><span style="font-size:12px;
          color:#2F2F2F">

          		<?php echo $room->priceSettle;?> <?php echo $cur;?>

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

          		<?php $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.',''); echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>

         </tr>

         <?php
       }
     }
     $privateroomcount = 0;

     foreach($booking->RoomDetails as $room)
     {
       if(substr_count($room->roomType,"Private") > 0)
       {
         if($privateroomcount == 0)
         {
           ?>
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

                <?php echo "Private rooms";?>

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

                <?php echo "Rooms";?>

             </span></b></p>

             </td>

            <td align="right" style="border:none;background:#464646;padding:2.25pt 6.75pt 2.25pt 6.75pt">
             <p style="line-height:18px"><b><span style="font-size:
             12px;color:#ffffff">

                <?php echo "Total";?>

             </span></b></p>

             </td>

            </tr>
           <?php
         }
         $privateroomcount++;
         ?>

         <tr>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  style="line-height:18px"><strong><span style="font-size:12px;color:#2F2F2F">

          		<?php echo ($room->date);?>

          </span></strong></p>
          </td>
          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px"><span style="font-size: 12px;color:#2F2F2F">


              <?php echo $room->roomTypeDescription;?>


          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p align=center style="text-align:center;line-height:
          16.2pt"><span style="font-size:12px;
          color:#2F2F2F">

          		<?php echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>
          <td align="center" valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p style="line-height:18px">
          <span style="font-size:12px;
          color:#2F2F2F">

          		1<?php // echo $room->beds;?>

          </span></p>
          </td>

          <td valign=top style="border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
          <p  align=right style="text-align:right;line-height:18px">
          <span style="font-size:12px;
          color:#2F2F2F">

          		<?php $total = $total + number_format((float)($room->priceSettle)*($room->beds),2,'.',''); echo number_format((float)($room->priceSettle)*($room->beds),2,'.','');?> <?php echo $cur;?>

          </span></p>
          </td>

         </tr>

         <?php
       }
     }
     ?>

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

      		<?php echo number_format($total,2,'.','');?> <?php echo $cur;?>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span
      style="font-size:12px;color:#2F2F2F">

      Amount already paid:

      </span></p>
      </td>
      <td style="background:#eaeff1;border:none;padding:2.25pt 6.75pt 2.25pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><span style="font-size:12px;
      color:#2F2F2F">


       <b><?php echo $booking->AmountCharged->value;?> <?php echo $cur;?></b>

      </span></p>
      </td>
     </tr>



     <tr>
      <td colspan=4 style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p align=right style="text-align:right;line-height:18px; font-size:13px;">
      <strong>

      		Balance to be paid upon arrival (<?php echo $booking->PropertyDetails->currency;?>):

      </strong>
      </p>
      </td>
      <td style="border:none;background:#DEE5E8;padding:4.5pt 6.75pt 4.5pt 6.75pt">
      <p  align=right style="text-align:right;line-height:18px"><b>
      <span style="font-size:10.0pt;color:#2F2F2F">

      		<?php echo $booking->PropertyAmountDue->value;?> <?php echo currency_symbol($booking->PropertyDetails->currency);?>

      </span></b>
      <?php if($isCustomCurrency):?>
      <span class="custom-cur">(~<?php echo $booking->$bookAmountDueField->value;?><?php echo $bookCurrency;?>)</span>
      <?php endif;?>
      </p>
      </td>
     </tr>

    </table>

    <?php }?>

    <br>

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

                        <?php echo $booking->PropertyDetails->propertyName;?><br>
                        <?php echo site_url($this->Db_links->get_link("info").'/'.url_title($propertyName).'/'.$propertyNumber);?>

       				</span>
                </p>
            </td>

        </tr>


        <tr>
        	<td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Phone number")?>:</strong><br>

                        <?php echo $booking->PropertyDetails->phone;?>

       				</span>
                </p>

            </td>

            <td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Adresse");?>:</strong><br>
                    <span><?php echo $booking->PropertyDetails->address;?></span>
                    <span><?php echo $booking->PropertyDetails->city;?>, <?php echo $booking->PropertyDetails->state;?></span>
                    <span><?php echo $booking->PropertyDetails->postCode;?></span>

                  </span>
              </p>

            </td>


        </tr>

        <tr>
        	<td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Email");?>:</strong><br>

                        <a href="mailto:<?php echo $booking->PropertyDetails->email;?>"><?php echo $booking->PropertyDetails->email;?></a>

       				</span>
                </p>

            </td>

            <td style="padding:0 0 10px 0;">

            	<p style="line-height:12pt">
                	<span style="font-size:12px;color:#2F2F2F">
              			<strong><?php echo _("Fax")?>:</strong><br>

                        <?php echo $booking->PropertyDetails->fax;?>

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

       		<?php if(!empty($booking->PropertyDetails->directionsTranslated)){?>
						<?php echo $booking->PropertyDetails->directionsTranslated;?>	<br />
            <strong><?php echo _("Version Originale:");?></strong><br />
            <?php echo nl2p($booking->PropertyDetails->directions,false,true);?>
          <?php } else{?>
            <?php echo nl2p($booking->PropertyDetails->directions,false,true);?>
          <?php }?>



       </span></p>

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

       		<?php echo _("Conditions");?>:

       </span></b></p>
       </td>

       </tr>
      </thead>
      <tr>
       <td valign=top style="border:solid #BEBCB7 1.0pt;mso-border-alt:solid #BEBCB7 .75pt; mso-border-top-alt:solid #BEBCB7 .25pt;background:#F8F7F5;padding:5.25pt 6.75pt 6.75pt 6.75pt">

        <?php if (!empty($booking->TermsAndConditions->value)){?>

        <h3 style="font-size:12px;color:#2F2F2F"><?php echo _("Conditions");?> - <?php echo $booking->PropertyDetails->propertyName;?></h3>

        <?php if (!empty($booking->TermsAndConditionsTranslated->value)){?>
        <div style="font-size:11px;color:#2F2F2F"><?php echo nl2p(var_check($booking->TermsAndConditionsTranslated->value,""),false,true)?></div>

        <p style="font-size:11px;color:#2F2F2F"><strong><?php echo _("Version Originale");?></strong></p>
        <?php }?>

        <div style="font-size:11px;color:#2F2F2F"><?php echo nl2p(var_check($booking->TermsAndConditions->value,""),false,true)?></div>

        <h3 style="font-size:12px;color:#2F2F2F"><?php echo _("Conditions");?> - <?php echo $this->config->item('site_name'); ?></h3>

        <p style="line-height:12pt"><span style="font-size:11px;color:#2F2F2F">

        <ul>
          <?php if ($this->wordpress->get_option('aj_red_email') != true){ ?>
            <li><font color="#a90000"><?php echo _("Les annulations doivent être effectuées directement auprès de l'établissement.");?></font></li>
            <li><?php echo _("Si vous ne vous présentez pas à la date prévue et que vous n'avez pas annulé votre réservation, votre carte de crédit sera facturée pour le montant total de votre première nuit de réservation.");?> </li>
            <li><font color="#a90000"><?php echo _("Pour effectuer toute modification à une réservation, veuillez contacter directement l'établissement.");?></font></li>
            <li><?php echo _("Si votre carte de crédit devient invalide ou que des modifications sont apportées à votre compte avant votre arrivée, veuillez contacter directement l'établissement afin de prendre d'autres dispositions.");?> </li>
            <li><?php echo _("En cas d'annulation de votre réservation, le dépôt et les frais de réservation ne peuvent pas être pas remboursés.");?> </li>
            <li><?php echo _("Veuillez s'il vous plaît lire attentivement nos termes et conditions.");?> </li>
            <li><?php echo _("Pour toute question, consultez notre");?> <?php /*?><a href="<?php echo $this->wordpress->get_option('aj_page_faq');?>"><?php */?><?php echo _("Centre d'aide en ligne");?><?php /*?></a><?php */?>.</li>

          <?php }else{?>


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

        <?php }else{?>

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

       <?php }?>

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
