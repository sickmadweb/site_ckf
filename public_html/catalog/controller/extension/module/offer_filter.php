<?php
class ControllerExtensionModuleOfferFilter extends Controller {
	public function index() {
		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}



		$offers_id = end($parts);

		$this->load->model('catalog/offers');

		$offers_info = $this->model_catalog_offers->getOffers($offers_id);

		if ($offers_info) {
			$this->load->language('extension/module/filter');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['action'] = str_replace('&amp;', '&', $this->url->link('product/offers', 'path=' . $this->request->get['path'] . $url));

			if (isset($this->request->get['filter'])) {
				$data['filter_offers'] = explode(',', $this->request->get['filter']);
			} else {
				$data['filter_offers'] = array();
			}

			$this->load->model('catalog/product');

			if (isset($this->request->get['filter'])) {
				$data['filter_category'] = explode(',', $this->request->get['filter']);
			} else {
				$data['filter_category'] = array();
			}

			$data['filter_groups'] = array();

			$filter_groups = $this->model_catalog_offers->getProductsFilters($offers_id);

			if ($filter_groups) {
				foreach ($filter_groups as $filter_group) {
					$childen_data = array();

					foreach ($filter_group['filter'] as $filter) {
						$filter_data = array(
							'filter_offers_id' => $offers_id,
							'filter_filter'      => $filter['filter_id']
						);

						$childen_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : '')
						);
					}

					$data['filter_groups'][] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $childen_data
					);
				}

				return $this->load->view('extension/module/offer_filter', $data);
			}
		}
	}
}