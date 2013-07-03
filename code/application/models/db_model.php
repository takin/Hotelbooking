<?php
/**
 * @author Louis-Michel
 *
 */
class Db_model extends CI_Model
{
    var $queries_path = "";
    var $transaction_queries = array();

    function Db_model()
    {
        parent::__construct();

        $this->load->model('Db_links');

        $this->db->simple_query("SET NAMES 'utf8'");

        $this->queries_path = FCPATH.'sql/';
    }

    function get_users()
    {
      $query = $this->db->get('user');

      return $query;
    }

    function get_gender_id($gender)
    {
      if(!is_null($gender))
      {
        $query = "SELECT gender_id FROM genders WHERE gender = '".$gender."' COLLATE utf8_general_ci";
        $query = $this->db->query($query);
        $gender = $query->row();
        return $gender->gender_id;
      }
      return NULL;

    }

    function get_gender_value($gender_id)
    {
      if(!is_null($gender_id))
      {
        $query = "SELECT gender FROM genders WHERE gender_id = '".$gender_id."' COLLATE utf8_general_ci";
        $query = $this->db->query($query);
        $gender = $query->row();
        return $gender->gender;
      }
      return NULL;
    }

    function get_user_profile($user_id)
    {
      $query = $this->db->query('SELECT * FROM user_profiles WHERE user_id = '.$user_id.' LIMIT 1');
      $row = $query->row_array();

      $query = $this->db->query('SELECT email FROM users WHERE id = '.$user_id.' LIMIT 1');
      $row2 = $query->row_array();

      return array_merge($row,$row2);
    }

