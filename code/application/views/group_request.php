<?php
//debug_dump($country_selected);
//debug_dump($city_selected);
//debug_dump("group form");
?>
<div id="sidebar" class="grid_6">
<?php $this->load->view('includes/video-popup'); ?>
	<?php $this->load->view('includes/testimonials'); ?>
	<?php $this->load->view('includes/siteinfo'); ?>
</div>
<div id="main" class="grid_10 group_request_page">
	<div class="box_content box_round group">

		<div class="group_request_info">
			<h1 class="box_round green_gradient_faded">
			<?php echo _('Group Booking');
			if(!empty($country_selected) && !empty($city_selected))
			{
        ?>
        - <?php echo ucfirst($city_selected);?>, <?php echo ucfirst($country_selected);?>
        <?php
			}
			elseif(!empty($country_selected))
			{
			  ?>
        - <?php echo ucfirst($country_selected);?>
        <?php
			}
			?>
			</h1>
			<img src="<?php echo site_url();?>/images/group_picture_page_sm.jpg" class="alignright" alt="" />
			<p><span class="highlight"><?php echo _('Free service - Best price guaranteed!');?></span></p>
			<p><?php echo _('We are your dedicated Group Booking specialist.');?></p>
			<p><u><?php echo _('Up to 80 people per reservation!');?></u></p>
			<p><?php echo _('If you are looking for a hostel, a Youth hostel, a hotel, a B&B or a cheap acommodation for your group, we are here to help.');?></p>
			<p><a href="#group_booking_request"><?php echo _('Send a free request now using the form below and we will get back to you in few hours.');?></a></p>
		</div>
		<div class="entry copy">

		<ul class="split group">
			<li><?php echo _('Sports groups');?></li>
			<li><?php echo _('Trips cultural, linguistic, thematic groups');?></li>
			<li><?php echo _('Bachelor party / Stag / Stagette groups');?></li>
			<li><?php echo _('Schools groups');?></li>
			<li><?php echo _('Students groups');?></li>
			<li><?php echo _('Family Reunion');?></li>
			<li><?php echo _('Marriage');?></li>
			<li><?php echo _('Business Trips');?></li>
			<li><?php echo _('Pilgrimage');?></li>
			<li><?php echo _('Schools');?></li>
			<li><?php echo _('Associations, chambers of commerce, schools, universities, day camps, sports camps, youth service towns, tourist offices, unions, community centers, businesses ...');?> </li>
		</ul>

		</div>
	</div>
	<div class="box_content box_round group">

		<script>
		jQuery(document).ready(function(){
				<?php if(!isset($country_selected)||($country_selected===NULL)):?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search_co','search_ci');
				<?php elseif(!isset($city_selected)||($city_selected===NULL)):?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search_co','search_ci',"<?php echo $country_selected;?>");
				<?php else:?>
				 loadCitiesMenu("<?php echo site_url($this->hostel_controller); ?>","<?php echo _('Chargement...');?>",'cities',cities,'search_co','search_ci',"<?php echo $country_selected;?>","<?php echo $city_selected; ?>");
				<?php endif;?>
		});

		</script>
		<div id="group_booking_request">
		<h2><?php echo _('Group Booking Request Form');?></h2>

			<form action="" method="post" class="group_booking box_round" id="group_request" onSubmit="submit_group_request();return false;">

			<h3 class="no-border">1. <?php echo _('Travel details');?></h3>
			<div class="group">
				<div class="two_col">
				<label for="search_co"><?php echo _('Spécifier le pays');?> <span class="mandatory">*</span></label>
				<select id="search_co" name="search_co" autocomplete="off" onchange="setCities('<?php echo _('Choisir la ville'); ?>','search_co','search_ci');">
				<option value=""><?php echo _('Choisir le pays'); ?></option>
				</select>
				<script type="text/javascript">
				 var SearchCo = new LiveValidation('search_co', { validMessage: ' ', onlyOnBlur: true});
				 SearchCo.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 SearchCo.add(Validate.Exclusion, { within: ['<?php echo _('Choisir le pays'); ?>'],failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				</div>
				<div class="two_col">
				<label for="search_ci"><?php echo _('Spécifier la ville');?> <span class="mandatory">*</span></label>
				<select id="search_ci" name="search_ci" autocomplete="off">
				<option value=""><?php echo _('Choisir la ville'); ?></option>
				</select>
				<script type="text/javascript">
				 var SearchCi = new LiveValidation('search_ci', { validMessage: ' ', onlyOnBlur: true});
				 SearchCi.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 SearchCi.add(Validate.Exclusion, { within: ['<?php echo _('Choisir la ville'); ?>'],failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				</div>
			</div>
			<div class="group">
				<div class="two_col">
				<label for="datepick"><?php echo _('Arrivée le:');?> <span class="mandatory">*</span></label>
				<input type="text" id="datepick" name="datepick" />
				<script type="text/javascript">
				 var datepi = new LiveValidation('datepick', { validMessage: ' ', onlyOnSubmit: true});
				 datepi.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				</div>
				<div class="two_col">

				<label for="search_night"><?php echo _('Nuits:');?> <span class="mandatory">*</span></label>
				<select name="search_night" id="search-night">
					<option selected="selected" value="">-- <?php echo _('Please Select');?> --</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
				</select>
				 <script type="text/javascript">
				 var SearchNight = new LiveValidation('search-night', { validMessage: ' ', onlyOnBlur: true});
				 SearchNight.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				 </div>
			 </div>
			 <h3 class="">2. <?php echo _('Group details');?></h3>
			 <div class="group">
				 <div class="three_col">
				 <label for="nb-male-gp"><?php echo _("Male:"); ?> <span class="mandatory">*</span></label>
				 <select name="nb_male_gp" id="nb-male-gp">
				 <option selected="selected" value="">-- <?php echo _('Please Select');?> --</option>
				 <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option>
				 </select>
				 <script type="text/javascript">
				 var Nbmale = new LiveValidation('nb-male-gp', { validMessage: ' ', onlyOnBlur: true});
				 Nbmale.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				 </div>
				 <div class="three_col">
				 <label for="nb-female-staff"><?php echo _("Female:"); ?> <span class="mandatory">*</span></label>
				 <select name="nb_female_gp" id="nb-female-gp">
				 <option selected="selected" value="">-- <?php echo _('Please Select');?> --</option>
				 <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option>
				 </select>
				 <script type="text/javascript">
				 var Nbfemale = new LiveValidation('nb-female-gp', { validMessage: ' ', onlyOnBlur: true});
				 Nbfemale.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				 </div>

				 <div class="three_col">
				 <label for="lower-age"><?php echo _('Youngest age');?> <span class="mandatory">*</span></label>
				 <select name="lower_age" id="lower-age">
				 <option selected="selected" value="">-- <?php echo _('Please Select');?> --</option>
				 <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option>
				 </select>
				 <script type="text/javascript">
				 var Nblower = new LiveValidation('lower-age', { validMessage: ' ', onlyOnBlur: true});
				 Nblower.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				 </div>
			 </div>
			 <h3 class="">3. <?php echo _('Staff/teachers/Coaches/Bus drivers Details');?></h3>
			 <div class="group">
				 <div class="three_col">
				 <label for="nb-person-staff"><?php echo _('Persons');?></label>
				 <select id="nb_person_staff" name="nb_person_staff">
				 <option selected="selected" value="0">-- <?php echo _('Please Select');?> --</option>
				 <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option>
				 </select>
				 </div>
				 <div class="three_col">
				 <label for="rm-type"><?php echo _('Room type requested');?></label>
				 <select name="rm_type" id="rm-type">
					 <option selected="selected" value="N/A">-- <?php echo _('Please Select');?> --</option>
					 <option value="Single rooms"><?php echo _('Single rooms');?></option>
					 <option value="Twin rooms"><?php echo _('Twin rooms');?></option>
					 <option value="Double rooms"><?php echo _('Double rooms');?></option>
					 <option value="Triple rooms"><?php echo _('Triple rooms');?></option>
					 <option value="In the dormss with the group"><?php echo _('In the dormss with the group');?></option>
				 </select>
				 <span id="rm_type_error" class=" LV_validation_message LV_invalid" style="display:none;"><?php echo _('Champ obligatoire');?></span>
				 </div>

			 </div>

			 <div class="total-nb"><?php printf( gettext('Total Number of people in your group: %s'),'<span class="total-nb-people">0</span>');?></div>

			 <h3 class="">4. <?php echo _('Requirements');?></h3>

			 <div class="radiogroup">
			 <label for="breakfast"><?php echo _('Breakfast')?> <span class="mandatory">*</span></label>
			 <input type="radio" class="radio" name="breakfast" value="yes"> <span class="radio_span"><?php echo _('yes');?></span>
			 <input type="radio" class="radio" name="breakfast" value="no" checked="checked"> <span class="radio_span"><?php echo _('no');?></span>
			 </div>

			 <div class="radiogroup">
			 <label for="dinner"><?php echo _('Dinner')?> <span class="mandatory">*</span></label>
			 <input type="radio" class="radio" name="dinner" value="yes"> <span class="radio_span"><?php echo _('yes');?></span>
			 <input type="radio" class="radio" name="dinner" value="no" checked="checked"> <span class="radio_span"><?php echo _('no');?></span>
			 </div>
			 <div class="radiogroup">
			 <label for="most_important"><?php echo _('Most Important')?> <span class="mandatory">*</span></label>
			 <input type="radio" class="radio" name="most_important" value="Price" checked="checked"> <span class="radio_span"><?php echo _('Price');?></span>
			 <input type="radio" class="radio" name="most_important" value="Location"> <span class="radio_span"><?php echo _('Location');?></span>
			 </div>
			 <h3 class="">5. <?php echo _('Your details');?></h3>
			 <div class="group">
				 <div class="two_col">
				 <label for="first_name"><?php echo _('Prénom');?> <span class="mandatory">*</span></label>
				 <input type="text" name="first_name" id="first-name" />
  				 <script type="text/javascript">
           var firstname = new LiveValidation('first-name', { validMessage: ' ', onlyOnBlur: true});
           firstname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
           </script>
				 </div>
				 <div class="two_col">
				 <label for="last_name"><?php echo _('Nom'); ?> <span class="mandatory">*</span></label>
				 <input type="text" name="last_name" id="last-name" />
				 		<script type="text/javascript">
						var lastname = new LiveValidation('last-name', { validMessage: ' ', onlyOnBlur: true});
						lastname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						</script>
				 </div>
				</div>
				<div class="group">
				 <div class="two_col">
				 <label for="email"><?php echo _("Adresse Email");?> <span class="mandatory">*</span></label>
				 <input type="text" name="email" id="email"/>
				   <script type="text/javascript">
						var EmailAddress = new LiveValidation('email', { validMessage: ' ', onlyOnBlur: true});
						EmailAddress.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						EmailAddress.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
						</script>
				 </div>
				 <div class="two_col">
				 <label for="confirm-email"><?php echo _("Confirmation Email");?> <span class="mandatory">*</span></label>
				 <input type="text" name="confirm-email" id="confirm-email" />
				 	<script type="text/javascript">
						var EmailAddress2 = new LiveValidation('confirm-email', { validMessage: ' ', onlyOnBlur: true});
						EmailAddress2.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
						EmailAddress2.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
						EmailAddress2.add( Validate.Confirmation, { match: 'email', failureMessage: "<?php echo _('Both emails must be the same'); ?>" } );
						</script>
				 </div>
				</div>
				<div class="group">
                                 <div class="one_col">
                                 <label for="phone_number"><?php echo _('Please provide a phone number where we can reach you from 4:00 pm to 9:00 pm.')?><span class="mandatory">*</span></label>
<small><?php echo _('Country code, Area code (without first 0) and mobile number; no spaces, brackets or dashes.')?> <?php echo _('UK Example: 442012341234')?></small><br/><br/>
                                 </div>
                                </div>
                                <div class="group">
				 <div class="two_col">
                                <input type="text" name="phone_number" id="phone_number"/> 
			   <script type="text/javascript">
					var phone_number = new LiveValidation('phone_number', { validMessage: ' ', onlyOnBlur: true});
					phone_number.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
					phone_number.add(Validate.Numericality, {notANumberMessage: "<?php echo _('Invalid phone number format'); ?>"});
					</script>
				 </div>

			 </div>

			 <div class="one_col">
				<label for="demand"><?php echo _('Due to high demand, and the limited amount of time Hostels can keep availability, we would need to know the following, when will you be able to book?');?> <span class="mandatory">*</span></label>
				<select name="demand" id="demand">
				<option value="" selected="selected">-- <?php echo _('Please Select');?> --</option>
				<option value="I just want an idea of the price and property"><?php echo _('I just want an idea of the price and property');?></option>
				<option value="Within 1 week or less"><?php echo _('Within 1 week or less');?></option>
				<script type="text/javascript">
				 var demand = new LiveValidation('demand', { validMessage: ' ', onlyOnBlur: true});
				 demand.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
				 </script>
				</select>
				</div>
			 <div class="one_col">
			 <label for="more_info"><?php echo _('Additional information/requests');?></label>
			 <textarea name="more_info"></textarea>
			 </div>
			 <div class="group">
			 <div class="three_col">
			 <input type="submit" id="submit-button" class="button-green box_shadow_hard box_round hoverit submit_request" value="<?php echo _('Submit')?>"  onClick="return check_error();" />


			 </div>
			 <p id="loading_message" class="loading_book" style="display: none;">
					<img src="<?php echo site_url('images/V2/loading-squares-greenback.gif');?>" alt=""/>
					<span><?php echo _('Traitement de la demande...'); ?></span>
				</p>
			 <p id="check_error" style="display:none;"><?php echo _('Please make sure all required fields are filled out correctly'); ?></p>
			 <p id="group_request_success" style="display:none;"><?php echo _('Thank you. Our team will contact you within few hours');?></p>
			 </div>

			</form>

		</div>
	</div>
</div>
