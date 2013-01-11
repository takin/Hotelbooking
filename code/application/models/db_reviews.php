<?php
/**
 * @author Louis-Michel
 *
 */

class Db_reviews extends CI_Model
{
    const REVIEW_TABLE    = 'wp_ext_hw_reviews';
    const WP_REVIEW_TABLE = 'wp_comments';
    
    var $wpblogDB;
    var $aubergeDB;
    
    var $api_used_code = 'HW';
    
    function Db_reviews()
    {
      parent::__construct();
      $this->wpblogDB  = $this->load->database('wpblog_reviews', TRUE);
      $this->aubergeDB = $this->load->database('default', TRUE);

      $this->wpblogDB->simple_query("SET NAMES 'utf8'");
      $this->aubergeDB->simple_query("SET NAMES 'utf8'");
      
      if($this->api_used == HB_API)
      {
        $this->api_used_code = "HB";
      }
      else 
      {
        $this->api_used_code = "HW";
      }
    }
    //TODO change approved to match WP databases varchar type
    function get_user_reviews($email,$approved = 0)
    {
      
      $this->wpblogDB->join(self::WP_REVIEW_TABLE, self::WP_REVIEW_TABLE.'.comment_ID = '.self::REVIEW_TABLE.'.wp_comment_id');
      
      if($approved > 0)
      {
        $this->wpblogDB->where('comment_approved', $approved);
      }
       
      $this->wpblogDB->where('comment_author_email', $email);
      $this->wpblogDB->where('API_used', $this->api_used_code);
      $this->wpblogDB->order_by('comment_date DESC');
        
      return $this->wpblogDB->get(self::REVIEW_TABLE);
      
    }
    function get_property_reviews($property_number,$approved = '1')
    {

      $this->wpblogDB->join(self::WP_REVIEW_TABLE, self::WP_REVIEW_TABLE.'.comment_ID = '.self::REVIEW_TABLE.'.wp_comment_id');
      
      $this->wpblogDB->where("comment_approved LIKE'1'");
      
      $this->wpblogDB->where('property_number', $property_number);
      $this->wpblogDB->where('API_used', $this->api_used_code);
      $this->wpblogDB->order_by('comment_date DESC');  
      $our_reviews = $this->wpblogDB->get(self::REVIEW_TABLE);
      
      $return_reviews = array();
      
      if($this->api_used == HB_API)
      {
        if((!is_null($our_reviews))&&($our_reviews->num_rows()>0))
        {
          $index = 0;
          foreach($our_reviews->result() as $our_review)
          {
            $return_reviews[$index]["author_name"]     = $our_review->firstname;
            $return_reviews[$index]["review_date"]     = $our_review->comment_date;
            $return_reviews[$index]["author_country"]  = $our_review->user_country;
            $return_reviews[$index]["review_likebest"] = $our_review->comment_content;
            $return_reviews[$index]["review_likebest_translated"] = "";
            $return_reviews[$index]["review_rating"]   = $our_review->user_rating*10;
            $index++;
          }
        }
      }
      else
      {
        if((!is_null($our_reviews))&&($our_reviews->num_rows()>0))
        {
          $index = 0;
          foreach($our_reviews->result() as $our_review)
          {
            $return_reviews[$index]["author_name"]     = $our_review->firstname;
            $return_reviews[$index]["review_date"]     = $our_review->comment_date;
            $return_reviews[$index]["author_country"]  = $our_review->user_country;
            $return_reviews[$index]["review"]          = $our_review->comment_content;
            $return_reviews[$index]["review_translated"] = "";
            $return_reviews[$index]["review_rating"]   = $our_review->user_rating*10;
            $return_reviews[$index]["review_source"]   = "LOCAL";
            $index++;
          }
        }
      }
      return $return_reviews;
    }
    