    /** add_hw_transaction
     *
     * @param $user_id
     * @param $email
     * @param $booking_time
     * @param $customer_booking_reference
     * @param $first_name
     * @param $last_name
     * @param $home_country
     * @param $gender
     * @param $phone_number
     * @param $arrival_date_time date and time of arrival
     * @param $property_number
     * @param $num_nights
     * @param $amount_charged
     * @param $grand_total
     * @param $charged_currency
     * @param $property_amount_due

     *
     *
     *
     *
     */
    function add_hw_transaction(
                $testmode,
                $booking_time,
                $customer_booking_reference,
                $first_name,
                $last_name,
                $home_country,
                $gender,
                $phone_number,
                $arrival_date_time,
                $property_number,
                $property_name,
                $num_nights,
                $amount_charged,
                $charged_currency,
                $property_amount_due,
                $property_currency,
                $email,
                $emailsent,
                $prop_emailsent,
                $RoomDetails,
                $mobile = 0,
                $user_id = NULL)
    {
      //TONOTICE should get instance of ci and load this load model does not work
      $this->load->model('Db_currency');

      $transaction_id = 0;
      //Get gender id
      $query = "SELECT gender_id FROM genders WHERE gender = '".$gender."' COLLATE utf8_general_ci";
      $query = $this->db->query($query);
      $gender = $query->row();

      $charged_currency = $this->Db_currency->get_currency_id($charged_currency);
      $property_currency = $this->Db_currency->get_currency_id($property_currency);

      settype($customer_booking_reference, "string");

      //Initialize booker info
      $booker_country_name = NULL;
      $booker_country_code = NULL;
      $booker_region_code  = NULL;
      $booker_city_name    = NULL;
      $booker_latitude     = NULL;
      $booker_longitude    = NULL;

      if(!empty($this->site_user))
      {
        if(!empty($this->site_user->CountryName))
        {
          $booker_country_name = $this->site_user->CountryName;
        }
        if(!empty($this->site_user->CountryCode))
        {
          $booker_country_code = $this->site_user->CountryCode;
        }
        if(!empty($this->site_user->RegionCode))
        {
          $booker_region_code = $this->site_user->RegionCode;
        }
        if(!empty($this->site_user->City))
        {
          $booker_city_name = $this->site_user->City;
        }
        if(!empty($this->site_user->Latitude))
        {
          $booker_latitude = $this->site_user->Latitude;
        }
        if(!empty($this->site_user->Longitude))
        {
          $booker_longitude = $this->site_user->Longitude;
        }
      }

      //Store data of transactions
      $data = array(
                  'user_id' => $user_id,
                  'mobile' => $mobile,
                  'API_booked' => 'HW',
                  'test_booked' => $testmode,
                  'email' => $email,
                  'email_conf_sent' => $emailsent,
                  'email_prop_sent' => $prop_emailsent,
                  'booking_time' => $booking_time,
                  'customer_booking_reference' => $customer_booking_reference,
                  'first_name' => $first_name,
                  'last_name' => $last_name,
                  'home_country' => $home_country,
                  'gender_id' => $gender->gender_id,
                  'phone_number' => $phone_number,
                  'arrival_date_time' => $arrival_date_time,
                  'property_number' => $property_number,
                  'property_name' => $property_name,
                  'num_nights' => $num_nights,
                  'amount_charged' => $amount_charged,
                  'charged_currency' => $charged_currency,
                  'property_amount_due' => $property_amount_due,
                  'property_currency' => $property_currency,
                  'site_domain_id' => $this->Db_links->get_domain_ID($_SERVER['HTTP_HOST']),
                  'booker_ip' => $this->input->ip_address(),
                  'booker_country_code' => $booker_country_code,
                  'booker_country_name' => $booker_country_name,
                  'booker_city_name' => $booker_city_name,
                  'booker_region_code' => $booker_region_code,
                  'booker_geo_latitude' => $booker_latitude,
                  'booker_geo_longitude' => $booker_longitude
              );

        $this->db->trans_begin();
        if($this->db->insert('transactions_hostelworld', $data) === false)
				{
          log_message("error", "Error trying to add HW transaction to DB for #$customer_booking_reference booked by $first_name, $last_name at $email");
        }

        array_push($this->transaction_queries, $this->db->last_query());

        $transaction_id = $this->db->insert_id();

        $this->db->query("SELECT @last_trans:=LAST_INSERT_ID();");
        array_push($this->transaction_queries, $this->db->last_query());

        //Store rooms details
        $RoomsNumber = Array();

        $rdi = 0;

        $property_grand_total = 0;
        settype($property_grand_total, "float");

        foreach($RoomDetails as $room)
        {
          $property_grand_total += (float)($room->priceSettle)*(float)($room->beds);

        	if(!in_array((string)$room->roomNumber,$RoomsNumber))
        	{
        		$RoomsNumber[$rdi] = (string)$room->roomNumber;
		        $rdi++;

		        $roomTypeDesc = NULL;
		        if(!empty($room->roomTypeDescription))
		        {
		          $roomTypeDesc = $room->roomTypeDescription;
		        }

            if($this->add_rooms_to_transaction( '@last_trans',
				                                        (string)$room->roomNumber,
													                      (string)$room->roomType,
													                      (int)$room->beds,
													                      $room->price,
													                      $room->priceUSD,
													                      $room->priceSettle,
													                      (string)$roomTypeDesc) === false
													                      )
						{
              log_message("error", "Error trying to add HW rooms to transaction to DB for #$customer_booking_reference booked by $first_name, $last_name at $email");
            }
            array_push($this->transaction_queries, $this->db->last_query());
        	}
        }

        $data = array('property_grand_total' => str_replace(',', '.', $property_grand_total));

        $this->db->where('transaction_id = @last_trans', NULL, FALSE);
        $this->db->update('transactions_hostelworld', $data);

        array_push($this->transaction_queries, $this->db->last_query());

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            log_message("error", "DB Transaction failed to insert last HW transaction saving transaction to $customer_booking_reference.sql");
            $this->save_trans_queries_to_file($customer_booking_reference.".sql");
        }

        $this->db->trans_off();

        //clear transaction queries
        $this->transaction_queries = array();

