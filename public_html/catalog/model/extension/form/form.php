<?php
class ModelMailForm extends Model {
	public function getInsertLogMassage( $data ) {

		$query = $this->db->query("

		INSERT INTO " . DB_PREFIX . "logs_forms 
		
		( `forms`,`date`,  `comment`, `contact_from`, `page`, store_id, location_id, employee, buyer) VALUES 
		
		( 
			'". (isset($data['forms']) ? $data['forms'] : ' ' ) ."',
			NOW(),
			'". (isset($data['comment']) ? $data['comment'] : ' ' ) ."',
			'". ( isset($data['contact']) ? $data['contact'] : ' ' ) ."',
			'". ($data['page'] ? $data['page'] : ' ' ) ."',
			'" . (int)$this->config->get('config_store_id') . "',
			'" . $this->session->data['location_id'] . "',
			'". (isset($data['employee']) ? $data['employee'] : ' ' ) ."',
			'". (isset($data['buyer']) ? $data['buyer'] : ' ' ) ."'
		)

		");

		return true;
	}

	public function getSalesEmails( $location_id = 0 ) {

		$query = $this->db->query("
		
		SELECT GROUP_CONCAT(`email`) AS emails FROM " . DB_PREFIX . "user
		WHERE location_id = '". $location_id ."'

		");

		return $query->row;
	
	}

	public function getHeadEmail( $location_id = 0 ) {
		
		$query = $this->db->query("
		
			SELECT * FROM `" . DB_PREFIX . "location` 
			WHERE `location_id`='". $location_id ."'
		

		");
		
		return $query->row;
		
	}
	
	
}