    function get_property_avg_rating($property_number,$approved = '1')
    {
      $query = "SELECT property_number, TRUNCATE(AVG(user_rating)*10,0) as average_rating, count(user_rating) as rating_count";
      $query.= " FROM ".self::REVIEW_TABLE;
      $query.= " JOIN ".self::WP_REVIEW_TABLE." ON ".self::WP_REVIEW_TABLE.".comment_ID = ".self::REVIEW_TABLE.".wp_comment_id";
      $query.= " WHERE comment_approved LIKE '1' AND property_number = ".$property_number;
      $query.= " AND API_used = '".$this->api_used_code."'";
      $query.= " GROUP BY property_number";
      
      $query = $this->wpblogDB->query($query);
      if ($query->num_rows() == 1) return $query->row();
      return NULL;
    }
    /*
    function get_top_ratings($qty = 10, $property_type = NULL)
    {
      $where = "WHERE approved = 1";
      if(!is_null($property_type))
      {
        $where = " AND property_type LIKE('".$property_type."')";
      }
      
      $query = "SELECT property_number, property_type,AVG(user_rating) as average_rating, count(user_rating) as rating_count FROM ".self::REVIEW_TABLE." ".$where." GROUP BY property_number ORDER BY average_rating DESC, rating_count DESC LIMIT ".$qty;
      $query = $this->_wpblogDB->query($query);
      return $query;
    }
    */
    function add_property_review($email,
                                 $firstname,
                                 $lastname,
                                 $user_country,
                                 $property_number,
                                 $property_name,
                                 $property_city,
                                 $property_country,
                                 $property_type,
                                 $user_review,
                                 $user_rating,
                                 $date_visited,
                                 $user_IP,
                                 $user_agent )
    {
      
      $data = array(
                  'firstname' => $firstname,
                  'lastname' => $lastname,
                  'user_country' => $user_country,
                  'user_rating' => $user_rating,
                  'user_visited' => $date_visited,
                  'property_number' => $property_number,
                  'property_name' => $property_name,
                  'property_city' => $property_city,
                  'property_country' => $property_country,
                  'property_type' => $property_type,
                  'site_domain' => $_SERVER['HTTP_HOST'],
                  'API_used' => $this->api_used_code
              );

      $this->wpblogDB->insert(self::REVIEW_TABLE, $data);
      
      $reviewID = $this->wpblogDB->insert_id();
      
      $wpCommentID = $this->_wp_add_comment( $this->_wp_get_option('aj_post_rating'),
                                             $firstname.' '.$lastname,
                                             $email,
                                             date('Y-m-d H:i:s'),
                                             $user_review,
                                             0,
                                             $user_IP,
                                             $user_agent);
      
      
//      $this->_wp_add_comment_metadata($wpCommentID, "review_id", $reviewID );
      
      $this->wpblogDB->where('review_id', $reviewID);
      $this->wpblogDB->update(self::REVIEW_TABLE, array('wp_comment_id' => $wpCommentID)); 
      
      return $wpCommentID;
    }

    function _wp_add_comment_metadata($comment_id, $meta_key, $meta_value )
    {
      $data = array(
                  'comment_id' => $comment_id,
                  'meta_key' => $meta_key,
                  'meta_value' => $meta_value,
              );

      $this->wpblogDB->insert("wp_commentmeta", $data);
      
      return $this->wpblogDB->insert_id();
    }
    
    function _wp_get_option($option_name)
    {
      $this->wpblogDB->where('option_name', $option_name);
      $query = $this->wpblogDB->get('wp_options');
      
      if ($query->num_rows() == 1) return $query->row()->option_value;
      return NULL;
    }
    
    /*
     * 
     * WP database structure
     * 
     * comment_post_ID  
     * comment_author 
     * comment_author_email 
     * comment_author_url 
     * comment_author_IP  
     * comment_date 
     * comment_date_gmt 
     * comment_content  
     * comment_karma   
     * comment_approved  
     * comment_agent  
     * comment_type  
     * comment_parent  
     * user_id
     * 
     * 
     */
    function _wp_add_comment($comment_post_ID,
                             $comment_author,
                             $comment_author_email,
                             $comment_date,
                             $comment_content,
                             $comment_approved,
                             $comment_author_IP,
                             $comment_agent)
    {
      $data = array(
                    'comment_post_ID' => $comment_post_ID,
                    'comment_author' => $comment_author,
                    'comment_author_email' => $comment_author_email,
                    'comment_date' => $comment_date,
                    'comment_content' => $comment_content,
                    'comment_approved' => $comment_approved,
                    'comment_author_IP' => $comment_author_IP,
                    'comment_agent' => $comment_agent
                    );

      $this->wpblogDB->insert(self::WP_REVIEW_TABLE, $data);
      
      return $this->wpblogDB->insert_id();
    }
}