<?php
class ControllerStartupLocation extends Controller {
	public function index() {

		if (empty($this->session->data['location_id'])) {

			$this->load->model('localisation/location');

			$locations = $this->model_localisation_location->getLocations();
	
			$this->session->data['location_id']	= $locations[0]['location_id'];	

		} 


	}
}