        return $transaction_id;
    }

    /** add_rooms_to_transaction
     *
     * @param $transaction_id
     * @param $room_number
     * @param $room_type
     * @param nb_beds
     * @param $rate
     * @param $rate_usd
     *
     * //Require SQL var @last_trans to be set
     *
     *
     */
    function add_rooms_to_transaction(
                      $transaction_id_sql_var_name,
                      $room_number,
                      $room_type,
                      $nb_beds,
                      $rate,
                      $rate_usd,
                      $rate_settle,
                      $room_type_description = NULL)
    {
      $room_number = $this->db->escape($room_number);
      $room_type   = $this->db->escape($room_type);
      $room_type_description   = $this->db->escape($room_type_description);
      $nb_beds     = $this->db->escape($nb_beds);
      $rate        = $this->db->escape($rate);
      $rate_usd    = $this->db->escape($rate_usd);
      $rate_settle = $this->db->escape($rate_settle);

      $sql = "INSERT INTO `rooms_per_transactions` (`transaction_id`, `room_number`, `room_type`, `room_type_description`, `beds`, `price`, `price_usd`, `price_settle`)";
      $sql.= " VALUES ($transaction_id_sql_var_name, $room_number, $room_type, $room_type_description, $nb_beds, $rate, $rate_usd, $rate_settle)";
      return $this->db->query($sql);

    }

    /** save_trans_queries_to_file
     *
     */
    function save_trans_queries_to_file($query_log_filename)
    {
      //if debug messages are enabled copy API XML to file
      if($query_log_filename!==false)
      {
        log_message('debug', 'Saving SQL queries to file '.$query_log_filename);

        try
        {
          $query_log_filename = $this->queries_path.$query_log_filename;
          $fp = fopen($query_log_filename, 'w');
          if (!$fp) {

              throw new Exception("Problem with opening of $query_log_filename");
          }
          else
          {
            foreach($this->transaction_queries as $transaction_query)
            {
              $fwrite = fwrite($fp, $transaction_query.";\n");
              if ($fwrite === false)
              {
                  throw new Exception("Problem writing SQL to $query_log_filename -> ".$transaction_query);
               }
            }


            fclose($fp);
          }
        }
        catch(Exception $e)
        {
          log_message('error', 'Error saving last transaction queries:'.$e->getMessage());
        }

      }

    }

