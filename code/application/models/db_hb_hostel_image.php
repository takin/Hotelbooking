<?php

class Db_hb_hostel_image extends CI_Model {
	const TABLE  = 'hb_hostel_image';

	function __construct() {
		parent::__construct();
	}

	public function getHostelImages($property_number) {
		$images = array();

		$this->db->where('hostel_hb_id =', (int)$property_number);

		$data = $this->db->get(self::TABLE);
		foreach ($data->result() as $row) {
			if (!empty($row->url)) {
				$images[] = $row->url;
			}
		}

		return $images;
	}
}
