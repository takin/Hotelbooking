<div class="wrap">
<h2>Administration</h2>

<form method="post" action="<?php bloginfo('url');?>/wp-admin/admin.php?page=auberges_admin/auberges_admin.php">
    <?php //settings_fields( 'baw-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Liste de transactions</th>
        <td>
          From
          <?php

          select_day("transaction_day_from","transaction_day_from","01");
          select_month("transaction_month_from","transaction_month_from","","01");
          select_year("transaction_year_from","transaction_year_from","",-5,1,date("Y"));
          ?>
        </td>
        </tr>
        <tr>
        <td></td>
        <td>
          To&nbsp;&nbsp;&nbsp;&nbsp;
          <?php
          select_day("transaction_day_to","transaction_day_to",date("d"));
          select_month("transaction_month_to","transaction_month_to","",date("m"));
          select_year("transaction_year_to","transaction_year_to","",-5,1,date("Y"));
          ?>
        <input class="button" type="submit" name="csv_transactions" value="Télécharger" />
        </td>
        </tr>

        <tr valign="top">
        <th scope="row">Liste d'usagers</th>
        <td><input class="button" type="submit" name="csv_usagers" value="Télécharger" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Liste de pays pour adwords</th>
        <td>
          <select name="domain_pays">
            <?php
            $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
            $aubergedb->hide_errors();
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);

            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>"><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="adword_pays_lang">
            <option value="fr">French</option>
            <option value="es">Spanish</option>
            <option value="en">English</option>
            <option value="de">German</option>
            <option value="pt">Portuguese</option>
            <option value="zh-CN">Chinese</option>
            <option value="it">Italian</option>
            <option value="pl">Polish</option>
            <option value="ru">Russian</option>
            <option value="no">Norwegian</option>
            <option value="fi">Finnish</option>
            <option value="cs">Czech</option>
            <option value="ko">Korean</option>
            <option value="ja">Japanese</option>
            <option value="hu">Hungarian</option>
          </select>
          <select name="country_api_used">
            <option value="HW">HostelWorld</option>
            <option value="HB">HostelBookers</option>
          </select>
          <input class="button" type="submit" name="csv_adword_pays" value="Télécharger" />
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Liste de ville pour adwords</th>
        <td>
          <select name="domain">
            <?php
            $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
            $aubergedb->hide_errors();
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);

            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>"><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="adword_lang">
            <option value="fr">French</option>
            <option value="es">Spanish</option>
            <option value="en">English</option>
            <option value="de">German</option>
            <option value="pt">Portuguese</option>
            <option value="zh-CN">Chinese</option>
            <option value="it">Italian</option>
            <option value="pl">Polish</option>
            <option value="ru">Russian</option>
            <option value="no">Norwegian</option>
            <option value="fi">Finnish</option>
            <option value="cs">Czech</option>
            <option value="ko">Korean</option>
            <option value="ja">Japanese</option>
            <option value="hu">Hungarian</option>
          </select>
          <select name="city_x_value">
          <?php
          for($val=0;$val < 100;$val++)
          {
            ?>
            <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
            <?php
          }
          ?>

          </select>
          <select name="city_api_used">
            <option value="HW">HostelWorld</option>
            <option value="HB">HostelBookers</option>
          </select>
          <input class="button" type="submit" name="csv_adword_villes" value="Télécharger" /> * La limite de charactères du champ "Custom city name" = le nombre sélectionné
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Liste d'auberges pour adwords</th>
        <td>
          <select name="domain_hostels">
            <?php
            $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
            $aubergedb->hide_errors();
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);

            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>"><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="hostels_currency">
            <option value="EUR">EUR</option>
            <option value="USD">USD</option>
            <option value="GBP">GBP</option>
          </select>
          <select name="x_value">
          <?php
          for($val=0;$val < 100;$val++)
          {
            ?>
            <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
            <?php
          }
          ?>
          </select>
          <select name="hostels_api_used">
            <option value="HW">HostelWorld</option>
            <option value="HB">HostelBookers</option>
          </select>
          <input class="button" type="submit" name="csv_adword_hostels" value="Télécharger" />
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Liste de districts pour adwords</th>
        <td>
          <select name="domain_districts">
            <?php
            $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
            $aubergedb->hide_errors();
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);

            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>"><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="districts_adword_lang">
            <option value="fr">French</option>
            <option value="es">Spanish</option>
            <option value="en">English</option>
            <option value="de">German</option>
            <option value="pt">Portuguese</option>
            <option value="zh-CN">Chinese</option>
            <option value="it">Italian</option>
            <option value="pl">Polish</option>
            <option value="ru">Russian</option>
            <option value="no">Norwegian</option>
            <option value="fi">Finnish</option>
            <option value="cs">Czech</option>
            <option value="ko">Korean</option>
            <option value="ja">Japanese</option>
            <option value="hu">Hungarian</option>
          </select>
          <select name="districts_api_used">
            <option value="HW">HostelWorld</option>
            <option value="HB">HostelBookers</option>
          </select>
          <input class="button" type="submit" name="csv_adword_districts" value="Télécharger" />
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Liste de landmarks pour adwords</th>
        <td>
          <select name="domain_landmarks">
            <?php
            $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
            $aubergedb->hide_errors();
            $sql_query = "SELECT * FROM `site_domains`";
            $results = $aubergedb->get_results($sql_query);

            foreach($results as $row)
            {
              ?>
              <option value="<?php echo $row->site_domain; ?>"><?php echo $row->site_domain; ?></option>
              <?php
            }
            ?>
          </select>
          <select name="landmarks_adword_lang">
            <option value="fr">French</option>
            <option value="es">Spanish</option>
            <option value="en">English</option>
            <option value="de">German</option>
            <option value="pt">Portuguese</option>
            <option value="zh-CN">Chinese</option>
            <option value="it">Italian</option>
            <option value="pl">Polish</option>
            <option value="ru">Russian</option>
            <option value="no">Norwegian</option>
            <option value="fi">Finnish</option>
            <option value="cs">Czech</option>
            <option value="ko">Korean</option>
            <option value="ja">Japanese</option>
            <option value="hu">Hungarian</option>
          </select>
          <select name="landmarks_api_used">
            <option value="HW">HostelWorld</option>
            <option value="HB">HostelBookers</option>
          </select>
          <input class="button" type="submit" name="csv_adword_landmarks" value="Télécharger" />
        </td>
        </tr>
    </table>
</form>
<div>
<table>
<tr><td colspan=2>List of available adwords CSVs</td></tr>
<?php
$uploads = wp_upload_dir();
$dir = $uploads['basedir'];
$files = glob($dir.'/adwords*');
foreach ($files as $file)
{
  ?>
  <tr><td><a href="<?php echo $uploads['baseurl']."/".basename($file); ?>"><?php echo basename($file);?></a></td><td><?php echo round(filesize($file) / 1048576, 2). " MB";?></td></tr>
  <?php
}

?>
</table>
</div>
</div>