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
    <img style="margin-top:20px;" border=0 src="<?php echo base_url();?>images/<?php echo $csspath; ?>/email-head.gif" alt="<?php echo $this->config->item('site_name'); ?>" />
    
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
    	
        Hello <?php echo $propertyName   ?>,
        
    </span>
    </strong>
    <br><br>
    <span style="font-size:12px;color:#2F2F2F">
    
   	A reservation has been made on <?php echo $this->config->item('site_name'); ?> in partnership with Hostelworld.		
      
    <br><br>   
		
		<?php echo site_url($this->Db_links->get_link("info").'/'.url_title($propertyName).'/'.$propertyNumber);?>
		
    <br><br>
		
    </span>
		   
    <h3 style="line-height:18px;border:none;">
    <span style="font-size:12px; color:#2F2F2F">
    
    	Reservation number: # HW-<?php echo $booking->CustomerReference->value;?>
    
    </span>
    <span style="font-size:11px;color:#2F2F2F"> 
      (validated <?php echo date('jS F Y');?>)
    </span>
    </h3>
		
		Guest Name: <?php echo $firstname;?> <?php echo $lastname;?>
    <br>
    <br>
		Guest email address: <?php echo $book_email_address;?>
		<br>
    <br>
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
             
                <?php echo "Persons";?>
             
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
          
          		
              <?php echo $room->roomType;?>
              
                
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
          
          		
              <?php echo $room->roomType;?>
              
                
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
      
      10% Deposit + service charges already paid for our services billed in <strong><?php echo $booking->ChargedCurrency->value;?></strong>:
      
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
      
      		Maximum Balance to be charged upon arrival (<?php echo $booking->PropertyDetails->currency;?>):
      
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
        
    <br>
    <br>
		<p style="line-height:18px">In case the guest wants to modify or cancel this reservation, we have instructed the guest to contact you directly. No need for the guest or yourself to contact <?php echo $this->config->item('site_name');?>.
		
		<br>
		<br>
		
		If there is any problem with this reservation, please contact Hostelworld right away at the following address: customerservice@hostelworld.com.
   
    <p style="line-height:18px">
        <span style="font-size:12px;color:#2F2F2F">
            Thank you,<br>
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
