Hello,<br />
<br />
This is Chris at <?php echo $this->config->item('admin_booking_email');?><br />
Please provide a quote for the following group:<br />
<br />
1. Travel details<br />
Country: <?php echo $search_country; ?><br />
City:    <?php echo $search_city; ?><br />
Date:    <?php echo $datepick; ?><br />
Nights:  <?php echo $search_night; ?><br />
<br />
2. Group details<br />
Male:     <?php echo $nb_male_gp; ?><br />
Female:   <?php echo $nb_female_gp; ?><br />
Youngest: <?php echo $lower_age; ?><br />
<br />
3. Staff/teachers/Coaches/Bus drivers Details<br />
Nb. Staff: <?php echo $nb_person_staff; ?><br />
Room type: <?php echo $rm_type; ?><br />
<br />
4. Requirements<br />
breakfast:      <?php echo $breakfast; ?><br />
dinner:         <?php echo $dinner; ?><br />
most-important: <?php echo $most_important; ?><br />
<br />
5 End-user details<br />
first-name: <?php echo $first_name; ?><br />
last-name:  <?php echo $last_name; ?><br />
email:      <?php echo $email; ?><br />
Phone:      <?php echo $phone_number; ?><br />
Demand:         <?php echo $demand; ?><br />
<br />
6. Additional information/requests<br />
<?php echo $more_info;?><br />
   <br />
   <br />
<?php
echo _('Hello')." $first_name $last_name<br />";
echo "<br />";
echo _('Good news!')."<br />";
echo "<br />";
echo _('We have found a great offer for your group.')."<br />";
echo "<br />";
echo _('Please take a look at the attached file for more details.')."<br />";
echo "<br />";
echo _('Please do not hesitate to contact me if you have any questions.')."<br />";
echo "<br />";
echo _('Sincerely.')."<br />";
echo "<br />";

echo _('Customer Service.')."<br />";
echo $this->config->item('site_name');
?>