/** add_hb_transaction
     *
     * @param $user_id
     * @param $email
     * @param $booking_time
     * @param $customer_booking_reference
     * @param $first_name
     * @param $last_name
     * @param $home_country
     * @param $gender
     * @param $phone_number
     * @param $arrival_date_time date and time of arrival
     * @param $property_number
     * @param $num_nights
     * @param $amount_charged
     * @param $grand_total
     * @param $charged_currency
     * @param $property_amount_due

     *
     *
     *
     *
     */
    function add_hb_transaction(
                $testmode,
                $booking_time,
                $customer_booking_reference,
                $first_name,
                $last_name,
                $home_country,
                $male_count,
                $female_count,
                $phone_number,
                $arrival_date_time,
                $property_number,
                $property_name,
                $num_nights,
                $amount_charged,
                $charged_currency,
                $property_amount_due,
                $property_amount_taken,
                $property_currency,
                $email,
                $emailsent,
                $RoomsBooked,
                $book_currency,
                $book_amount,
                $user_id = NULL)
    {
      $this->load->model('Db_currency');

      $transaction_id = 0;

      $charged_currency = $this->Db_currency->get_currency_id($charged_currency);
      $property_currency = $this->Db_currency->get_currency_id($property_currency);
	  $book_currency = $this->Db_currency->get_currency_id($book_currency);

      //Initialize booker info
      $booker_country_name = NULL;
      $booker_country_code = NULL;
      $booker_region_code  = NULL;
      $booker_city_name    = NULL;
      $booker_latitude     = NULL;
      $booker_longitude    = NULL;

      if(!empty($this->site_user))
      {
        if(!empty($this->site_user->CountryName))
        {
          $booker_country_name = $this->site_user->CountryName;
        }
        if(!empty($this->site_user->CountryCode))
        {
          $booker_country_code = $this->site_user->CountryCode;
        }
        if(!empty($this->site_user->RegionCode))
        {
          $booker_region_code = $this->site_user->RegionCode;
        }
        if(!empty($this->site_user->City))
        {
          $booker_city_name = $this->site_user->City;
        }
        if(!empty($this->site_user->Latitude))
        {
          $booker_latitude = $this->site_user->Latitude;
        }
        if(!empty($this->site_user->Longitude))
        {
          $booker_longitude = $this->site_user->Longitude;
        }
      }

      //Store data of transactions
      $data = array(
                  'user_id' => $user_id,
                  'API_booked' => 'HB',
                  'test_booked' => $testmode,
                  'email' => $email,
                  'email_conf_sent' => $emailsent,
                  'booking_time' => $booking_time,
                  'customer_booking_reference' => (string)$customer_booking_reference,
                  'first_name' => $first_name,
                  'last_name' => $last_name,
                  'home_country' => $home_country,
                  'male_count' => $male_count,
                  'female_count' => $female_count,
                  'phone_number' => $phone_number,
                  'arrival_date_time' => $arrival_date_time,
                  'property_number' => $property_number,
                  'property_name' => $property_name,
                  'num_nights' => $num_nights,
                  'amount_charged' => $amount_charged,
                  'charged_currency' => $charged_currency,
                  'property_amount_due' => $property_amount_due,
                  'property_grand_total' => ($property_amount_due+$property_amount_taken),
                  'property_currency' => $property_currency,
                  'site_domain_id' => $this->Db_links->get_domain_ID($_SERVER['HTTP_HOST']),
       			  'booker_ip' => $this->input->ip_address(),
                  'booker_country_code' => $booker_country_code,
                  'booker_country_name' => $booker_country_name,
                  'booker_city_name' => $booker_city_name,
                  'booker_region_code' => $booker_region_code,
                  'booker_geo_latitude' => $booker_latitude,
                  'booker_geo_longitude' => $booker_longitude,
                  'book_currency' => $book_currency,
                  'book_amount' => $book_amount
              );

        if($this->db->insert('transactions_hostelworld', $data) === false)
        {
          log_message("error", "Error trying to add HB transaction to DB for #$customer_booking_reference booked by $first_name, $last_name at $email");
        }

        $transaction_id = $this->db->insert_id();

        //Store rooms details
        $RoomsNumber = Array();

        $rdi = 0;

        for($rm=0;$rm < count($RoomsBooked->room);$rm++)
        {

        	if(!in_array((string)$RoomsBooked->room[$rm]->id,$RoomsNumber))
        	{
        		$RoomsNumber[$rdi] = $RoomsBooked->room[$rm]->id;
		        $rdi++;

            if($this->add_rooms_to_transaction( $transaction_id,
				                                        (string)$RoomsBooked->room[$rm]->id,
													                      (string)$RoomsBooked->room[$rm]->name,
													                      (int)$RoomsBooked->room[$rm]->beds,
													                      $RoomsBooked->room[$rm]->pricePropertyCurrency,
													                      $RoomsBooked->room[$rm]->priceUSD,
													                      $RoomsBooked->room[$rm]->priceGBP) === false)
            {
              log_message("error", "Error trying to add HB rooms to transaction to DB for #$customer_booking_reference booked by $first_name, $last_name at $email");
            }
        	}
        }

        return $transaction_id;
    }


    /*
     *
     *
     */
    function add_keyword_to_transaction($transaction_id, $keyword_id, $keyword_value)
    {
      $data = array(
               'transaction_id' => $transaction_id,
               'keyword_id' => $keyword_id,
               'keyword_value' => $keyword_value
            );

      return $this->db->insert('transaction_keyword', $data);
    }

    /**
     *
     */

    function get_email_of_expired_trip($days_after_comeback = 2, $limit = NULL)
    {
      $query = "SELECT num_nights, arrival_date_time, email, first_name, last_name, site_domain_id, ";
      $query.= "       property_number, property_name, customer_booking_reference, email_review_sent, transaction_id, ";
      $query.= "       DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL num_nights DAY)) AS comeback_days";
      $query.= " FROM transactions_hostelworld";
      $query.= " WHERE DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL num_nights DAY)) >= ".$days_after_comeback;
      $query.= "       AND email_review_sent = 0";
      $query.= " GROUP BY email, property_number, comeback_days";
      $query.= " ORDER BY site_domain_id ASC, comeback_days DESC, transaction_id ASC";
//      print $query;
      if(!empty($limit))
      {
        $query.= " LIMIT ".$limit;
      }
      $query = $this->db->query($query);

      return $query;
    }

    function markReviewEmailSent($email, $property_number, $comeback_days)
    {
      $this->db->set('email_review_sent', 1);
      $this->db->where('email', $email);
      $this->db->where('property_number', $property_number);
      $comeback_days = $this->db->escape($comeback_days);
      $this->db->where('DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL num_nights DAY)) = '. $comeback_days, NULL, FALSE);

      return $this->db->update("transactions_hostelworld");
    }

    /**
     * NOW in db_currency
     *
     */
