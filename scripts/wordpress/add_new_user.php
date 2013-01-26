<?php
//echo "\r\n" . count($argv) . "\r\n";
//die();
if(count($argv) != 5){ 
    
    die("\r\nMissing userDetails, \r\n example: php add_new_user.php username password me@example.com " . '"firstName LastName"'. " \r\n  \r\n ");
}

// define all databases
$arrDatabases = array(
    array(
        "site_name" => "www.auberges.com",
        "db_name" => "aj_wp_main_fr"
    ), array(
        "site_name" => "www.aubergesdejeunesse.com",
        "db_name" => "aj_wp_fr_aj"
    ),
    array(
        "site_name" => "www.hostales.com",
        "db_name" => "aj_wp_ht_es"
    ),
    array(
        "site_name" => "www.alberguesjuveniles.com",
        "db_name" => "aj_wp_es"
    ),
    array(
        "site_name" => "www.youth-hostel.com",
        "db_name" => "aj_wp_en"
    ),
    array(
        "site_name" => "www.alberguesdajuventude.com",
        "db_name" => "aj_wp_pt"
    ),
    array(
        "site_name" => "www.ostellidellagioventu.com",
        "db_name" => "aj_wp_it"
    ),
    array(
        "site_name" => "www.youth-hostels.jp",
        "db_name" => "aj_wp_ja"
    ),
    array(
        "site_name" => "www.youth-hostels.kr",
        "db_name" => "aj_wp_ko"
    ),
    array(
        "site_name" => "www.jugendherbergen.eu",
        "db_name" => "aj_wp_de"
    ),
    array(
        "site_name" => "www.pousadasdejuventude.com",
        "db_name" => "aj_wp_pj_pt"
    ),
    array(
        "site_name" => "www.alberguesjuveniles.es",
        "db_name" => "aj_wp_es_es"
    ),
    array(
        "site_name" => "www.albergues-pensiones.com",
        "db_name" => "aj_wp_ap_es"
    ),
    array(
        "site_name" => "www.hostels.in",
        "db_name" => "aj_wp_hi"
    ),
    array(
        "site_name" => "www.youth-hostel.co.uk",
        "db_name" => "aj_wp_en_uk"
    ),
    array(
        "site_name" => "www.youth-hostel.hk",
        "db_name" => "aj_wp_zh"
    ),
    array(
        "site_name" => "www.hostele.com",
        "db_name" => "aj_wp_pl"
    ),
    array(
        "site_name" => "www.youth-hostels.ru",
        "db_name" => "aj_wp_ru_yh"
    ),
    array(
        "site_name" => "www.schroniskamlodziezowe.com",
        "db_name" => "aj_wp_pl_sm"
    ),
    array(
        "site_name" => "www.youth-hostels.ca",
        "db_name" => "aj_wp_en_ca"
    ),
    array(
        "site_name" => "www.aubergesdejeunesse.ca",
        "db_name" => "aj_wp_fr_ca"
    ),
    array(
        "site_name" => "www.hostelek.com",
        "db_name" => "aj_wp_hu"
    ),
    array(
        "site_name" => "www.ifjusagiszallasok.com",
        "db_name" => "aj_wp_hu_if"
    ),
    array(
        "site_name" => "www.hostels.ru.com",
        "db_name" => "aj_wp_ru"
    ),
    array(
        "site_name" => "www.retkeilymajoja.com",
        "db_name" => "aj_wp_fi"
    ),
    array(
        "site_name" => "www.hostelleja.com",
        "db_name" => "aj_wp_fi_ho"
    ),
    array(
        "site_name" => "www.hostely.com",
        "db_name" => "aj_wp_cs"
    ),
    array(
        "site_name" => "www.mladeznickeubytovny.com",
        "db_name" => "aj_wp_cs_ml"
    ),
    array(
        "site_name" => "www.herbergen.com",
        "db_name" => "aj_wp_de_he"
    ),
    array(
        "site_name" => "www.xn--e1amhmfp1c.xn--p1ai",
        "db_name" => "aj_wp_ru_xo"
    ),
    array(
        "site_name" => "www.hosteis.com",
        "db_name" => "aj_wp_pt_ho"
    ),
    array(
        "site_name" => "www.hostelli.com",
        "db_name" => "aj_wp_it_ho"
    ),
    array(
        "site_name" => "www.youth-hostels.ie",
        "db_name" => "aj_wp_en_ie"
    ),
    array(
        "site_name" => "www.youth-hostels.co.nz",
        "db_name" => "aj_wp_en_nz"
    ),
    array(
        "site_name" => "www.youth-hostels.eu",
        "db_name" => "aj_wp_en_eu"
    ),
    array(
        "site_name" => "www.youth-hostels.asia",
        "db_name" => "aj_wp_en_asia"
    ),
    array(
        "site_name" => "www.youth-hostels.cn",
        "db_name" => "aj_wp_zh_cn"
    ),
    array(
        "site_name" => "www.hostels.jp",
        "db_name" => "aj_wp_ja_ho"
    ),
    array(
        "site_name" => "www.hostels.mobi",
        "db_name" => "aj_wp_en_mobi"
    ),
    array(
        "site_name" => "www.nofeehostels.com",
        "db_name" => "aj_wp_en_nf"
    ),
    array(
        "site_name" => "www.xn--xn2by4qtje86kn5ezmb.kr",
        "db_name" => "aj_wp_ko_ho"
    ),
    array(
        "site_name" => "www.hbsitetest.com",
        "db_name" => "aj_wp_hb"
    ),
    array(
        "site_name" => "www.hwsitetest.com",
        "db_name" => "aj_wp_hw"
    )
);

