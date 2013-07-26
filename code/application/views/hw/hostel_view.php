<script type="text/javascript" src="<?php echo site_url('js/pweb/includes/mustache.js'); ?>" charset="UTF-8"></script>
<script type="text/javascript" src="<?php echo site_url('js/save_property.js?v=' . time()); ?>" charset="UTF-8"></script>

<?php
$switch_api = false;
if ($this->session->userdata('switch_api')) {
    $switch_api = true;
}
echo form_hidden('switch_api', $switch_api);
?>
<div id="sidebar" class="grid_4 hostel_view_side">
    <?php //$this->load->view('includes/widget-cours'); ?>
    <a id="back_to_results" style="display: none;" title="<?php echo _('Back to search results') ?>"
       class="back_to_results expand"
       href="<?php echo base_url() . $hostel->country . '/' . $hostel->city; ?>"><strong>&laquo; <?php echo _('Back to search results') ?></strong></a>
    <?php
    if (!isset($date_selected))
        $date_selected = NULL;
    if (!isset($numnights_selected))
        $numnights_selected = NULL;
    if (!isset($bc_continent))
        $bc_continent = NULL;
    if (!isset($bc_country))
        $bc_country = NULL;
    if (!isset($bc_city))
        $bc_city = NULL;
    $this->load->view('includes/side_search_box', array('date_selected' => $date_selected, 'current_view' => $current_view, 'numnights_selected' => $numnights_selected, 'bc_continent' => $bc_continent, 'bc_country' => $bc_country, 'bc_city' => $bc_city));
    ?>
    <?php 
    if (ISDEVELOPMENT) {
        $static_map_icon_base_url = "http://www.aubergesdejeunesse.com/";
    } else {
        $static_map_icon_base_url = base_url();
    }
    if (isset($google_map_geo_latlng)) { ?>
        <div class="box_content map_button_box box_round" id="map_button_side">
            <a id="city_map_show_hostel" href="javascript:void(0);" onclick="$('#show_full_map').trigger('click');
                                    $(document).scrollTop($('#show_full_map').offset().top);">
                <span><strong><?php echo _("Voir la carte"); ?></strong></span>
                <img class=""
                     src="https://maps.google.com/maps/api/staticmap?center=<?php echo $google_map_geo_latlng; ?>&zoom=10&size=253x125&sensor=false&language=<?php echo $this->wordpress->get_option('aj_lang_code2'); ?>&markers=icon:<?php echo $static_map_icon_base_url; ?>images/map_markers/selected/marker_selected_0.png%7C+<?php echo $google_map_geo_latlng; ?>"/>
            </a>
        </div>
    <?php } ?>

    <?php $this->load->view('includes/recently_viewed_properties'); ?>
    <?php $this->load->view('includes/video-popup'); ?>
    <?php $this->load->view('includes/testimonials'); ?>
    <?php $this->load->view('includes/widget-qr-code'); ?>
    <?php $this->load->view('includes/siteinfo'); ?>
</div>