//    function get_currency_id($currency_code)
//    {
//      settype($currency_code, "string");
//      $currency_id = NULL;
//      if($currency_code != NULL)
//      {
//        $query = "SELECT currency_id FROM currencies WHERE currency_code = '".$currency_code."' COLLATE utf8_general_ci";
//        $query = $this->db->query($query);
//
//        if ($query->num_rows() > 0)
//        {
//          $currency_id = $query->row();
//          $currency_id = $currency_id->currency_id;
//        }
//        else
//        {
//          $data = array('currency_code' => $currency_code);
//
//          $this->db->insert('currencies', $data);
//          $currency_id = $this->db->insert_id();
//        }
//      }
//      return $currency_id;
//
//    }

    /*
     * NOW in db_currency
     */
//    function get_currency_code($currency_id)
//    {
//      if(!is_null($currency_id))
//      {
//        $query = "SELECT currency_code FROM currencies WHERE currency_id = '".$currency_id."' COLLATE utf8_general_ci";
//        $query = $this->db->query($query);
//        $cur = $query->row();
//        return $cur->currency_code;
//      }
//      return NULL;
//    }

    /*** get functions **************************************/

    function get_user_bookings($user_email)
    {
      $user_email = $this->db->escape_str($user_email);

      $sql_query = "SELECT *
                    FROM
                    (
                        (
                            SELECT API_booked, transaction_id,booking_time, transactions_hostelworld.email, first_name, last_name, gender ,home_country,
                                    phone_number, customer_booking_reference, arrival_date_time, transactions_hostelworld.property_number,
                                    transactions_hostelworld.property_name, num_nights,property_grand_total, amount_charged, c1.currency_code AS amount_charged_currency,
                                    property_amount_due, c2.currency_code AS property_currency, c3.currency_code AS book_currency, transactions_hostelworld.book_amount,
                                    IFNULL(hw_hostel.property_type,'property') as property_type,
                                    address1 as property_address1,
                                    address2 as property_address2,
                                    `city_".$this->site_lang."` as property_city,
                                    `country_".$this->site_lang."` as property_country,
                                    hw_hostel.geo_longitude,
                                    hw_hostel.geo_latitude,
                                    phone as property_tel,
                                    hw_hostel.email as property_email,
                                    hw_hostel.imageURL as property_thumb_url

                            FROM transactions_hostelworld
                            LEFT JOIN genders ON transactions_hostelworld.gender_id = genders.gender_id
                            LEFT OUTER JOIN currencies c1 ON transactions_hostelworld.charged_currency = c1.currency_id
                            LEFT OUTER JOIN currencies c2 ON transactions_hostelworld.property_currency = c2.currency_id
                            LEFT OUTER JOIN currencies c3 ON transactions_hostelworld.book_currency = c3.currency_id
                            LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
                            LEFT JOIN hw_city    ON hw_hostel.hw_city_id  = hw_city.hw_city_id
                            LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                            LEFT JOIN cities2 ON hw_country.hw_country = cities2.country_en AND hw_city.hw_city = cities2.city_en
                            WHERE transactions_hostelworld.email LIKE'$user_email'
                            AND API_booked = 'HW'
                            ORDER BY transaction_id DESC
                        ) UNION (
                            SELECT API_booked, transaction_id,booking_time, transactions_hostelworld.email, first_name, last_name, gender ,home_country,
                                    phone_number, customer_booking_reference, arrival_date_time, transactions_hostelworld.property_number,
                                    transactions_hostelworld.property_name, num_nights,property_grand_total, amount_charged, c1.currency_code AS amount_charged_currency,
                                    property_amount_due, c2.currency_code AS property_currency, c3.currency_code AS book_currency, transactions_hostelworld.book_amount,
                                    IFNULL(hb_hostel.property_type,'property') as property_type,
                                    address1 as property_address1,
                                    address2 as property_address2,
                                    IFNULL(`city_".$this->site_lang."`,IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en)) as property_city,
                                    IFNULL(`country_".$this->site_lang."`,(SELECT `country_".$this->site_lang."` FROM cities2 WHERE LOWER(cities2.country_en) LIKE LOWER(hb_country.lname_en) LIMIT 1))  as property_country,
                                    hb_hostel.geo_longitude,
                                    hb_hostel.geo_latitude,
                                    phone as property_tel,
                                    hb_hostel.email as property_email,
                                    (SELECT url FROM hb_hostel_image WHERE hb_hostel_image.hostel_hb_id = hb_hostel.property_number LIMIT 1) as property_thumb_url

                            FROM transactions_hostelworld
                            LEFT JOIN genders ON transactions_hostelworld.gender_id = genders.gender_id
                            LEFT OUTER JOIN currencies c1 ON transactions_hostelworld.charged_currency = c1.currency_id
                            LEFT OUTER JOIN currencies c2 ON transactions_hostelworld.property_currency = c2.currency_id
                            LEFT OUTER JOIN currencies c3 ON transactions_hostelworld.book_currency = c3.currency_id
                            LEFT JOIN hb_hostel ON transactions_hostelworld.property_number = hb_hostel.property_number
                            LEFT JOIN hb_city ON hb_city.hb_id = hb_hostel.city_hb_id
                            LEFT JOIN hb_country ON hb_country.hb_country_id = hb_city.hb_country_id
                            LEFT JOIN cities2 ON hb_country.lname_en = cities2.country_en AND IF(LOCATE(',',hb_city.lname_en)>0,TRIM(LEFT(hb_city.lname_en,LOCATE(',',hb_city.lname_en)-1)),hb_city.lname_en) = cities2.city_en
                            WHERE transactions_hostelworld.email LIKE'$user_email'
                            AND API_booked = 'HB'
                            ORDER BY transaction_id DESC
                        )
                    ) as user_bookings
                    ORDER BY transaction_id DESC";

      return $this->db->query($sql_query);
    }

    function get_last_booking_info($api = 0)
    {

      $sql_query = "SELECT last_booking.property_name, last_booking.property_number, num_nights, SUM(beds) AS guests, IFNULL(property_type,'property') as property_type ";
      $sql_query.= " FROM";
      $sql_query.= " (";
      $sql_query.= "     SELECT transaction_id, property_name, property_number, num_nights";
      $sql_query.= "     FROM transactions_hostelworld ";
      $sql_query.= "     WHERE customer_booking_reference NOT LIKE'%TEST%' AND (test_booked != 1 OR (test_booked IS NULL)) ";
      if($api == HB_API)
      {
        $sql_query.= ' AND API_booked = "HB"';
      }
      else
      {
        $sql_query.= ' AND API_booked = "HW"';
      }
      $sql_query.= "     ORDER BY booking_time DESC";
      $sql_query.= "     LIMIT 1";
      $sql_query.= " ) AS last_booking";
      $sql_query.= " LEFT JOIN rooms_per_transactions ON last_booking.transaction_id = rooms_per_transactions.transaction_id ";

      if($api == HB_API)
      {
        $sql_query.= " LEFT JOIN hb_hostel ON last_booking.property_number = hb_hostel.property_number";
      }
      else
      {
        $sql_query.= " LEFT JOIN hw_hostel ON last_booking.property_number = hw_hostel.property_number";
      }

      $sql_query.= " GROUP BY rooms_per_transactions.transaction_id";

      $sql_query = $this->db->query($sql_query);

      if ($sql_query->num_rows() == 1) return $sql_query->row();
      return NULL;
    }
}

?>