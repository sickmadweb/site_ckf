<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCatalogViews extends Controller {
	private $error = array();
	private $views_id = 0;
	private $path = array();

	public function index() {
		$this->load->language('catalog/views');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/views');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/views');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/views');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_views->addViews($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/views');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/views');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_views->editViews($this->request->get['views_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/views');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/views');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $views_id) {
				$this->model_catalog_views->deleteViews($views_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('catalog/views');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/views');

		if ($this->validateRepair()) {
			$this->model_catalog_views->repairCategories();

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			'href' => $this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		$data['add'] = $this->url->link('catalog/views/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/views/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['repair'] = $this->url->link('catalog/views/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/views/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/views/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['path'])) {
			if ($this->request->get['path'] != '') {
					$this->path = explode('_', $this->request->get['path']);
					$this->views_id = end($this->path);
					$this->session->data['path'] = $this->request->get['path'];
			} else {
				unset($this->session->data['path']);
			}
		} elseif (isset($this->session->data['path'])) {
				$this->path = explode('_', $this->session->data['path']);
				$this->views_id = end($this->path);
 		}

		$data['categories'] = $this->getCategories(0);

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

		$views_total = $this->model_catalog_views->getTotalCategories();

		$data['results'] = $this->language->get('text_views_total') . ($views_total);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/views_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['views_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['parent'])) {
			$data['error_parent'] = $this->error['parent'];
		} else {
			$data['error_parent'] = '';
		}

		$url = '';

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
			'href' => $this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['views_id'])) {
			$data['action'] = $this->url->link('catalog/views/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/views/edit', 'user_token=' . $this->session->data['user_token'] . '&views_id=' . $this->request->get['views_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['views_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$views_info = $this->model_catalog_views->getViews($this->request->get['views_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['views_description'])) {
			$data['views_description'] = $this->request->post['views_description'];
		} elseif (isset($this->request->get['views_id'])) {
			$data['views_description'] = $this->model_catalog_views->getViewsDescriptions($this->request->get['views_id']);
		} else {
			$data['views_description'] = array();
		}

		$language_id = $this->config->get('config_language_id');
		if (isset($data['views_description'][$language_id]['name'])) {
			$data['heading_title'] = $data['views_description'][$language_id]['name'];
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($views_info)) {
			$data['path'] = $views_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($views_info)) {
			$data['parent_id'] = $views_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		$this->load->model('catalog/filter');

		if (isset($this->request->post['views_filter'])) {
			$filters = $this->request->post['views_filter'];
		} elseif (isset($this->request->get['views_id'])) {
			$filters = $this->model_catalog_views->getViewsFilters($this->request->get['views_id']);
		} else {
			$filters = array();
		}

		$data['views_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['views_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
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

		if (isset($this->request->post['views_store'])) {
			$data['views_store'] = $this->request->post['views_store'];
		} elseif (isset($this->request->get['views_id'])) {
			$data['views_store'] = $this->model_catalog_views->getViewsStores($this->request->get['views_id']);
		} else {
			$data['views_store'] = array(0);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($views_info)) {
			$data['image'] = $views_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($views_info) && is_file(DIR_IMAGE . $views_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($views_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($views_info)) {
			$data['top'] = $views_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($views_info)) {
			$data['column'] = $views_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($views_info)) {
			$data['sort_order'] = $views_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($views_info)) {
			$data['status'] = $views_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['views_seo_url'])) {
			$data['views_seo_url'] = $this->request->post['views_seo_url'];
		} elseif (isset($this->request->get['views_id'])) {
			$data['views_seo_url'] = $this->model_catalog_views->getViewsSeoUrls($this->request->get['views_id']);
		} else {
			$data['views_seo_url'] = array();
		}

		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($views_info)) {
			$data['noindex'] = $views_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['views_layout'])) {
			$data['views_layout'] = $this->request->post['views_layout'];
		} elseif (isset($this->request->get['views_id'])) {
			$data['views_layout'] = $this->model_catalog_views->getViewsLayouts($this->request->get['views_id']);
		} else {
			$data['views_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/views_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/views')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['views_description'] as $language_id => $value) {
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

		if (isset($this->request->get['views_id']) && $this->request->post['parent_id']) {
			$results = $this->model_catalog_views->getViewsPath($this->request->post['parent_id']);

			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['views_id']) {
					$this->error['parent'] = $this->language->get('error_parent');

					break;
				}
			}
		}

		if ($this->request->post['views_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['views_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['views_id']) || ($seo_url['query'] != 'views_id=' . $this->request->get['views_id']))) {
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
        $this->load->language('catalog/views');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/views');
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $views_id) {
                $this->model_catalog_views->editViewsStatus($views_id, 1);
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
			$this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }

    public function disable() {
        $this->load->language('catalog/views');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/views');
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $views_id) {
                $this->model_catalog_views->editViewsStatus($views_id, 0);
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
            $this->response->redirect($this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }

	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'catalog/views')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'catalog/views')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/views')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/views')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/views');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => $this->config->get('config_limit_autocomplete')
			);

			$results = $this->model_catalog_views->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'views_id' => $result['views_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function getCategories($parent_id, $parent_path = '', $indent = '') {
		$views_id = array_shift($this->path);
		$output = array();
		static $href_views = null;
		static $href_action = null;
		if ($href_views === null) {
			$href_views = $this->url->link('catalog/views', 'user_token=' . $this->session->data['user_token'] . '&path=', true);
			$href_action = $this->url->link('catalog/views/update', 'user_token=' . $this->session->data['user_token'] . '&views_id=', true);
		}
		$results = $this->model_catalog_views->getCategoriesByParentId($parent_id);
		foreach ($results as $result) {
			$path = $parent_path . $result['views_id'];
			$href = ($result['children']) ? $href_views . $path : '';
			$name = $result['name'];
			if ($views_id == $result['views_id']) {
				$name = '<b>' . $name . '</b>';
				$data['breadcrumbs'][] = array(
					'text'      => $result['name'],
					'href'      => $href,
					'separator' => ' :: '
			);
				$href = '';
			}
			$selected = isset($this->request->post['selected']) && in_array($result['views_id'], $this->request->post['selected']);
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $href_action . $result['views_id']
			);
			$output[$result['views_id']] = array(
				'views_id' => $result['views_id'],
				'name'        => $name,
				'sort_order'  => $result['sort_order'],
				'noindex'  	  => $result['noindex'],
				'edit'        => $this->url->link('catalog/views/edit', 'user_token=' . $this->session->data['user_token'] . '&views_id=' . $result['views_id'], true),
				'selected'    => $selected,
				'action'      => $action,
				'href'        => $href,
				'href_shop'   => HTTP_CATALOG . 'index.php?route=product/views&path=' . ($result['views_id']),
				'indent'      => $indent
			);
			if ($views_id == $result['views_id']) {
				$output += $this->getCategories($result['views_id'], $path . '_', $indent . str_repeat('&nbsp;', 8));
			}
		}
		return $output;
	}
	private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
		$output = array();
		if (array_key_exists($parent_id, $categories)) {
			if ($parent_name != '') {
				$parent_name .= $this->language->get('text_separator');
			}
			foreach ($categories[$parent_id] as $views) {
				$output[$views['views_id']] = array(
					'views_id' => $views['views_id'],
					'name'        => $parent_name . $views['name']
				);
				$output += $this->getAllCategories($categories, $views['views_id'], $parent_name . $views['name']);
			}
		}
		return $output;
		}
}