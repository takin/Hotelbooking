<?php
/**
 * @author Louis-Michel
 *
 */
class Db_sms extends CI_Model
{
  const SMS_TABLE          = 'sms';
  const TRANSACTION_TABLE  = 'transactions_hostelworld';

  function Db_sms()
  {
      parent::__construct();
  }

  function add_sms($transaction_id, $status = 0, $days_from_arrival = NULL, $msg_id = NULL, $error_code = NULL)
  {
    $this->db->set('transaction_id', $transaction_id);
    $this->db->set('status', $status);
    $this->db->set('msg_id', $msg_id);
    $this->db->set('error_code', $error_code);
    $this->db->set('days_before_arrival', $days_from_arrival);

    return $this->db->insert(self::SMS_TABLE);
  }

  function update_sms($transaction_id, $status = 0, $days_from_arrival = NULL, $msg_id = NULL, $error_code = NULL)
  {
    $this->db->set('status', $status);
    $this->db->set('msg_id', $msg_id);
    $this->db->set('error_code', $error_code);
    $this->db->set('days_before_arrival', $days_from_arrival);

    $this->db->where('transaction_id', $transaction_id);
    return $this->db->update(self::SMS_TABLE);
  }

  function get_sms_list($days_from_today = 0)
  {
    $days_from_today = $this->db->escape($days_from_today);

    $query = "(";
    $query.= "SELECT transactions_hostelworld.site_domain_id,API_booked,site_domain,
                     API_booked,sms_id, ".self::SMS_TABLE.".transaction_id,
                     msg_id, error_code, days_before_arrival, arrival_date_time,
                     customer_booking_reference, transactions_hostelworld.property_number, phone_number,
                     DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL - days_before_arrival DAY)) AS alert_day,
                     transactions_hostelworld.property_name,
                     hw_hostel.phone as property_phone,
                     IF(LOCATE(',',hw_hostel.email)>0,TRIM(LEFT(hw_hostel.email,LOCATE(',',hw_hostel.email)-1)),hw_hostel.email) as property_email
              FROM ".self::SMS_TABLE."
              LEFT JOIN ".self::TRANSACTION_TABLE." ON ".self::SMS_TABLE.".transaction_id = ".self::TRANSACTION_TABLE.".transaction_id
              LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
              LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
              WHERE status < 1
              	AND API_booked = 'HW'
                AND error_code IS NULL
							  AND DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL - days_before_arrival DAY)) >= $days_from_today
    					ORDER BY site_domain_id ASC";
    $query.= " ) UNION ( ";
    $query.= "SELECT transactions_hostelworld.site_domain_id,API_booked,site_domain,
                       API_booked,sms_id, ".self::SMS_TABLE.".transaction_id,
                       msg_id, error_code, days_before_arrival, arrival_date_time,
                       customer_booking_reference, transactions_hostelworld.property_number, phone_number,
                       DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL - days_before_arrival DAY)) AS alert_day,
                       transactions_hostelworld.property_name,
                       hb_hostel.phone as property_phone,
                       IF(LOCATE(',',hb_hostel.email)>0,TRIM(LEFT(hb_hostel.email,LOCATE(',',hb_hostel.email)-1)),hb_hostel.email) as property_email
                FROM ".self::SMS_TABLE."
                LEFT JOIN ".self::TRANSACTION_TABLE." ON ".self::SMS_TABLE.".transaction_id = ".self::TRANSACTION_TABLE.".transaction_id
                LEFT JOIN site_domains ON transactions_hostelworld.site_domain_id = site_domains.site_domain_id
                LEFT JOIN hb_hostel ON transactions_hostelworld.property_number = hb_hostel.property_number
                WHERE status < 1
                  AND API_booked = 'HB'
                  AND error_code IS NULL
  							  AND DATEDIFF(current_date(),ADDDATE(arrival_date_time, INTERVAL - days_before_arrival DAY)) >= $days_from_today
      					ORDER BY site_domain_id ASC";
    $query.= " )";
    $query = $this->db->query($query);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }

    return array();
  }
}