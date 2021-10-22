<?php
class ControllerCommonLocation extends Controller {
	public function index() {
		$this->load->language('common/location');

		$data['action'] = $this->url->link('common/location/location', '', $this->request->server['HTTPS']);

		$this->load->model('localisation/location');

		$data['locations'] = array();

		$data['location_id'] = $this->session->data['location_id'];

		$results = $this->model_localisation_location->getLocations();

		foreach ($results as $result) {
				$data['locations'][] = array(
					'location_id'  => $result['location_id'],
					'name'         => $result['name'],
					'address'      => $result['address'],
					'telephone'    => $result['telephone']
				);
		}

		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
		}

		return $this->load->view('common/location', $data);
	}

	public function location() {

		if (isset($this->request->post['location_id'])) {
				
			$this->session->data['location_id'] = $this->request->post['location_id'];
		
		} else {
			$this->session->data['location_id'] = $this->config->get('config_location')[0]; 
		}
	
		if (isset($this->request->post['redirect'])) {
			$this->response->redirect($this->request->post['redirect']);
		} else {
			$this->response->redirect($this->url->link('common/home'));
		}
	}
}