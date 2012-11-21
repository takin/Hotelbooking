<br /><br />
<label for="<?php echo $quote_id;?>_property_number">Property number</label>
<input type="text" name="<?php echo $quote_id;?>_property_number" size="2"/>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_breakfast" id="<?php echo $quote_id;?>_inc_breakfast" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_breakfast">Breakfast</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_lunch" id="<?php echo $quote_id;?>_inc_lunch" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_lunch">Lunch</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_lunch_pack" id="<?php echo $quote_id;?>_inc_lunch_pack" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_lunch_pack">Lunch Pack</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_dinner" id="<?php echo $quote_id;?>_inc_dinner" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_dinner">Dinner</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_linen" id="<?php echo $quote_id;?>_inc_linen" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_linen">linen</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_towels" id="<?php echo $quote_id;?>_inc_towels" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_towels">towels</label>
<br />
<input type="checkbox" name="<?php echo $quote_id;?>_inc_luggage_storage" id="<?php echo $quote_id;?>_inc_luggage_storage" />
<label class="checkbox" for="<?php echo $quote_id;?>_inc_luggage_storage">Luggage Storage</label>
<br />
<table>
<thead>
<tr>

<td>Room desc</td>
<td>
<select name="<?php echo $quote_id;?>_col2_name">
<option value="beds">beds</option>
<option value="rooms">rooms</option>
<option value="people" selected="selected">people</option>
<option value="nights">nights</option>
</select>
</td>
<td><select name="<?php echo $quote_id;?>_col3_name">
<option value="beds">beds</option>
<option value="rooms">rooms</option>
<option value="people">people</option>
<option value="nights"  selected="selected">nights</option>
</select></td>
<td>Price (main)</td>
<td>Price (sec)</td>
</tr>
</thead>
<tbody>
<?php
for ($row=0;$row<6;$row++)
{
  $this->load->view('restricted/admin/group_quote_room_row', array('quote_id' => $quote_id));
}
?>
</tbody>
</table>
<br />
<label for="<?php echo $quote_id;?>_total_custom">Total (main)</label>
<input type="text" name="<?php echo $quote_id;?>_total_custom" size="4"/>
<?php
$this->Db_currency->select_currency($quote_id."_total_custom_cur",$quote_id."_total_custom_cur","EUR");
?>
<br />
<label for="<?php echo $quote_id;?>_total_prop">Total (sec)</label>
<input type="text" name="<?php echo $quote_id;?>_total_prop" size="4"/>
<?php
$this->Db_currency->select_currency($quote_id."_total_prop_cur",$quote_id."_total_prop_cur","GBP");
?>
<br /><br />
<label for="<?php echo $quote_id;?>_custom_down_pay">Down payment (main)</label>
<input type="text" name="<?php echo $quote_id;?>_custom_down_pay" size="4"/>
<br />
<label for="<?php echo $quote_id;?>_prop_down_pay">Down payment (sec)</label>
<input type="text" name="<?php echo $quote_id;?>_prop_down_pay" size="4"/>
<br />
<label for="balance_d">Balance payment date (will change to nb days before arrival date)</label>
<?php
select_day($quote_id."_balance_d",$quote_id."_balance_d");
select_month_year($quote_id."_balance_my",$quote_id."_balance_my","",0,24);
?>