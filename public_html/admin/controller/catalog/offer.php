<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCatalogOffer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offer');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_offer->addOffer($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_price_min'])) {
				$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
			}
			
			if (isset($this->request->get['filter_price_max'])) {
				$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_quantity_min'])) {
				$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
			}
			
			if (isset($this->request->get['filter_quantity_max'])) {
				$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
				if (isset($this->request->get['filter_sub_category'])) {
					$url .= '&filter_sub_category';
				}
			}
			
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			
			if (isset($this->request->get['filter_noindex'])) {
				$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
			}

			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_offer->editOffer($this->request->get['offer_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_price_min'])) {
				$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
			}
			
			if (isset($this->request->get['filter_price_max'])) {
				$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_quantity_min'])) {
				$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
			}
			
			if (isset($this->request->get['filter_quantity_max'])) {
				$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
				if (isset($this->request->get['filter_sub_category'])) {
					$url .= '&filter_sub_category';
				}
			}
			
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			
			if (isset($this->request->get['filter_noindex'])) {
				$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
			}
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offer');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $offer_id) {
				$this->model_catalog_offer->deleteOffer($offer_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_price_min'])) {
				$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
			}
			
			if (isset($this->request->get['filter_price_max'])) {
				$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_quantity_min'])) {
				$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
			}
			
			if (isset($this->request->get['filter_quantity_max'])) {
				$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
				if (isset($this->request->get['filter_sub_category'])) {
					$url .= '&filter_sub_category';
				}
			}
			
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			
			if (isset($this->request->get['filter_noindex'])) {
				$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
			}

			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/offer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offer');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $offer_id) {
				$this->model_catalog_offer->copyOffer($offer_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}
			
			if (isset($this->request->get['filter_price_min'])) {
				$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
			}
			
			if (isset($this->request->get['filter_price_max'])) {
				$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
			
			if (isset($this->request->get['filter_quantity_min'])) {
				$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
			}
			
			if (isset($this->request->get['filter_quantity_max'])) {
				$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
				$url .= '&filter_category=' . $this->request->get['filter_category'];
				if (isset($this->request->get['filter_sub_category'])) {
					$url .= '&filter_sub_category';
				}
			}
			
			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}
			
			if (isset($this->request->get['filter_noindex'])) {
				$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
			}
			
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = '';
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$filter_price_min = $this->request->get['filter_price_min'];
		} else {
			$filter_price_min = null;
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$filter_price_max = $this->request->get['filter_price_max'];
		} else {
			$filter_price_max = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = '';
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$filter_quantity_min = $this->request->get['filter_quantity_min'];
		} else {
			$filter_quantity_min = null;
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$filter_quantity_max = $this->request->get['filter_quantity_max'];
		} else {
			$filter_quantity_max = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}
		
		$filter_sub_category = null;
		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
			if (!empty($filter_category) && isset($this->request->get['filter_sub_category'])) {
				$filter_sub_category = true;
			} elseif (isset($this->request->get['filter_sub_category'])) {
				unset($this->request->get['filter_sub_category']);
			}
		} else {
			$filter_category = null;
			if (isset($this->request->get['filter_sub_category'])) {
				unset($this->request->get['filter_sub_category']);
			}
		}
		
		$filter_category_name = null;
		if (isset($filter_category)) {
			if ($filter_category>0) {
				$this->load->model('catalog/category');
				

				
				$category = $this->model_catalog_category->getCategory($filter_category);
				if ($category) {
					$filter_category_name = ($category['path']) ? $category['path'] . ' &gt; ' . $category['name'] : $category['name'];
				} else {
					$filter_category = null;
					unset($this->request->get['filter_category']);
					$filter_sub_category = null;
					if (isset($this->request->get['filter_sub_category'])) {
						unset($this->request->get['filter_sub_category']);
					}
				}
			} else {
				$filter_category_name = $this->language->get('text_none_category');
			}
		}
		
		$filter_manufacturer_id = null;
		$filter_manufacturer_name = '';
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = (int)$this->request->get['filter_manufacturer_id'];
			if($filter_manufacturer_id > 0) {
				$this->load->model('catalog/manufacturer');
				$manufacturer = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);
				if ($manufacturer) {
					$filter_manufacturer_name = $manufacturer['name'];
				} else {
					$filter_manufacturer_name = null;
					unset($this->request->get['filter_manufacturer_id']);
				}
			} else {
				$filter_manufacturer_id = 0;
				$filter_manufacturer_name =  $this->language->get('text_none_manufacturer');
			}
		}
		
		/*
		$filter_manufacturer_name = null;
		if (isset($filter_manufacturer)) {
			if ($filter_manufacturer>0) {
				$this->load->model('catalog/manufacturer');
				$manufacturer = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer);
				if ($manufacturer) {
					$filter_manufacturer_name = $manufacturer['name'];
				} else {
					$filter_manufacturer = null;
					unset($this->request->get['filter_manufacturer']);
				}
			} elseif ($filter_manufacturer==0) {
				$filter_manufacturer_name = $this->language->get('text_none_manufacturer');
			}
		}
		*/
		
		if (isset($this->request->get['filter_noindex'])) {
			$filter_noindex = $this->request->get['filter_noindex'];
		} else {
			$filter_noindex = '';
		}
		
		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category';
			}
		}
		
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		
		if (isset($this->request->get['filter_noindex'])) {
			$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
		}
		
		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/offer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/offer/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/offer/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/offer/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/offer/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['offers'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_price_min'=> $filter_price_min,
			'filter_price_max'=> $filter_price_max,
			'filter_quantity' => $filter_quantity,
			'filter_quantity_min' 	=> $filter_quantity_min,
			'filter_quantity_max' 	=> $filter_quantity_max,
			'filter_status'   		=> $filter_status,
			'filter_category'		=> $filter_category,
			'filter_sub_category'	=> $filter_sub_category,
			'filter_manufacturer_id'=> $filter_manufacturer_id,
			'filter_noindex' 		=> $filter_noindex,
			'filter_image' 		    => $filter_image,
			'sort'            		=> $sort,
			'order'           		=> $order,
			'start'           		=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           		=> $this->config->get('config_limit_admin')
		);
		

		$this->load->model('tool/image');

		$offer_total = $this->model_catalog_offer->getTotalOffers($filter_data);

		$results = $this->model_catalog_offer->getOffers($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$offer_specials = $this->model_catalog_offer->getOfferSpecials($result['offer_id']);

			foreach ($offer_specials  as $offer_special) {
				if (($offer_special['date_start'] == '0000-00-00' || strtotime($offer_special['date_start']) < time()) && ($offer_special['date_end'] == '0000-00-00' || strtotime($offer_special['date_end']) > time())) {
					$special = $this->currency->format($offer_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['offers'][] = array(
				'offer_id' => $result['offer_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled_short') : $this->language->get('text_disabled_short'),
				'noindex'    => $result['noindex'] ? $this->language->get('text_enabled_short') : $this->language->get('text_disabled_short'),
				'href_shop'  => HTTP_CATALOG . 'index.php?route=product/offer&offer_id=' . $result['offer_id'],
				'edit'       => $this->url->link('catalog/offer/edit', 'user_token=' . $this->session->data['user_token'] . '&offer_id=' . $result['offer_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category';
			}
		}
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		
		if (isset($this->request->get['filter_noindex'])) {
			$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
		}
		
		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_noindex'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.noindex' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category';
			}
		}
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		
		if (isset($this->request->get['filter_noindex'])) {
			$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
		}
		
		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}	
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $offer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($offer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($offer_total - $this->config->get('config_limit_admin'))) ? $offer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $offer_total, ceil($offer_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_price_min'] = $filter_price_min;
		$data['filter_price_max'] = $filter_price_max;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_quantity_min'] = $filter_quantity_min;
		$data['filter_quantity_max'] = $filter_quantity_max;
		$data['filter_status'] = $filter_status;
		$data['filter_category_name'] = $filter_category_name;
		$data['filter_category'] = $filter_category;
		$data['filter_sub_category'] = $filter_sub_category;
		$data['filter_manufacturer_name'] = $filter_manufacturer_name;
		$data['filter_manufacturer_id'] = $filter_manufacturer_id;
		$data['filter_noindex'] = $filter_noindex;
		$data['filter_image'] = $filter_image;
		
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/offer_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['offer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
		
		if (isset($this->error['meta_h1'])) {
			$data['error_meta_h1'] = $this->error['meta_h1'];
		} else {
			$data['error_meta_h1'] = array();
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}
		
		if (isset($this->request->get['filter_price_min'])) {
			$url .= '&filter_price_min=' . $this->request->get['filter_price_min'];
		}
		
		if (isset($this->request->get['filter_price_max'])) {
			$url .= '&filter_price_max=' . $this->request->get['filter_price_max'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}
		
		if (isset($this->request->get['filter_quantity_min'])) {
			$url .= '&filter_quantity_min=' . $this->request->get['filter_quantity_min'];
		}
		
		if (isset($this->request->get['filter_quantity_max'])) {
			$url .= '&filter_quantity_max=' . $this->request->get['filter_quantity_max'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category';
			}
		}
		
		if (isset($this->request->get['filter_manufacturer_id'])) {
			$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
		}
		
		if (isset($this->request->get['filter_noindex'])) {
			$url .= '&filter_noindex=' . $this->request->get['filter_noindex'];
		}
		
		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['offer_id'])) {
			$data['action'] = $this->url->link('catalog/offer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/offer/edit', 'user_token=' . $this->session->data['user_token'] . '&offer_id=' . $this->request->get['offer_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['offer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$offer_info = $this->model_catalog_offer->getOffer($this->request->get['offer_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['offer_description'])) {
			$data['offer_description'] = $this->request->post['offer_description'];
		} elseif (isset($this->request->get['offer_id'])) {
			$data['offer_description'] = $this->model_catalog_offer->getOfferDescriptions($this->request->get['offer_id']);
		} else {
			$data['offer_description'] = array();
		}
		
		$language_id = $this->config->get('config_language_id');
		if (isset($data['offer_description'][$language_id]['name'])) {
			$data['heading_title'] = $data['offer_description'][$language_id]['name'];
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($offer_info)) {
			$data['model'] = $offer_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($offer_info)) {
			$data['sku'] = $offer_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($offer_info)) {
			$data['upc'] = $offer_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($offer_info)) {
			$data['ean'] = $offer_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($offer_info)) {
			$data['jan'] = $offer_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($offer_info)) {
			$data['isbn'] = $offer_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($offer_info)) {
			$data['mpn'] = $offer_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($offer_info)) {
			$data['location'] = $offer_info['location'];
		} else {
			$data['location'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['offer_store'])) {
			$data['offer_store'] = $this->request->post['offer_store'];
		} elseif (isset($this->request->get['offer_id'])) {
			$data['offer_store'] = $this->model_catalog_offer->getOfferStores($this->request->get['offer_id']);
		} else {
			$data['offer_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($offer_info)) {
			$data['shipping'] = $offer_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($offer_info)) {
			$data['price'] = $offer_info['price'];
		} else {
			$data['price'] = '';
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['offer_recurrings'])) {
			$data['offer_recurrings'] = $this->request->post['offer_recurrings'];
		} elseif (!empty($offer_info)) {
			$data['offer_recurrings'] = $this->model_catalog_offer->getRecurrings($offer_info['offer_id']);
		} else {
			$data['offer_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($offer_info)) {
			$data['tax_class_id'] = $offer_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($offer_info)) {
			$data['date_available'] = ($offer_info['date_available'] != '0000-00-00') ? $offer_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($offer_info)) {
			$data['quantity'] = $offer_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($offer_info)) {
			$data['minimum'] = $offer_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($offer_info)) {
			$data['subtract'] = $offer_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($offer_info)) {
			$data['sort_order'] = $offer_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($offer_info)) {
			$data['stock_status_id'] = $offer_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($offer_info)) {
			$data['status'] = $offer_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($offer_info)) {
			$data['noindex'] = $offer_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($offer_info)) {
			$data['weight'] = $offer_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($offer_info)) {
			$data['weight_class_id'] = $offer_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($offer_info)) {
			$data['length'] = $offer_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($offer_info)) {
			$data['width'] = $offer_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($offer_info)) {
			$data['height'] = $offer_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($offer_info)) {
			$data['length_class_id'] = $offer_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($offer_info)) {
			$data['manufacturer_id'] = $offer_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($offer_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($offer_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/category');
		
		$data['categories'] = $this->model_catalog_category->getAllCategories();
		
		if (isset($this->request->post['main_offers_id'])) {
			$data['main_offers_id'] = $this->request->post['main_offers_id'];
				} elseif (isset($offer_info)) {
			$data['main_offers_id'] = $this->model_catalog_offer->getOfferMainCategoryId($this->request->get['offer_id']);
				} else {
			$data['main_offers_id'] = 0;
		}			

		if (isset($this->request->post['offer_category'])) {
			$categories = $this->request->post['offer_category'];
		} elseif (isset($this->request->get['offer_id'])) {
			$categories = $this->model_catalog_offer->getOfferCategories($this->request->get['offer_id']);
		} else {
			$categories = array();
		}

		$data['offer_categories'] = array();

		foreach ($categories as $offers_id) {
			$category_info = $this->model_catalog_category->getCategory($offers_id);

			if ($category_info) {
				$data['offer_categories'][] = array(
					'offers_id' => $category_info['offers_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['offer_filter'])) {
			$filters = $this->request->post['offer_filter'];
		} elseif (isset($this->request->get['offer_id'])) {
			$filters = $this->model_catalog_offer->getOfferFilters($this->request->get['offer_id']);
		} else {
			$filters = array();
		}

		$data['offer_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['offer_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['offer_attribute'])) {
			$offer_attributes = $this->request->post['offer_attribute'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_attributes = $this->model_catalog_offer->getOfferAttributes($this->request->get['offer_id']);
		} else {
			$offer_attributes = array();
		}

		$data['offer_attributes'] = array();

		foreach ($offer_attributes as $offer_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($offer_attribute['attribute_id']);

			if ($attribute_info) {
				$data['offer_attributes'][] = array(
					'attribute_id'                  => $offer_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'offer_attribute_description' => $offer_attribute['offer_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['offer_option'])) {
			$offer_options = $this->request->post['offer_option'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_options = $this->model_catalog_offer->getOfferOptions($this->request->get['offer_id']);
		} else {
			$offer_options = array();
		}

		$data['offer_options'] = array();

		foreach ($offer_options as $offer_option) {
			$offer_option_value_data = array();

			if (isset($offer_option['offer_option_value'])) {
				foreach ($offer_option['offer_option_value'] as $offer_option_value) {
					$offer_option_value_data[] = array(
						'offer_option_value_id' => $offer_option_value['offer_option_value_id'],
						'option_value_id'         => $offer_option_value['option_value_id'],
						'quantity'                => $offer_option_value['quantity'],
						'subtract'                => $offer_option_value['subtract'],
						'price'                   => $offer_option_value['price'],
						'price_prefix'            => $offer_option_value['price_prefix'],
						'points'                  => $offer_option_value['points'],
						'points_prefix'           => $offer_option_value['points_prefix'],
						'weight'                  => $offer_option_value['weight'],
						'weight_prefix'           => $offer_option_value['weight_prefix']
					);
				}
			}

			$data['offer_options'][] = array(
				'offer_option_id'    => $offer_option['offer_option_id'],
				'offer_option_value' => $offer_option_value_data,
				'option_id'            => $offer_option['option_id'],
				'name'                 => $offer_option['name'],
				'type'                 => $offer_option['type'],
				'value'                => isset($offer_option['value']) ? $offer_option['value'] : '',
				'required'             => $offer_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['offer_options'] as $offer_option) {
			if ($offer_option['type'] == 'select' || $offer_option['type'] == 'radio' || $offer_option['type'] == 'checkbox' || $offer_option['type'] == 'image') {
				if (!isset($data['option_values'][$offer_option['option_id']])) {
					$data['option_values'][$offer_option['option_id']] = $this->model_catalog_option->getOptionValues($offer_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['offer_discount'])) {
			$offer_discounts = $this->request->post['offer_discount'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_discounts = $this->model_catalog_offer->getOfferDiscounts($this->request->get['offer_id']);
		} else {
			$offer_discounts = array();
		}

		$data['offer_discounts'] = array();

		foreach ($offer_discounts as $offer_discount) {
			$data['offer_discounts'][] = array(
				'customer_group_id' => $offer_discount['customer_group_id'],
				'quantity'          => $offer_discount['quantity'],
				'priority'          => $offer_discount['priority'],
				'price'             => $offer_discount['price'],
				'date_start'        => ($offer_discount['date_start'] != '0000-00-00') ? $offer_discount['date_start'] : '',
				'date_end'          => ($offer_discount['date_end'] != '0000-00-00') ? $offer_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['offer_special'])) {
			$offer_specials = $this->request->post['offer_special'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_specials = $this->model_catalog_offer->getOfferSpecials($this->request->get['offer_id']);
		} else {
			$offer_specials = array();
		}

		$data['offer_specials'] = array();

		foreach ($offer_specials as $offer_special) {
			$data['offer_specials'][] = array(
				'customer_group_id' => $offer_special['customer_group_id'],
				'priority'          => $offer_special['priority'],
				'price'             => $offer_special['price'],
				'date_start'        => ($offer_special['date_start'] != '0000-00-00') ? $offer_special['date_start'] : '',
				'date_end'          => ($offer_special['date_end'] != '0000-00-00') ? $offer_special['date_end'] :  ''
			);
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($offer_info)) {
			$data['image'] = $offer_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($offer_info) && is_file(DIR_IMAGE . $offer_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($offer_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['offer_image'])) {
			$offer_images = $this->request->post['offer_image'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_images = $this->model_catalog_offer->getOfferImages($this->request->get['offer_id']);
		} else {
			$offer_images = array();
		}

		$data['offer_images'] = array();

		foreach ($offer_images as $offer_image) {
			if (is_file(DIR_IMAGE . $offer_image['image'])) {
				$image = $offer_image['image'];
				$thumb = $offer_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['offer_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $offer_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['offer_download'])) {
			$offer_downloads = $this->request->post['offer_download'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offer_downloads = $this->model_catalog_offer->getOfferDownloads($this->request->get['offer_id']);
		} else {
			$offer_downloads = array();
		}

		$data['offer_downloads'] = array();

		foreach ($offer_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['offer_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['offer_related'])) {
			$offers = $this->request->post['offer_related'];
		} elseif (isset($this->request->get['offer_id'])) {
			$offers = $this->model_catalog_offer->getOfferRelated($this->request->get['offer_id']);
		} else {
			$offers = array();
		}

		$data['offer_relateds'] = array();

		foreach ($offers as $offer_id) {
			$related_info = $this->model_catalog_offer->getOffer($offer_id);

			if ($related_info) {
				$data['offer_relateds'][] = array(
					'offer_id' => $related_info['offer_id'],
					'name'       => $related_info['name']
				);
			}
		}
		
		if (isset($this->request->post['offer_related_article'])) {
			$articles = $this->request->post['offer_related_article'];
		} elseif (isset($offer_info)) {
			$articles = $this->model_catalog_offer->getArticleRelated($this->request->get['offer_id']);
		} else {
			$articles = array();
		}
	
		$data['offer_related_article'] = array();
		$this->load->model('blog/article');
		
		foreach ($articles as $article_id) {
			$article_info = $this->model_blog_article->getArticle($article_id);
			
			if ($article_info) {
				$data['offer_related_article'][] = array(
					'article_id' => $article_info['article_id'],
					'name'       => $article_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($offer_info)) {
			$data['points'] = $offer_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['offer_reward'])) {
			$data['offer_reward'] = $this->request->post['offer_reward'];
		} elseif (isset($this->request->get['offer_id'])) {
			$data['offer_reward'] = $this->model_catalog_offer->getOfferRewards($this->request->get['offer_id']);
		} else {
			$data['offer_reward'] = array();
		}

		if (isset($this->request->post['offer_seo_url'])) {
			$data['offer_seo_url'] = $this->request->post['offer_seo_url'];
		} elseif (isset($this->request->get['offer_id'])) {
			$data['offer_seo_url'] = $this->model_catalog_offer->getOfferSeoUrls($this->request->get['offer_id']);
		} else {
			$data['offer_seo_url'] = array();
		}

		if (isset($this->request->post['offer_layout'])) {
			$data['offer_layout'] = $this->request->post['offer_layout'];
		} elseif (isset($this->request->get['offer_id'])) {
			$data['offer_layout'] = $this->model_catalog_offer->getOfferLayouts($this->request->get['offer_id']);
		} else {
			$data['offer_layout'] = array();
		}

		$data['variants'] = array();

		$variants = $this->model_catalog_offer->getVariants($this->request->get['offer_id']);

		foreach ($variants as $variant) {

			$data['variants'][] = array(
				'name'     => $variant['name'],
				'product_id'     => $variant['product_id'],
			);

		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/offer_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['offer_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 0) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
			
						if ((utf8_strlen($value['meta_h1']) < 0) || (utf8_strlen($value['meta_h1']) > 255)) {
				$this->error['meta_h1'][$language_id] = $this->language->get('error_meta_h1');
			}
		}

		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if ($this->request->post['offer_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['offer_id']) || (($seo_url['query'] != 'offer_id=' . $this->request->get['offer_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');

								break;
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
	
	public function enable() {
        $this->load->language('catalog/offer');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/offer');
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $offer_id) {
                $this->model_catalog_offer->editOfferStatus($offer_id, 1);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            $this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
    public function disable() {
        $this->load->language('catalog/offer');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/offer');
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $offer_id) {
                $this->model_catalog_offer->editOfferStatus($offer_id, 0);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            $this->response->redirect($this->url->link('catalog/offer', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
	
	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'catalog/offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'catalog/offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/offer');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = $this->config->get('config_limit_autocomplete');
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_offer->getOffers($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$offer_options = $this->model_catalog_offer->getOfferOptions($result['offer_id']);

				foreach ($offer_options as $offer_option) {
					$option_info = $this->model_catalog_option->getOption($offer_option['option_id']);

					if ($option_info) {
						$offer_option_value_data = array();

						foreach ($offer_option['offer_option_value'] as $offer_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($offer_option_value['option_value_id']);

							if ($option_value_info) {
								$offer_option_value_data[] = array(
									'offer_option_value_id' => $offer_option_value['offer_option_value_id'],
									'option_value_id'         => $offer_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$offer_option_value['price'] ? $this->currency->format($offer_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $offer_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'offer_option_id'    => $offer_option['offer_option_id'],
							'offer_option_value' => $offer_option_value_data,
							'option_id'            => $offer_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $offer_option['value'],
							'required'             => $offer_option['required']
						);
					}
				}

				$json[] = array(
					'offer_id' => $result['offer_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
