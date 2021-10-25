<?php
class ControllerCommonPhone extends Controller {
	public function index() {

		if (!isset($this->request->cookie['phone']) or $this->request->cookie['phone'] == 'NULL' ) {
	//		$phone = explode(";", $this->config->get('config_telephone'));

			$data['locations_contacts'] = array();

			$this->load->model('localisation/location');

			$phone = $this->model_localisation_location->getLocationsContacts();	
	
			$data['phone'] = $phone[array_rand($phone, 1)]['phone'] ;
			$data['email'] = $phone[array_rand($phone, 1)]['email'] ;
		//	print_r($data['phone']['phone']);
			$data['href_phone']= preg_replace("/[^,.0-9]/", '', $data['phone']);
			setcookie('phone', $data['phone'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
			setcookie('email', $data['email'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);			
		}	else {
			$data['href_phone']= preg_replace("/[^,.0-9]/", '', $this->request->cookie['phone']);
			$data['phone']= $this->request->cookie['phone'];

            if (!isset($this->request->cookie['email']) or $this->request->cookie['email'] == 'NULL') {
				$phone = $this->model_localisation_location->getLocationsContacts();	
				$data['email'] = $phone[array_rand($phone, 1)]['email'] ;
            } else {
				$data['email']= $this->request->cookie['email'];
			}

		}

		return $this->load->view('common/phone', $data);
		
	}

	public function reboot( $location_id ) {

	//		$phone = explode(";", $this->config->get('config_telephone'));

	$data['locations_contacts'] = array();

	$this->load->model('localisation/location');

	$phone = $this->model_localisation_location->getLocationsContacts( $location_id );	

	$data['phone'] = $phone[array_rand($phone, 1)]['phone'] ;
	$data['email'] = $phone[array_rand($phone, 1)]['email'] ;
//	print_r($data['phone']['phone']);
	$data['href_phone']= preg_replace("/[^,.0-9]/", '', $data['phone']);
	setcookie('phone', $data['phone'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);


}

}