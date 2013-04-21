<?php

class Db_favorite_hostels extends CI_Model {
	const FAVORITE_TABLE  = 'favorite_hostels';

	function __construct() {
		parent::__construct();
	}

	/**
	 *
	 * @param array $params
	 */
	public function saveFav($params) {
		$this->db->set('hostel_hb_id',   (int)$params['propertyNumber']);
		$this->db->set('nights',         (int)$params['nights']);
		$this->db->set('arrival_date',   (string)$params['date']);
		$this->db->set('notes',          (string)$params['notes']);
		$this->db->set('user_id',        (int)$params['userId']);

		if (empty($params['id'])) {
			return $this->db->insert(self::FAVORITE_TABLE);
		}

		$this->db->where('id', (int)$params['id']);

		return $this->db->update(self::FAVORITE_TABLE);
	}

	public function countPropertyNumber($propertyNumber) {
		$this->db->where('hostel_hb_id', (int)$propertyNumber);
		$this->db->from(self::FAVORITE_TABLE);

		return $this->db->count_all_results();
	}
}