//****************************************************************
//$userDetails = array(
//    "user_login" => "Karim",
//    "user_pass" => "mcweb",
//    "user_email" => "Karim5977@gmail.com",
//    "display_name" => "Karim Samir"
//);

$userDetails = array(
    "user_login" => $argv[1],
    "user_pass" => $argv[2],
    "user_email" => $argv[3],
    "display_name" => $argv[4]
);

//****************************************************************

$arrFinalResult = array();
define("HOST", "92.243.25.30");
define("USER", "aj_site");
define("PASSWORD", "2bVHhwjCGQrRnGW2");

// create connection and return it to use it when query
$connection = mySqlConnect();

function mySqlConnect() {

    $connection = mysqli_connect(HOST, USER, PASSWORD);
    if (!$connection) {
        die('Could not connect: ' . mysql_error());
    }
    return $connection;
}

$dirName = date("Y_m_d_G_h_s");

foreach ($arrDatabases as $sites => $siteDetails) {
    echo " ------------Working on " . $siteDetails["db_name"] . " -------------- \n\r";

    echo " Backuping users and usersmeta tables  \n\r";

    
    // create dir to use it to backup tables in it
    if (!is_dir($dirName)) {
        mkdir($dirName);
    }
    
    // backup files using this command
    $output = 'mysqldump --port 3306 -u '.USER.' --password='.PASSWORD.' -h '.HOST.' --add-drop-table ' . $siteDetails["db_name"] .' wp_users wp_usermeta > '. $dirName ."/" .$siteDetails["db_name"].'_wp_users_backup.sql';
     shell_exec($output);    

     
    $arrResult = saveData($connection, $siteDetails["db_name"], $userDetails);

    if ((array_key_exists("failed", $arrResult))) {
        $arrFinalResult["failed"][] = $arrResult + $siteDetails;
    } else {
        $arrFinalResult["success"][] = $arrResult + $siteDetails;
    }
    echo " -------------Finished " . $siteDetails["db_name"] . " -------------- \n\r";
}

// close mysql connection
mysqli_close($connection);

