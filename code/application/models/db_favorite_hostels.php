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
		$this->load->model('Db_links');

		$allData = array();

		// HB
		$data = $this->db->query("
			select favorite_hostels.*, hb_hostel.*, hb_city.lname_en, hb_country.lname_en as country
			from favorite_hostels
			join hb_hostel on (hb_hostel.property_number = favorite_hostels.hostel_hb_id)
			join hb_city on (hb_city.hb_id = hb_hostel.city_hb_id)
			join hb_country on (hb_city.hb_country_id = hb_country.hb_country_id)
			where favorite_hostels.user_id = $userId
		");

		foreach ($data->result() as $row) {
			$allData[] = array(
				'id'                => $row->id,
				'property_page_url' => $this->Db_links->build_property_page_link($row->property_type, $row->property_name, $row->hb_hostel_id),
				'hostel_hb_id'      => $row->hostel_hb_id,
				'name'              => $row->property_name,
				'property_number'   => $row->property_number,
				'arrival_date'      => $row->arrival_date,
				'nights'            => $row->nights,
				'notes'             => $row->notes,
				'city'              => $row->lname_en,
				'country'           => $row->country
			);
		}
return $allData;
		$this->db->flush_cache();
//		$this->db->_reset_write();

		// HW
		$data = $this->db->query("
			select favorite_hostels.*, hw_hostel.*, hw_city.hw_city, hw_country.hw_country
			from favorite_hostels
			join hw_hostel on (hw_hostel.property_number = favorite_hostels.hostel_hb_id)
			join hw_city on (hw_city.hw_city_id = hw_hostel.hw_city_id)
			join hw_country on (hw_city.hw_country_id = hw_country.hw_country_id)
			where favorite_hostels.user_id = $userId
		");

		foreach ($data->result() as $row) {
			$allData[] = array(
				'id'             => $row->id,
				'propertyName'   => $row->property_name,
				'propertyNumber' => $row->property_number,
				'arrivalDate'    => $row->arrival_date,
				'nights'         => $row->nights,
				'notes'          => $row->notes,
				'city'           => $row->hw_city,
				'country'        => $row->hw_country
			);
		}

		return $allData;
	}
}
