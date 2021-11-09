<?php
class ControllerReportTraffic extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('report/traffic');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('report/traffic');

		$this->getList();	
	}
	

	
	public function getList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('report/traffic', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['reports'] = array();
		
		$this->load->model('report/traffic');
		$this->load->model('setting/store');


		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {

			
			$result = $this->model_report_traffic->getReports($store['store_id']);
	

			
			if ($result) {
				$data['reports'][] = array(
					'site'	       => $result['site'],	
					'store_id'     => $result['store_id'],
					'users'        => $result['users'],
					'repeated'     => $result['repeated'],		
					'telephone'    => $result['telephone'],		
					'contacts'     => $result['contacts'],		
					'viber'        => $result['viber'],		
					'whatsapp'     => $result['whatsapp'],		
					'checkout' 	   => $result['checkout'],		
					'orders'       => $result['orders'],		
					'email'        => $result['email'],		
					'configurator' => $result['configurator'],		
					'time_less_1'  => $result['time_less_1'],	
					'time_1m_5m'   => $result['time_1m_5m'],	
					'time_more_5m' => $result['time_more_5m'],	
	
				);
			}


		}
				
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
							
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('report/traffic', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'report/traffic')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}	
}