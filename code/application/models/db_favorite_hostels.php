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

		if (!empty($params['city'])) {
			$this->db->set('city', (string)$params['city']);
		}
		if (!empty($params['country'])) {
			$this->db->set('country', (string)$params['country']);
		}
		if (!empty($params['propertyUrl'])) {
			$this->db->set('property_url', (string)$params['propertyUrl']);
		}
		if (!empty($params['propertyName'])) {
			$this->db->set('property_name', (string)$params['propertyName']);
		}
		if (isset($params['isHB'])) {
			$this->db->set('type', $params['isHB'] ? 1 : 0);
		}

		if (empty($params['id'])) {
			$this->db->set('added', date('Y-m-d H:m:d'));

			return $this->db->insert(self::FAVORITE_TABLE);
		}

		$this->db->where('id', (int)$params['id']);

		return $this->db->update(self::FAVORITE_TABLE);
	}

	public function countPropertyNumber($id, $propertyNumber, $type) {
		if ($id) {
			$this->db->where('id !=', (int)$id);
		}

		$this->db->where('hostel_hb_id', (int)$propertyNumber);
		$this->db->where('type', (int)$type);

		$this->db->from(self::FAVORITE_TABLE);

		return $this->db->count_all_results();
	}

	public function savedPropertiesNumbers($userId) {
		$numbers = array();

		$this->db->where('id', (int)$userId);

		$data = $this->db->get(self::FAVORITE_TABLE);

		foreach ($data->result() as $row) {
			$numbers["{$row->hostel_hb_id}"] = $row->hostel_hb_id;
		}

		return $numbers;
	}

	public function getAll($userId) {
		$allData = array();

		$data = $this->db->query("
			select favorite_hostels.*
			from favorite_hostels
			where favorite_hostels.user_id = $userId
		");

		foreach ($data->result() as $row) {
			$allData[] = array(
				'id'                => $row->id,
				'property_page_url' => $row->property_url,
				'hostel_hb_id'      => $row->hostel_hb_id,
				'name'              => $row->property_name,
				'property_number'   => $row->hostel_hb_id,
				'arrival_date'      => $row->arrival_date,
				'arrival_date_show' => date('d F Y', strtotime($row->arrival_date . ' 00:00:00')),
				'nights'            => $row->nights,
				'notes'             => $row->notes,
				'city'              => $row->city,
				'country'           => $row->country
			);
		}

		return $allData;
	}

	public function removeProperty($id, $userId) {
		$this->db->where('id', (int)$id);
		$this->db->where('user_id', (int)$userId);

		return $this->db->delete(self::FAVORITE_TABLE);
	}
}
