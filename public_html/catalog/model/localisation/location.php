<?php
class ModelLocalisationLocation extends Model {
	public function getLocation($location_id) {
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, fax, image, open, comment, href FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");

		return $query->row;
	}

	public function getLocations() {
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, fax, image, open, comment FROM " . DB_PREFIX . "location ");

		return $query->rows;
	}
	
	public function getLocationsContacts( $location_id = false ) {

        $locations = array();

        if ($location_id == false) {
            $query = $this->db->query("

            SELECT * FROM   " . DB_PREFIX . "user u

            LEFT JOIN " . DB_PREFIX . "user_contacts uc ON (u.user_id = uc.user_id)
            
            WHERE u.location_id IN ( ". implode(",",$this->config->get('config_location')) ." )

            ");

        } else {
            $query = $this->db->query("

            SELECT * FROM   " . DB_PREFIX . "user u

            LEFT JOIN " . DB_PREFIX . "user_contacts uc ON (u.user_id = uc.user_id)
            
            WHERE u.location_id IN ( ". $location_id ." )

            ");

        }

            return  $query->rows;

    } 
    
    public function getLocationsImages( $location_id = false ) {

        $locations = array();


            $query = $this->db->query("

			SELECT  `image` FROM `ckf_location_image_additional` WHERE `location_id`=".$location_id ."


            ");



            return  $query->rows;

    }
}