echo "\n\r *******************Final Result ****************************** \n\r";
if (is_array($arrFinalResult["failed"]) && !empty($arrFinalResult["failed"])) {
    foreach ($arrFinalResult["failed"] as $key => $value) {

        echo "Failed inserting User in database: " . $value["db_name"] . "\n\r ";
        echo "Details: " . $value["details"] . "\n\r ";
        echo " ------------------------------------- \n\r";
    }
}

echo "\n\r ************************************************************** \n\r";


function saveData($pConnection, $pDbName, $pUserDetails) {

    $arrResult = array();
    $arrResult["user_exist"] = false;

    // select DB to used it to insert data
    mysqli_select_db($pConnection, $pDbName);


// remove extra spaces from name
    $user_login = mysql_escape_string(trim($pUserDetails["user_login"]));
    $user_pass = md5(mysql_escape_string(trim($pUserDetails["user_pass"])));
    $user_email = mysql_escape_string(trim($pUserDetails["user_email"]));
    $display_name = mysql_escape_string(trim($pUserDetails["display_name"]));


    echo "Checking user exist in Database \n\r ";

    $sql = "select user_login from wp_users where user_login='$user_login'";
    $result = mysqli_query($pConnection, $sql);

    if (!$result) {

        $arrResult["failed"] = true;
        $arrResult["details"] = 'Error: ' . mysql_error();

        echo "Failed Checking user exist in Database because of " . $arrResult["details"] . "\n\r ";

        return $arrResult;
    } else {

        // return number of rows found
        // if 0 then user doesn't exit
        //otherwise this user already exist in DB
        if (mysqli_num_rows($result) > 0) {
            $arrResult["failed"] = true;
            $arrResult["details"] = 'User already exist in DB';

            echo $arrResult["details"] . "\n\r ";
            return $arrResult;
        } else {


            $sql = "INSERT INTO `$pDbName`.`wp_users` 
	( 
	`ID`,
        `user_login`, 
	`user_pass`, 
	`user_nicename`, 
	`user_email`, 
	`user_registered`,  
	`user_status`, 
	`display_name`
	)
	VALUES
	( NULL,
        '$user_login', 
	'$user_pass', 
	'$display_name', 
	'$user_email', 
	Now(), 
	0, 
	'$display_name'
	);
";


            // insert data in MySQL now
            $result = mysqli_query($pConnection, $sql);

            $userID = mysqli_insert_id($pConnection);

            if (!$result || !$userID) {
                $arrResult["failed"] = true;
                $arrResult["details"] = 'Failed inserting user details in table "wp_users"  Error: ' . mysql_error();

                echo $arrResult["details"] . "\n\r ";
                return $arrResult;
            } else {
                $sql = "INSERT INTO `$pDbName`.`wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) 
        VALUES (NULL, $userID, 'wp_capabilities', 'a:1:{s:13:\"administrator\";b:1;}'),
                (NULL, $userID, 'wp_user_level', '10');";

                // insert data in MySQL now
                $result = mysqli_query($pConnection, $sql);

                if (!$result) {
                    $arrResult["failed"] = true;
                    $arrResult["details"] = 'Failed inserting user details in table "wp_usermeta" Error: ' . mysql_error();

                    echo $arrResult["details"] . "\n\r ";

                    $sql_delete = "Delete FROM `$pDbName`.`wp_users` where user_login='$user_login';";

                    echo "Deleting user from table wp_users \n\r ";

                    // insert data in MySQL now
                    $result = mysqli_query($pConnection, $sql_delete);

                    if (!$result) {
                        echo "Failed Deleting user from table wp_users, please delete it manually \n\r ";

                    }
                    else{
                        echo "Success deleting user from table wp_users \n\r ";

                    }
                    echo "FAILED \n\r ";

                    return $arrResult;
                }
                else{
                    echo "Success \n\r ";

                }
            }
        }
    }
}

?>
