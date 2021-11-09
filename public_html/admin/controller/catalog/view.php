<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCatalogView extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/view');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/view');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/view');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/view');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_view->addView($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/view');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/view');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_view->editView($this->request->get['view_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/view');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/view');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $view_id) {
				$this->model_catalog_view->deleteView($view_id);
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

			$this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function copy() {
		$this->load->language('catalog/view');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/view');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $view_id) {
				$this->model_catalog_view->copyView($view_id);
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

			$this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
				

				
				$category = $this->model_catalog_views->getCategory($filter_category);
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
			'href' => $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/view/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('catalog/view/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/view/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/view/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/view/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['views'] = array();

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

		$view_total = $this->model_catalog_view->getTotalViews($filter_data);

		$results = $this->model_catalog_view->getViews($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$view_specials = $this->model_catalog_view->getViewSpecials($result['view_id']);

			foreach ($view_specials  as $view_special) {
				if (($view_special['date_start'] == '0000-00-00' || strtotime($view_special['date_start']) < time()) && ($view_special['date_end'] == '0000-00-00' || strtotime($view_special['date_end']) > time())) {
					$special = $this->currency->format($view_special['price'], $this->config->get('config_currency'));

					break;
				}
			}

			$data['views'][] = array(
				'view_id' => $result['view_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled_short') : $this->language->get('text_disabled_short'),
				'noindex'    => $result['noindex'] ? $this->language->get('text_enabled_short') : $this->language->get('text_disabled_short'),
				'href_shop'  => HTTP_CATALOG . 'index.php?route=product/view&view_id=' . $result['view_id'],
				'edit'       => $this->url->link('catalog/view/edit', 'user_token=' . $this->session->data['user_token'] . '&view_id=' . $result['view_id'] . $url, true)
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

		$data['sort_name'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_noindex'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.noindex' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

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
		$pagination->total = $view_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($view_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($view_total - $this->config->get('config_limit_admin'))) ? $view_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $view_total, ceil($view_total / $this->config->get('config_limit_admin')));

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

		$this->response->setOutput($this->load->view('catalog/view_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['view_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['view_id'])) {
			$data['action'] = $this->url->link('catalog/view/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/view/edit', 'user_token=' . $this->session->data['user_token'] . '&view_id=' . $this->request->get['view_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['view_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$view_info = $this->model_catalog_view->getView($this->request->get['view_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['view_description'])) {
			$data['view_description'] = $this->request->post['view_description'];
		} elseif (isset($this->request->get['view_id'])) {
			$data['view_description'] = $this->model_catalog_view->getViewDescriptions($this->request->get['view_id']);
		} else {
			$data['view_description'] = array();
		}
		
		$language_id = $this->config->get('config_language_id');
		if (isset($data['view_description'][$language_id]['name'])) {
			$data['heading_title'] = $data['view_description'][$language_id]['name'];
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($view_info)) {
			$data['model'] = $view_info['model'];
		} else {
			$data['model'] = '';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($view_info)) {
			$data['sku'] = $view_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($view_info)) {
			$data['upc'] = $view_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($view_info)) {
			$data['ean'] = $view_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($view_info)) {
			$data['jan'] = $view_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($view_info)) {
			$data['isbn'] = $view_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($view_info)) {
			$data['mpn'] = $view_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($view_info)) {
			$data['location'] = $view_info['location'];
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

		if (isset($this->request->post['view_store'])) {
			$data['view_store'] = $this->request->post['view_store'];
		} elseif (isset($this->request->get['view_id'])) {
			$data['view_store'] = $this->model_catalog_view->getViewStores($this->request->get['view_id']);
		} else {
			$data['view_store'] = array(0);
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($view_info)) {
			$data['shipping'] = $view_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($view_info)) {
			$data['price'] = $view_info['price'];
		} else {
			$data['price'] = '';
		}

		$this->load->model('catalog/recurring');

		$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();

		if (isset($this->request->post['view_recurrings'])) {
			$data['view_recurrings'] = $this->request->post['view_recurrings'];
		} elseif (!empty($view_info)) {
			$data['view_recurrings'] = $this->model_catalog_view->getRecurrings($view_info['view_id']);
		} else {
			$data['view_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($view_info)) {
			$data['tax_class_id'] = $view_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($view_info)) {
			$data['date_available'] = ($view_info['date_available'] != '0000-00-00') ? $view_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}

		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($view_info)) {
			$data['quantity'] = $view_info['quantity'];
		} else {
			$data['quantity'] = 1;
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($view_info)) {
			$data['minimum'] = $view_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($view_info)) {
			$data['subtract'] = $view_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($view_info)) {
			$data['sort_order'] = $view_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($view_info)) {
			$data['stock_status_id'] = $view_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($view_info)) {
			$data['status'] = $view_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($view_info)) {
			$data['noindex'] = $view_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($view_info)) {
			$data['weight'] = $view_info['weight'];
		} else {
			$data['weight'] = '';
		}

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($view_info)) {
			$data['weight_class_id'] = $view_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($view_info)) {
			$data['length'] = $view_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($view_info)) {
			$data['width'] = $view_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($view_info)) {
			$data['height'] = $view_info['height'];
		} else {
			$data['height'] = '';
		}

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($view_info)) {
			$data['length_class_id'] = $view_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($view_info)) {
			$data['manufacturer_id'] = $view_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($view_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($view_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		// Categories
		$this->load->model('catalog/views');
		
		$data['categories'] = $this->model_catalog_views->getAllCategories();
		
		if (isset($this->request->post['main_views_id'])) {
			$data['main_views_id'] = $this->request->post['main_views_id'];
				} elseif (isset($view_info)) {
			$data['main_views_id'] = $this->model_catalog_view->getViewMainCategoryId($this->request->get['view_id']);
				} else {
			$data['main_views_id'] = 0;
		}			

		if (isset($this->request->post['view_category'])) {
			$categories = $this->request->post['view_category'];
		} elseif (isset($this->request->get['view_id'])) {
			$categories = $this->model_catalog_view->getViewCategories($this->request->get['view_id']);
		} else {
			$categories = array();
		}

		$data['view_categories'] = array();

		foreach ($categories as $views_id) {
			$category_info = $this->model_catalog_views->getViews($views_id);

			if ($category_info) {
				$data['view_categories'][] = array(
					'views_id' => $category_info['views_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		// Filters
		$this->load->model('catalog/filter');

		if (isset($this->request->post['view_filter'])) {
			$filters = $this->request->post['view_filter'];
		} elseif (isset($this->request->get['view_id'])) {
			$filters = $this->model_catalog_view->getViewFilters($this->request->get['view_id']);
		} else {
			$filters = array();
		}

		$data['view_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['view_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}

		// Attributes
		$this->load->model('catalog/attribute');

		if (isset($this->request->post['view_attribute'])) {
			$view_attributes = $this->request->post['view_attribute'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_attributes = $this->model_catalog_view->getViewAttributes($this->request->get['view_id']);
		} else {
			$view_attributes = array();
		}

		$data['view_attributes'] = array();

		foreach ($view_attributes as $view_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($view_attribute['attribute_id']);

			if ($attribute_info) {
				$data['view_attributes'][] = array(
					'attribute_id'                  => $view_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'view_attribute_description' => $view_attribute['view_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('catalog/option');

		if (isset($this->request->post['view_option'])) {
			$view_options = $this->request->post['view_option'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_options = $this->model_catalog_view->getViewOptions($this->request->get['view_id']);
		} else {
			$view_options = array();
		}

		$data['view_options'] = array();

		foreach ($view_options as $view_option) {
			$view_option_value_data = array();

			if (isset($view_option['view_option_value'])) {
				foreach ($view_option['view_option_value'] as $view_option_value) {
					$view_option_value_data[] = array(
						'view_option_value_id' => $view_option_value['view_option_value_id'],
						'option_value_id'         => $view_option_value['option_value_id'],
						'quantity'                => $view_option_value['quantity'],
						'subtract'                => $view_option_value['subtract'],
						'price'                   => $view_option_value['price'],
						'price_prefix'            => $view_option_value['price_prefix'],
						'points'                  => $view_option_value['points'],
						'points_prefix'           => $view_option_value['points_prefix'],
						'weight'                  => $view_option_value['weight'],
						'weight_prefix'           => $view_option_value['weight_prefix']
					);
				}
			}

			$data['view_options'][] = array(
				'view_option_id'    => $view_option['view_option_id'],
				'view_option_value' => $view_option_value_data,
				'option_id'            => $view_option['option_id'],
				'name'                 => $view_option['name'],
				'type'                 => $view_option['type'],
				'value'                => isset($view_option['value']) ? $view_option['value'] : '',
				'required'             => $view_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['view_options'] as $view_option) {
			if ($view_option['type'] == 'select' || $view_option['type'] == 'radio' || $view_option['type'] == 'checkbox' || $view_option['type'] == 'image') {
				if (!isset($data['option_values'][$view_option['option_id']])) {
					$data['option_values'][$view_option['option_id']] = $this->model_catalog_option->getOptionValues($view_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['view_discount'])) {
			$view_discounts = $this->request->post['view_discount'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_discounts = $this->model_catalog_view->getViewDiscounts($this->request->get['view_id']);
		} else {
			$view_discounts = array();
		}

		$data['view_discounts'] = array();

		foreach ($view_discounts as $view_discount) {
			$data['view_discounts'][] = array(
				'customer_group_id' => $view_discount['customer_group_id'],
				'quantity'          => $view_discount['quantity'],
				'priority'          => $view_discount['priority'],
				'price'             => $view_discount['price'],
				'date_start'        => ($view_discount['date_start'] != '0000-00-00') ? $view_discount['date_start'] : '',
				'date_end'          => ($view_discount['date_end'] != '0000-00-00') ? $view_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['view_special'])) {
			$view_specials = $this->request->post['view_special'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_specials = $this->model_catalog_view->getViewSpecials($this->request->get['view_id']);
		} else {
			$view_specials = array();
		}

		$data['view_specials'] = array();

		foreach ($view_specials as $view_special) {
			$data['view_specials'][] = array(
				'customer_group_id' => $view_special['customer_group_id'],
				'priority'          => $view_special['priority'],
				'price'             => $view_special['price'],
				'date_start'        => ($view_special['date_start'] != '0000-00-00') ? $view_special['date_start'] : '',
				'date_end'          => ($view_special['date_end'] != '0000-00-00') ? $view_special['date_end'] :  ''
			);
		}

		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($view_info)) {
			$data['image'] = $view_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($view_info) && is_file(DIR_IMAGE . $view_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($view_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['view_image'])) {
			$view_images = $this->request->post['view_image'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_images = $this->model_catalog_view->getViewImages($this->request->get['view_id']);
		} else {
			$view_images = array();
		}

		$data['view_images'] = array();

		foreach ($view_images as $view_image) {
			if (is_file(DIR_IMAGE . $view_image['image'])) {
				$image = $view_image['image'];
				$thumb = $view_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['view_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $view_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('catalog/download');

		if (isset($this->request->post['view_download'])) {
			$view_downloads = $this->request->post['view_download'];
		} elseif (isset($this->request->get['view_id'])) {
			$view_downloads = $this->model_catalog_view->getViewDownloads($this->request->get['view_id']);
		} else {
			$view_downloads = array();
		}

		$data['view_downloads'] = array();

		foreach ($view_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);

			if ($download_info) {
				$data['view_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['view_related'])) {
			$views = $this->request->post['view_related'];
		} elseif (isset($this->request->get['view_id'])) {
			$views = $this->model_catalog_view->getViewRelated($this->request->get['view_id']);
		} else {
			$views = array();
		}

		$data['view_relateds'] = array();

		foreach ($views as $view_id) {
			$related_info = $this->model_catalog_view->getView($view_id);

			if ($related_info) {
				$data['view_relateds'][] = array(
					'view_id' => $related_info['view_id'],
					'name'       => $related_info['name']
				);
			}
		}
		
		if (isset($this->request->post['view_related_article'])) {
			$articles = $this->request->post['view_related_article'];
		} elseif (isset($view_info)) {
			$articles = $this->model_catalog_view->getArticleRelated($this->request->get['view_id']);
		} else {
			$articles = array();
		}
	
		$data['view_related_article'] = array();
		$this->load->model('blog/article');
		
		foreach ($articles as $article_id) {
			$article_info = $this->model_blog_article->getArticle($article_id);
			
			if ($article_info) {
				$data['view_related_article'][] = array(
					'article_id' => $article_info['article_id'],
					'name'       => $article_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($view_info)) {
			$data['points'] = $view_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['view_reward'])) {
			$data['view_reward'] = $this->request->post['view_reward'];
		} elseif (isset($this->request->get['view_id'])) {
			$data['view_reward'] = $this->model_catalog_view->getViewRewards($this->request->get['view_id']);
		} else {
			$data['view_reward'] = array();
		}

		if (isset($this->request->post['view_seo_url'])) {
			$data['view_seo_url'] = $this->request->post['view_seo_url'];
		} elseif (isset($this->request->get['view_id'])) {
			$data['view_seo_url'] = $this->model_catalog_view->getViewSeoUrls($this->request->get['view_id']);
		} else {
			$data['view_seo_url'] = array();
		}

		if (isset($this->request->post['view_layout'])) {
			$data['view_layout'] = $this->request->post['view_layout'];
		} elseif (isset($this->request->get['view_id'])) {
			$data['view_layout'] = $this->model_catalog_view->getViewLayouts($this->request->get['view_id']);
		} else {
			$data['view_layout'] = array();
		}

		$data['variants'] = array();

		$variants = $this->model_catalog_view->getVariants($this->request->get['view_id']);

		foreach ($variants as $variant) {

			$data['variants'][] = array(
				'name'     => $variant['name'],
				'offer_id'     => $variant['offer_id'],
			);

		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/view_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/view')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['view_description'] as $language_id => $value) {
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

		if ($this->request->post['view_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['view_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['view_id']) || (($seo_url['query'] != 'view_id=' . $this->request->get['view_id'])))) {
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
        $this->load->language('catalog/view');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/view');
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $view_id) {
                $this->model_catalog_view->editViewStatus($view_id, 1);
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
            $this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
    public function disable() {
        $this->load->language('catalog/view');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/view');
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $view_id) {
                $this->model_catalog_view->editViewStatus($view_id, 0);
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
            $this->response->redirect($this->url->link('catalog/view', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
	
	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'catalog/view')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'catalog/view')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/view')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/view')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('catalog/view');
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

			$results = $this->model_catalog_view->getViews($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$view_options = $this->model_catalog_view->getViewOptions($result['view_id']);

				foreach ($view_options as $view_option) {
					$option_info = $this->model_catalog_option->getOption($view_option['option_id']);

					if ($option_info) {
						$view_option_value_data = array();

						foreach ($view_option['view_option_value'] as $view_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($view_option_value['option_value_id']);

							if ($option_value_info) {
								$view_option_value_data[] = array(
									'view_option_value_id' => $view_option_value['view_option_value_id'],
									'option_value_id'         => $view_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$view_option_value['price'] ? $this->currency->format($view_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $view_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'view_option_id'    => $view_option['view_option_id'],
							'view_option_value' => $view_option_value_data,
							'option_id'            => $view_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $view_option['value'],
							'required'             => $view_option['required']
						);
					}
				}

				$json[] = array(
					'view_id' => $result['view_id'],
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