<?php
//error handling standard
if ($api_error == false) {
    //$hostel is set
} elseif ($api_error_msg == false) {
    //serveur inaccessible en ce moment (HW only)
} else {
    //error message = $api_error_msg->UserMessage->message
    //error details (en anglais) = $api_error_msg->UserMessage->detail
    ?>
    <div id="main" class="grid_12">
        <div class="box_content box_round group">
            <h1 class="content_title"><?php echo _("Erreur de requête"); ?></h1>

            <div class="dispo-error">
                <p><strong><?php echo _("Erreur:"); ?></strong>
                    <?php
                    if (!empty($api_error_msg->messageTranslated)) {
                        echo $api_error_msg->messageTranslated;
                    } else {
                        echo $api_error_msg->message;
                    }
                    ?>
                    <br>
                </p>

                <p><strong><?php echo _("Détails:"); ?></strong>
                    <?php
                    if (!empty($api_error_msg->detailTranslated)) {
                        echo $api_error_msg->detailTranslated;
                    } else {
                        echo $api_error_msg->detail;
                    }
                    ?>
                    <br>
                </p>
            </div>
        </div>
    </div>
<?php
}
?>
<?php if ($api_error == false): //print_r($hostel->PropertyImages) ?>
<?php
// Separate images from thumb to main
    $main_images = array();
    $thumb_images = array();
    $count_main = 0;
    $count_thumb = 0;
    foreach ($hostel->PropertyImages as $image):
        if ($image->imageType == 'Main') {
            $main_images[$count_main] = $image->imageURL;
            $count_main++;
        }
        if ($image->imageType == 'Thumbnail') {
            $thumb_images[$count_thumb] = $image->imageURL;
            $count_thumb++;
        }
    endforeach;
    ?>

    <div id="main" class="grid_12">
    <?php if (isset($_POST["submitted"])) { ?>
        <div class="box_content box_round group comment_sent">
            <h2 class="comment_sent"><?php echo _('Votre commentaire a été envoyé à notre équipe pour approbation. Merci!'); ?></h2>
        </div>
    <?php } else { ?>
        <?php if ($this->input->get('comment', TRUE) == "insert") { ?>
            <div class="box_content box_round group">
                <div class="enter-comment">

                    <h2 class="margbot15"><?php printf(gettext("Laissez vos impressions sur %s"), var_check($hostel->property_name, "")); ?></h2>

                    <form action="" method="POST" id="comment-insert">
                        <input type="hidden" name="property_number" value="<?php echo $hostel->property_number ?>">
                        <input type="hidden" name="property_name" value="<?php echo $hostel->property_name ?>">
                        <input type="hidden" name="property_city" value="<?php echo $hostel->city ?>">
                        <input type="hidden" name="property_country" value="<?php echo $hostel->country ?>">
                        <input type="hidden" name="property_type" value="<?php echo $hostel->property_type ?>">
                        <table>
                            <tr>
                                <td><label for="firstname"><?php echo _("Votre Prénom:"); ?></label>
                                    <input type="text" id="comment_firstname" name="firstname" class="large-text"
                                           value="<?php echo set_value('firstname', ''); ?>"/></td>
                                <td><?php echo form_error('firstname'); ?></td>
                                <script type="text/javascript">
                                    var comment_firstname = new LiveValidation('comment_firstname', {validMessage: ' ', wait: 500});
                                    comment_firstname.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

                                    comment_firstname.add(Validate.Length, {minimum: 2});
                                </script>
                            </tr>
                            <tr>
                                <td><label for="lastname"><?php echo _("Votre Nom (ne sera pas publié):"); ?></label>
                                    <input type="text" id="comment_name" name="lastname" class="large-text"
                                           value="<?php echo set_value('lastname', ''); ?>"/></td>
                                <td><?php echo form_error('lastname'); ?></td>
                                <script type="text/javascript">
                                    var comment_name = new LiveValidation('comment_name', {validMessage: ' ', wait: 500});
                                    comment_name.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});

                                    comment_name.add(Validate.Length, {minimum: 2});
                                </script>
                            </tr>
                            <tr>
                                <td>
                                    <label for="email"><?php echo _("Votre Email (ne sera pas publié):"); ?></label>
                                    <input type="text" id="comment_email" name="email" class="large-text"
                                           value="<?php echo set_value('email', ''); ?>"/><?php echo form_error('email'); ?>
                                </td>
                                <td><?php echo form_error('email'); ?></td>
                                <script type="text/javascript">
                                    var comment_email = new LiveValidation('comment_email', {validMessage: ' ', wait: 500});
                                    comment_email.add(Validate.Email, {failureMessage: "<?php echo _('Insérer un courriel valide'); ?>"});
                                    comment_email.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
                                </script>
                            </tr>

                            <tr>
                                <td>
                                    <label for="nationality"><?php echo _("Nationalité :"); ?></label>
                                    <?php $this->Db_country->select_country("Nationality", "nationality", set_value('nationality', ''), "style=\"width: auto;\"", "en", $this->site_lang, _('Choisir le pays')); ?>
                                </td>
                                <td><?php echo form_error('nationality'); ?></td>
                                <script type="text/javascript">
                                    var Nationality = new LiveValidation('Nationality', {validMessage: ' ', wait: 500});
                                    Nationality.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
                                </script>
                            </tr>

                            <tr>
                                <td>
                                    <label for=""><?php echo _("Veuillez évaluer votre expérience:"); ?></label>

                                    <div class="stars clearfix">
                                        <input id="star_radio" type="radio" class="star {split:2}" name="star-rating"
                                               value="1" <?php echo set_radio('star-rating', '1'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="2" <?php echo set_radio('star-rating', '2'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="3" <?php echo set_radio('star-rating', '3'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="4" <?php echo set_radio('star-rating', '4'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="5" <?php echo set_radio('star-rating', '5'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="6" <?php echo set_radio('star-rating', '6'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="7" <?php echo set_radio('star-rating', '7'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="8" <?php echo set_radio('star-rating', '8'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="9" <?php echo set_radio('star-rating', '9'); ?>/>
                                        <input type="radio" class="star {split:2}" name="star-rating"
                                               value="10" <?php echo set_radio('star-rating', '10'); ?>/>

                                    </div>
                                </td>
                                <td><?php echo form_error('star-rating'); ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <label
                                        for="comment"><?php printf(gettext("Vos commentaires sur %s :"), var_check($hostel->property_name, "")); ?></label>
                                    <textarea id="text_comment" name="comment" cols="50"
                                              rows="10"><?php echo set_value('comment', ''); ?></textarea>
                                </td>
                                <td><?php echo form_error('comment'); ?></td>
                                <script type="text/javascript">
                                    var text_comment = new LiveValidation('text_comment', {validMessage: ' ', wait: 500});
                                    text_comment.add(Validate.Presence, {failureMessage: "<?php echo _('Champ obligatoire'); ?>"});
                                </script>
                            </tr>

                            <tr>
                                <td>
                                    <label style="padding-top:10px; padding-bottom:5px;"
                                           for=""><?php echo _("La date de votre dernier séjour à cet endroit :"); ?></label>

                                    <?php select_month("month", "month_comment", "small-select", set_value("month_comment", NULL)); ?>

                                    <?php select_year("year-comment", "year_comment", "small-select", 0, -11, set_value("year_comment", NULL)); ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" name="submitted" value="true"/>
                                    <input type="submit" class="button-green box_round hoverit" name="comment-submit"
                                           id="comment-submit" value="<?php echo _("Soumettre"); ?>"/>
                                </td>
                                <td></td>
                            </tr>

                        </table>
                    </form>
                </div>
            </div>
        <?php
        }
    } // End if for the show form and comment post ?>

    <div class="box_content box_round group">
    <?php if (isset($hostel->PropertyImages)) { ?>
        <div class="box_round image_container">
            <div class="slideshow" id="slideshow">
                <div class="slides">
                    <div class="main-pic">
                        <?php foreach ($main_images as $image):
				if (empty($print)) { ?>
                            <a class="openup" rel="<?php echo var_check($hostel->property_name, ""); ?>"
                               href="<?php echo var_check($image, "/test.jpg"); ?>"
                               alt="<?php echo $hostel->property_name; ?>">
                                <img class="main" width="210" data-href="<?php echo $image; ?>"
                                     src="<?php echo site_url("images/V2/blank.gif"); ?>"
                                     alt="<?php echo $hostel->property_name; ?>"/>
                                <img class="main" width="210" src="<?php echo $image; ?>"
                                     alt="<?php echo $hostel->property_name; ?>"/></a>
                        <?php } else { ?>
				<img class="main" width="210" src="<?php echo $image; ?>"alt="<?php echo $hostel->property_name; ?>"/></a>  <?php 
			} endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="group">
        <h1><?php echo my_mb_ucfirst($bc_city); ?>
            - <?php echo $this->Db_term_translate->get_term_translation($hostel->property_type, $this->site_lang); ?>
            - <?php echo var_check($hostel->property_name, ""); ?></h1>

        <p class="address_title"><?php echo var_check($hostel->address1, "");
            echo ' ' . var_check($hostel->address2, "");
            echo ', ' . var_check($hostel->city, "");
            echo ', ' . var_check($hostel->country, ""); ?>
            <?php // Need to show district name, city, country here, like a breakcrumb almost  ?>
        </p>

        <?php
        if (is_array($district_info) && !empty($district_info)) {
            ?>
            <div id="hostel_districts" class="hostel_districts">
                <p>
                    <span class="hostel_districts_district"><?php echo _('Districts'); ?>:</span>
                            <span class="hostel_districts_values">
        <?php
                                foreach ($district_info as $key => $district) {

                                    echo $district->district_name;

                                    if (count($district_info) != $key + 1) {
                                        echo ", ";
                                    } else {
                                        echo ".";
                                    }
                                }//end Foreach
                                ?>
                            </span>
                </p>
            </div>
        <?php }// end if  ?>

        <?php
        if (is_array($landmarks) && !empty($landmarks)) {
            ?>
            <div id="hostel_landmarks" class="hostel_landmarks">
                <p>
                    <span class="hostel_landmarks_landmark"><?php echo _('Landmarks (within 2km)'); ?>:</span>
                            <span class="hostel_landmarks_values">
        <?php
                                foreach ($landmarks as $key => $landmark) {

                                    echo $landmark->landmark_name;

                                    if (count($landmarks) != $key + 1) {
                                        echo ", ";
                                    } else {
                                        echo ".";
                                    }
                                }//end Foreach
                                ?>
                            </span>
                </p>
            </div>
        <?php }// end if ?>

        <div class="top_info" id="top_info_short">
            <?php
            $word = 50;
            if (!empty($hostel->descriptionTranslated)) {
                $description_first_lang = word_limiter(strip_tags($hostel->descriptionTranslated), $word);
                echo '<div class="translated"><p>' . $description_first_lang . '</p></div>';
                $description_first = word_limiter(strip_tags($hostel->description), $word);
                echo '<div class="original" style="display:none;"><p>' . $description_first . '</p></div>';
            } else {
                $description_first = word_limiter(strip_tags($hostel->description), $word);
                echo '<div class="original"><p>' . $description_first . '</p></div>';
            }
            ?>
        </div>
        <div class="top_info" id="top_info_long" style="display:none;">
            <?php
            if (!empty($hostel->descriptionTranslated)) {
                echo '<div class="translated">' . strip_tags($hostel->descriptionTranslated, '<p>') . '</div>';
                echo '<div class="original" style="display:none;">' . nl2p(var_check(strip_tags($hostel->description, '<p>'), ""), false, true) . '</div>';
            } else {
                echo nl2p(var_check($hostel->description, ""), false, true);
            }
            ?>
        </div>
        <div style="float:left">
            <p><a id="read_more_hostel" href="#"><?php echo _('Read more…') ?> &raquo;</a></p>

            <p><a id="read_less_hostel" style="display:none;" href="#">&laquo; <?php echo _('Close') ?></a></p>
        </div>
        <?php if ($this->site_lang != "en") { ?>
            <select class="select-translate translation-toggle">
                <option value="translate"><?php echo _("Voir la version traduite"); ?></option>
                <option value="original"><?php echo _("Voir l'original"); ?></option>
            </select>
        <?php } ?>
        <br clear="all"/>
        <?php
        $code = $this->wordpress->get_option('aj_lang_code');
        $shortcode = strtolower(substr($code, 0, 2));
        $code = str_replace('-', '_', $code);
        if ($code == '' || $shortcode == 'en') {
            $code = "en_US";
        }
        ?>
    </div>
    <div class="group hostel-meta">
        <div class="fblike">
            <script src="https://connect.facebook.net/<?php echo $code; ?>/all.js#xfbml=1"></script>
            <fb:like data-layout="button_count" show_faces="false"></fb:like>
        </div>

        <?php if ($this->config->item('displaySaveProperty')) {
            $addToFav = $favorited ? 'display:none' : '';
            $addedToFav = $favorited ? '' : 'display:none';
            ?>
            <div class="save_to_favorites_options">
                <span class="city_selected" style="display: none"><?php echo my_mb_ucfirst($bc_city); ?></span>
                    <span class="country_selected"
                          style="display: none"><?php echo my_mb_ucfirst($bc_country); ?></span>

                <a href="#" class="save_to_favorites" id="save_to_favorites_<?php echo $hostel->property_number; ?>"
                   style="vertical-align: middle;<?php echo $addToFav; ?>"
                   rel="<?php echo var_check($hostel->property_name, ""); ?>"
                   title="<?php echo _("You can save this property as a favorite in your account so you can easily book it at a later date if you wish."); ?>">
                    <img style="vertical-align: middle" src="<?php echo site_url(); ?>/images/save_favorite_white.png"/>
                    <?php echo _('Add to my favorites'); ?>
                </a>

                <a href="<?php echo site_url('user/favorite_properties'); ?>" class="saved_to_favorites"
                   id="saved_to_favorites_<?php echo $hostel->property_number; ?>"
                   style="vertical-align: middle;<?php echo $addedToFav; ?>"
                   title='<?php echo _('This property has been saved in your "My account" section. You can now easily book it at a later date if you wish.'); ?>'>
                    <img style="vertical-align: middle" src="<?php echo site_url(); ?>/images/saved_favorite_white.png"/>
                    <?php echo _('Saved to my favorites'); ?>
                </a>
            </div>
        <?php } ?>


        <?php if ($showEmail) { ?>
            <div class="share-email">
                <a id="share-email" class="share" href="<?php echo site_url("images/share_email.png"); ?>"><img
                        src="<?php echo site_url("images/share_email.png"); ?>" alt="Share Email"/></a>
            </div>
        <?php } ?>


        <div class="amenities no-indent">
            <?php
            if (!empty($main_services)) {
                foreach ($main_services as $service) {
                    if ($service->service_type == 'facility') {
                        ?>
                        <span
                            class="icon_facility icon_facility_<?php echo $service->service_id; ?> group"><span><?php echo$service->description; ?></span></span>
                    <?php
                    } else {
                        ?>
                        <span
                            class="icon_facility icon_landmark group"><span><?php echo $service->description; ?></span></span>
                    <?php
                    }
                }
            }
            ?>
        </div>

    </div>
    <?php if (isset($hostel->PropertyImages)) {
        $count = 0; ?>
        <div class="thumbnail_list" id="thumbnail_list">
            <?php foreach ($thumb_images as $image): ?>
                <?php if (empty($print)) { ?>
                    <a class="openup" rel="<?php echo var_check($hostel->property_name, ""); ?>"
                       href="<?php echo $main_images[$count]; ?>"
                       alt="<?php echo var_check($hostel->property_name, ""); ?>">

                        <img height="45px" data-href="<?php echo $image; ?>"
                             src="<?php echo site_url("images/V2/blank.gif"); ?>"
                             alt="<?php echo $hostel->property_name; ?>"/>
                        <noscript>
                            <img height="45px" src="<?php echo $image; ?>"
                                 alt="<?php echo $hostel->property_name; ?>"/>
                        </noscript>

                    </a>
                <?php } else { ?>
                    <img height="45px" src="<?php echo $image; ?>" alt="<?php echo $hostel->property_name; ?>"/>
                <?php } ?>
                <?php $count++;
            endforeach; ?>
        </div>
    <?php } ?>
    </div>

    <nav class="hostel_tabs group" id="hostels_tabs">
        <ul class="box_round ui-tabs-nav green_gradient_faded ui-hostels_tabs-nav">
            <li class="first">
                <a class="tab_price" href="#hostel_info_home">
                    <?php
                    if ($switch_api) {
                        echo _("Information");
                    } else {
                        echo _("Info & Prix");
                    }
                    ?>
                </a>
            </li>
            <li><a id="show_full_map" class="tab_direction" href="#hostel_info_direction"
                   onClick="appendBootstrap()"><?php echo _("Cartes et Directions"); ?></a></li>
            <li class="last"><a id="tab_comment" class="tab_review"
                                href="#hostel_info_reviews"><?php echo _("Commentaires"); ?></a></li>
        </ul>
        <?php
        if (!empty($hostel->rating)) {
            $rating = '';
            if (($hostel->rating > 59) && ($hostel->rating < 70)) {
                $rating = _("Good");
            } elseif (($hostel->rating > 69) && ($hostel->rating < 80)) {
                $rating = _("Very good");
            } elseif (($hostel->rating > 79) && ($hostel->rating < 90)) {
                $rating = _("Great");
            } elseif (($hostel->rating > 89)) {
                $rating = _("Fantastic");
            }
            ?>
            <ul class="box_round rating">
                <li class="first last"><span class="" title="<?php echo _("évaluation moyenne"); ?>"><strong
                            class="txt-mid green"><?php echo $rating; ?></strong><strong
                            style="color:#333333;"><?php echo $hostel->rating; ?> %</strong></span></li>
            </ul>
        <?php } ?>
    </nav>

    <div class="box_content box_round group hostel_info ui-tabs">
    <div id="hostel_info_home" class="hostels_tab_content">
    <?php
    if (!$this->api_forced) {
        ?>
        <div class="content_block">

            <form class="group box_round" id="dispo-form" action="" method="">
                <input type="hidden" id="book-property-number" name="book-property-number"
                       value="<?php echo $hostel->property_number; ?>"/>
                <input type="hidden" id="book-property-name" name="book-property-name"
                       value="<?php echo $hostel->property_name; ?>"/>
                <?php
                //To support handling of dates and numnights of cached property page
                //JS is used to carry the session data on the cached view
                ?>
                <script>
                    $(document).ready(
                        function () {
                            jQuery("#book-pick").datepicker({dateFormat: 'd MM, yy', minDate: 0});
                            var date_cookie = getCookie('date_selected');
                            var valid_date_cookie = true;
                            if (isValidDate(date_cookie)) {
                                var date_url = date_cookie;
                                var date_array = date_cookie.split('-');
                                var date_avail = new Date(date_array[0], date_array[1] - 1, date_array[2]);
                                $("#book-pick").datepicker("setDate", date_avail);
                            }
                            else {
                                var date_avail = new Date();
                                date_avail.setDate(date_avail.getDate() + 10);
                                $("#book-pick").datepicker("setDate", date_avail);
                                date_avail = siteDateString(date_avail);
                                var date_url = date_avail;
                                valid_date_cookie = false;
                            }

                            var numnight_cookie = getCookie('numnights_selected');
                            if (numnight_cookie) {
                                document.getElementById('book-night').value = numnight_cookie;
                                var night_url = numnight_cookie;
                            }
                            else {
                                numnight_avail = 2;
                                document.getElementById('book-night').value = numnight_avail;
                                var night_url = numnight_avail;
                            }

                            function getURLParameter(name) {
                                return decodeURI(
                                    (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
                                );
                            }

                            var currency_value = getURLParameter('currency');
                            if (!currency_value || currency_value == "null") {
                                currency_value = getCookie('currency_selected');
                            }
                            if (currency_value) {
//                                document.getElementById('book-property-currency').value = currency_value;
                            }
                            else {
                                currency_value = 'EUR';
//                                document.getElementById('book-property-currency').value = currency_value;
                            }

                            <?php
                            $and_print = '';
                            if (!empty($print)) {
                                $and_print = '?print=true';
                            }
                            ?>

                            var current_url = $("#back_to_results").attr("href");
                            $('#back_to_results').attr("href", current_url + "/" + date_url + "/" + night_url);
                            if (valid_date_cookie) {
                                checkAvailability('<?php echo site_url($this->hostel_controller); ?>', '<?php echo str_replace("'", "\\'", $bc_country); ?>', '<?php echo str_replace("'", "\\'", $bc_city); ?>', <?php echo $hostel->property_number; ?>, 'book-pick', document.getElementById('book-night').value, '<?php echo addslashes($hostel->property_name); ?>', /*document.getElementById('book-property-currency').value*/ '<?php $this->config->item('site_currency_selected')?>', '<?php echo _('Date invalide'); ?>', 'booking-table', '<?php echo $and_print; ?>');
                            }
                        }
                    );
                </script>
                <ul class="group">
                    <?php
                    if (!isset($date_selected)) {
                        $date_selected = get_date_default();
                    }
                    ?>
                    <li>
                        <label for="book-pick"><?php echo _('Arrivée le:'); ?></label>
                        <input type="text" id="book-pick" name="book-pick" value="<?php echo $date_selected; ?>"/>
                    </li>

                    <li>
                        <?php
                        if (!isset($numnights_selected)) {
                            $numnights_selected = 2;
                        }
                        $hb_api_used = ($this->api_used == HB_API) ? TRUE : FALSE;
                        select_nights(_('Nuits:'), "book-night", "book-night", $numnights_selected, $hb_api_used);
                        ?>
                    </li>
                    <li>
                        <label for="book-property-currency"><?php echo _("Devise:"); ?></label>
                        <?php $this->Db_currency->select_currency("book-property-currency", "book-property-currency", $currency, "", $this->site_lang); ?>
                    </li>                                                                                                                                                                                                                                                                                                                                                                                                                                                             

                    <?php
                    $and_print = '';
                    if (!empty($print)) {
                        $and_print = '?print=true';
                    }
                    ?>

                    <li class="last">
                        <input onfocus="this.blur()" type="button" name="book-submit" id="book-submit"
                               class="button-green box_round hoverit" value="<?php echo _("Rechercher"); ?>"
                               OnClick="$('ul.tabing').tabs('select', 0);
                                   checkAvailability('<?php echo site_url($this->hostel_controller); ?>', '<?php echo str_replace("'", "\\'", $bc_country); ?>', '<?php echo str_replace("'", "\\'", $bc_city); ?>',<?php echo $hostel->property_number; ?>, 'book-pick', document.getElementById('book-night').value, '<?php echo addslashes($hostel->property_name); ?>', /*document.getElementById('book-property-currency').value*/ '<?php $this->config->item('site_currency_selected')?>', '<?php echo _('Date invalide'); ?>', 'booking-table', '<?php echo $and_print; ?>');"/>
                    </li>

                </ul>

            </form>
            <p id="loading_dispo"><?php echo _('Recherche de disponibilités...'); ?></p>

            <div id="booking-table"></div>

        </div>


        <?php if ($showPDF) { ?>
            <div class="content_block" id="share_pdf_container">
                <a href="<?php echo site_url(); ?>" id="share-pdf" class="share share-pdf" style="display: block">
                    <strong style="float: left; display: block;"><?php echo _("Want to receive or send a PDF copy of this quote?"); ?></strong>
                </a>

                <a href="<?php echo site_url(); ?>" class="share-pdf share" style="float: left; display: block; margin-left: 5px">
                   <img src="<?php echo site_url("images/share_pdf.png"); ?>" alt="Share PDF" style="padding-left: 20px" />
                </a>
                <br style="clear: both"/>
            </div>
        <?php } ?>

    <?php
    }
    ?>
    <?php if (isset($hostel->facilities)) { ?>
        <div class="content_block">
            <h2 class="margbot10"><?php echo _("Commodité"); ?></h2>
            <?php if (!empty($hostel->Facilities->facilityTranslated)) { ?>
                <select class="select-translate">
                    <option value="translate"><?php echo _("Voir la version traduite"); ?></option>
                    <option value="original"><?php echo _("Voir l'original"); ?></option>
                </select>
            <?php } ?>
            <div class="group">
                <ul class="float-list green-li increase1 <?php if (!empty($hostel->facilitiesTranslated)) { ?>translated<?php } ?>">
                    <?php
                    $numloop = count($hostel->facilities);
                    $breakloop = round($numloop / 3);
                    $loopcount = 0;
                    $facilities = (array)$hostel->facilities;
                    if (!empty($hostel->facilitiesTranslated)) {
                        $facilities = (array)$hostel->facilitiesTranslated;
                    }
                    if (!empty($facilities)) {
                        foreach ($facilities as $facility) {
                            if (!empty($facility)) {
                                if ($loopcount == $breakloop) {
                                    $loopcount = 0;
                                    echo '</ul><ul class="float-list green-li increase1';
                                    if (!empty($hostel->facilitiesTranslated)) {
                                        echo ' translated';
                                    }
                                    echo '">';
                                }
                                $loopcount++;
                                echo '<li>' . stripslashes(var_check($facility, "")) . '</li>';
                            }
                        }
                    }
                    ?>

                </ul>
                <?php if (!empty($hostel->facilitiesTranslated)) { ?>
                    <ul class="float-list green-li increase1 original marg20l" style="display:none;">
                        <?php
                        $numloop = count($hostel->facilities);
                        $breakloop = round($numloop / 3);
                        $loopcount = 0;

                        $facilities = (array)$hostel->facilities;

                        if (!empty($facilities)) {
                            foreach ($facilities as $facility) {
                                if (!empty($facility)) {
                                    if ($loopcount == $breakloop) {
                                        $loopcount = 0;
                                        echo '</ul><ul class="float-list green-li increase1 original" style="display:none;">';
                                    }
                                    $loopcount++;
                                    echo '<li>' . stripslashes(var_check($facility, "")) . '</li>';
                                }
                            }
                        }
                        ?>
                    </ul>

                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($hostel->conditions)) { ?>
        <div class="content_block">
            <h2><?php echo _("Informations Importantes"); ?></h2>
            <?php if (!empty($hostel->conditionsTranslated)) { ?>
                <select class="select-translate">
                    <option value="translate"><?php echo _("Voir la version traduite"); ?></option>
                    <option value="original"><?php echo _("Voir l'original"); ?></option>
                </select>
            <?php } ?>
            <?php
            if (!empty($hostel->conditionsTranslated)) {
                echo '<div class="translated">' . strip_tags($hostel->conditionsTranslated, '<p>') . '</div>';
                echo '<div class="original" style="display:none;">' . nl2p(var_check(strip_tags($hostel->conditions, '<p>'), ""), false, true) . '</div>';
            } else {
                echo nl2p(var_check(strip_tags($hostel->conditions), ""), false, true);
            }
            ?>
        </div>
    <?php } ?>
    </div>

    <?php if (!empty($print)) { ?>
        <link type="text/css" rel="stylesheet" href="/css/pdf.css"/>
        <strong><?php echo _('PLEASE NOTE THIS IS NOT A CONFIRMED BOOKING'); ?></strong><br/><br/>
    <?php } ?>

    <div id="hostel_info_direction" class="hostels_tab_content ui-tabs-hide">
        <div class="content_block">
            <h2><?php echo _("Adresse"); ?></h2>
            <a href="#hostel_info_home" onclick="$('.tab_price').trigger('click'); return false;" class="booking-form-submit button-green box_round hoverit" style="display: inline; float: right"><?php echo _('Click here to see info and prices'); ?></a>

            <p><?php echo var_check($hostel->address1, "");
                echo ' ' . var_check($hostel->address2, "");
                echo ', ' . var_check($hostel->city, "");
                echo ', ' . var_check($hostel->country, ""); ?></p>
        </div>
        <div class="content_block">
            <h2><?php echo _("Cartes"); ?></h2>
            <?php
              $checked = "";
              $districtChecked = false;
            if (is_array($district_info) && !empty($district_info)) {
                ?>
                <div id="hostel_mapView_districts" class="hostel_mapView_districts">
                    <p>
                        <span class="mapView_districtWord"><?php echo _('Districts'); ?>:</span>

                        <?php
                      
                        foreach ($district_info as $key => $district) {
                            
                            if ($key == 0) {
                                $checked = "checked";
                                $districtChecked = true;
                            }
                            ?>
                            <input type="radio" id="landmarkAndDistrict" name="landmarkAndDistrict" <?php echo $checked; ?>
                                   value="<?php echo $district->um_id; ?>"
                                   onchange="ClearlandmarkAndDistrict(); changeDistrictLayer(<?php echo $district->um_id; ?>);"><?php echo $district->district_name; ?>

                        <?php 
                            }//end Foreach  
                            $checked = "";
                            ?>
                    </p>
                </div>
            <?php }// end if  ?>

            <?php
            // start showing landmarks checkboxes
            if (is_array($landmarks) && !empty($landmarks)) {
                ?>
                <div id="hostel_mapView_landmarks" class="hostel_mapView_landmarks">
                    <p>
                        <span class="mapView_landmarkWord"><?php echo _('Landmarks (within 2km)'); ?>:</span>

                        <?php
                        foreach ($landmarks as $key => $landmark) {
                            if ($districtChecked === false) {                            
                                if ($key == 0) {
                                    $checked = "checked";
                                }
                            }
                            ?>
                            <input type="radio" id="landmarkAndDistrict" name="landmarkAndDistrict" <?php echo $checked; ?>
                                   value="<?php echo $landmark->geo_latitude . "," . $landmark->geo_longitude; ?>"
                                   onchange="ClearlandmarkAndDistrict(); changeLandmarkLayer(<?php echo "'" . $landmark->geo_latitude . "," . $landmark->geo_longitude . "'"; ?>);"><?php echo $landmark->landmark_name; ?>

                        <?php }//end Foreach   ?>
                    </p>
                </div>
            <?php
            }// end if
            // end showing landmarks checkboxes
            ?>
            <div id="map-wrap" class="margbot20">
                <div id="map_canvas"></div>
            </div>
        </div>
        <div class="content_block">
            <h2 id="street_panel_title" style="display:none;"><img src="<?php echo base_url(); ?>images/street-view.png"
                                                                   alt=""/><?php echo _("Street View"); ?></h2>

            <div id="street_panel-wrap" style="display:none;">
                <div id="street_panel"></div>
            </div>
        </div>
        <?php if (!empty($hostel->directions)) { ?>
            <div class="content_block">
                <h2><?php echo _("Directions"); ?></h2>
                <?php if (!empty($hostel->directionsTranslated)) { ?>
                    <select class="select-translate">
                        <option value="translate"><?php echo _("Voir la version traduite"); ?></option>
                        <option value="original"><?php echo _("Voir l'original"); ?></option>
                    </select>
                <?php } ?>
                <?php
                if (!empty($hostel->directionsTranslated)) {
                    echo '<div class="translated">' . strip_tags($hostel->directionsTranslated, '<p>') . '</div>';
                    echo '<div class="original" style="display:none;">' . nl2p(var_check(strip_tags($hostel->directions, '<p>'), ""), false, true) . '</div>';
                } else {
                    echo nl2p(var_check($hostel->directions, ""), false, true);
                }
                ?>
            </div>
        <?php } ?>
    </div>

    <div id="hostel_info_reviews" class="hostels_tab_content ui-tabs-hide">
        <script type="text/javascript">
            function update_review_list() {
                $.ajax(
                    {
                        type: "POST",
                        url: "<?php echo site_url(); ?>property_reviews/<?php echo $hostel->property_number; ?>",
                        success: function (data) {
                            <?php
                            if (!empty($user_reviews)) {
                                ?>
                            $("#remote-comment-list-part").html(data);
                            <?php
                        } else {
                            ?>
                            $("#comment_list_part").html(data);
                            $("#remote-comment-list-part").html("");
                            <?php
                        }
                        ?>
                        }
                    });
            }
            $(document).ready(function () {
                update_review_list();
            });
        </script>



        <?php
        if (!empty($user_reviews)) {
            $this->load->view("hw/reviews", array('user_reviews' => $user_reviews, 'reviews_translation_available' => $reviews_translation_available, 'review_count' => count($user_reviews)));
        } else {
            $this->load->view("review_empty");
        }
        ?>
        <div id="remote-comment-list-part">
            <p><img src="<?php echo base_url(); ?>images/V2/loading-squares.gif" alt=""/></p>
        </div>

    </div>
    </div>
    </div>
<?php endif; //endif api error ?>
<?php
/* Check the fourth param in URL */
if ($this->uri->segment(4, 0)) {
    /* Convert to lower so make it case in sensitive */
    $uri_segement = strtolower($this->uri->segment(4));
    if ($uri_segement == "map") {
        // make the diection tab selected and triger the click event
        echo "<script type='text/javascript'>$(document).ready(function() { $('#hostels_tabs').tabs('select',1); $('#show_full_map').trigger('click'); });</script>";
    } else if ($uri_segement == 'comments') { // make the coments tab selected
        echo "<script type='text/javascript'>$(document).ready(function() { $('#hostels_tabs').tabs('select',2); });</script>";
    } else {
        // do nothing
    }
}
?>

<?php $this->load->view('includes/template-share-email-popup'); ?>
<?php $this->load->view('includes/save_property'); ?>
<?php $this->load->view('includes/template-share-email-popup'); ?>
