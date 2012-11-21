<script type="text/javascript">
$('a.show-room-info').click(function() {
	return false;
});
$('a.show-room-info').mouseover(function() {
	$(this).next().show();
});
$('a.show-room-info').mouseleave(function() {
	$(this).next().hide();
});
</script>
<?php
$min_price_shared = 0;
$min_price_private = 0;

$max_guest_per_unity_enable = false;
if(!empty($property_rooms["sharedRooms"]) &&
!empty($property_rooms["sharedRooms"][0]['max_guest_per_unity']))
{
  $max_guest_per_unity_enable = true;
}
?>

<div class="avail-wrap">
<table border="0" cellpadding="0" cellspacing="0">
<tbody>
  <tr>
    <th class="title">
      <div class="room-type">
      <a class="show-room-info" href="#"><?php echo _('Chambres partagées - Dortoirs'); ?></a>
				<div class="room-type-info">
					<h5><?php echo _('Chambres partagées - Dortoirs'); ?></h5>
					<p><?php echo _('Price per person (Dorm shared with others).'); ?> <?php echo _('You must share the room (unless you purchase all the beds in the dorm).'); ?></p>
					<span class="room-info-arrow"></span>

				</div>
			</div>
		</th>
		
      <th>&nbsp;<?php //echo _('Max guests per dorm');?></th>
      
    <?php
    $date = clone $dateStart;

    for($i=0;$i<$numNights;$i++)
    {
      echo "<th>";
			echo my_mb_ucfirst(mb_substr(strftime("%a",$date->format('U')),0,2, 'UTF-8'));
      echo strftime("<br />%e",$date->format('U'));
      $date->modify("+1 day");
      echo "</th>";
    }
    ?>
    <th class="last"><?php echo _('Beds (max)'); ?></th>
  </tr>

<?php

if(!empty($property_rooms["sharedRooms"]))
{
  foreach($property_rooms["sharedRooms"] as $room)
  {
    ?>
    <tr>
      <td class="first">
        <?php
        if(!empty($room['descriptionTranslated']))
        {
          echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$room['description'].'">'.$room['descriptionTranslated'].'</span>';
        }
        else
        {
          echo $room['description'];
        }
        if($breakfast_included == 1){
					echo '<span class="free-breakfast">';
					echo _('Breakfast Included');
					echo '</span>';
				}?>
				
      </td>      
				<td align="center" title="<?php echo _('Bed in a dorm. 1 person per bed maximum')?>"><span class="nbpeople-table icon-nbpeople nbpeople-1">1</span></td>
				
        <?php
      //dates columns
      $date = clone $dateStart;

      for($i=0;$i<$numNights;$i++)
      {
        ?>
        <td align="center">
          <?php
          if(!empty($room["availableDates"][$date->format("Y-m-d")]))
          {
            if ($numNights > 5)
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].round($room["availableDates"][$date->format("Y-m-d")]["price"],0);
              ?>
              </span>
              <?php
            }
            else
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].$room["availableDates"][$date->format("Y-m-d")]["price"];
              ?>
              </span>
              <?php
            }
						if($min_price_shared == 0){
							$min_price_shared = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}elseif($min_price_shared > $room["availableDates"][$date->format("Y-m-d")]["price"]){
							$min_price_shared = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}
						$currency_formin = $room["currency"];
          }
          else
          {
            ?>
            <span class="na-book price">0</span>
            <?php
          }
          ?>
        </td>
        <?php

        $date->modify("+1 day");
      }
      ?>
      <td align="center">
      <?php echo $room["availableBeds"]?>
      </td>
    </tr>
    <?php
  }
}
else
{

  ?>
  <tr>
  	<td class="first" colspan="<?php echo $numNights+3;?>">
  	<?php echo _("No dorms available");?>
  	</td>
  </tr>
  <?php
}
?>
<tr>
  <th class="title">
    <div class="room-type">
    	<a class="show-room-info" href="#"><?php echo _('Chambres privées'); ?></a>
			<div class="room-type-info">
				<h5><?php echo _('Chambres privées'); ?></h5>
				<p><?php echo _('Price per room (not per person).'); ?> <?php echo _('You must pay for the whole private room, even if you do not need all the beds. The room cannot be shared.'); ?></p>
				<span class="room-info-arrow"></span>

			</div>
		</div>
	</th>
	<th>&nbsp;<?php //echo _('Max guests per room');?></th>

  <?php
  $date = clone $dateStart;

  for($i=0;$i<$numNights;$i++)
  {
    echo "<th>";
    echo my_mb_ucfirst(mb_substr(strftime("%a",$date->format('U')),0,2, 'UTF-8'));
    echo strftime("<br />%e",$date->format('U'));
    $date->modify("+1 day");
    echo "</th>";
  }
  ?>
  <th class="last"><?php echo _('Rooms (max)'); ?></th>
</tr>
<?php
if(!empty($property_rooms["privateRooms"]))
{
  foreach($property_rooms["privateRooms"] as $room)
  {
    ?>
    <tr>
      <td class="first">
        <?php
        if(!empty($room['descriptionTranslated']))
        {
          echo '<span class="tooltip" title="'._("VERSION ORIGINALE :").' '.$room['description'].'">'.$room['descriptionTranslated'].'</span>';
        }
        else
        {
          echo $room['description'];
        }
        if($breakfast_included == 1){
					echo '<span class="free-breakfast">';
					echo _('Breakfast Included');
					echo '</span>';
				}?>
      </td>
			<td align="center" title="<?php echo _('Maximum number of guests in the room')?>" ><span class="nbpeople-table icon-nbpeople nbpeople-<?php echo $room['max_guest_per_unity'];?>"><?php echo $room['max_guest_per_unity'];?> x</span></td>
        <?php
      //dates columns
      $date = clone $dateStart;

      for($i=0;$i<$numNights;$i++)
      {
        ?>
        <td align="center">
          <?php
          if(!empty($room["availableDates"][$date->format("Y-m-d")]))
          {
            if ($numNights > 5)
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].round($room["availableDates"][$date->format("Y-m-d")]["price"],0);
              ?>
              </span>
              <?php
            }
            else
            {
              ?>
              <span class="price">
              <?php
              echo $room["currency"].$room["availableDates"][$date->format("Y-m-d")]["price"];
              ?>
              </span>
              <?php
            }
						if($min_price_private == 0){
							$min_price_private = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}elseif($min_price_private > $room["availableDates"][$date->format("Y-m-d")]["price"]){
							$min_price_private = $room["availableDates"][$date->format("Y-m-d")]["price"];
						}
						$currency_formin = $room["currency"];
          }
          else
          {
            ?>
            <span class="na-book price">0</span>
            <?php
          }
          ?>
        </td>
        <?php

        $date->modify("+1 day");
      }
      ?>
      <td align="center">
      <?php echo $room["availableRooms"]?>
      </td>
    </tr>
    <?php
  }
}
else
{
  ?>
  <tr>
  	<td class="first" colspan="<?php echo $numNights+3;?>">
  	<?php echo _("No private room available");?>
  	</td>
  </tr>
  <?php
}
?>
</tbody>
</table>
<div class="bottom-table-city group">
	<span><?php echo _('Best price. We guarantee it.')?></span>
	<span><?php echo _('It only takes 2 minutes')?></span>
</div>
</div>