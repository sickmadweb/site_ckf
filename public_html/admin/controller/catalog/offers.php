<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCatalogOffers extends Controller {
	private $error = array();
	private $offers_id = 0;
	private $path = array();

	public function index() {
		$this->load->language('catalog/offers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offers');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/offers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offers');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_offers->addOffers($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/offers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offers');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_offers->editOffers($this->request->get['offers_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/offers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offers');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $offers_id) {
				$this->model_catalog_offers->deleteOffers($offers_id);
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

			$this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('catalog/offers');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/offers');

		if ($this->validateRepair()) {
			$this->model_catalog_offers->repairCategories();

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

			$this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		$data['add'] = $this->url->link('catalog/offers/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/offers/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['repair'] = $this->url->link('catalog/offers/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/offers/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/offers/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['path'])) {
			if ($this->request->get['path'] != '') {
					$this->path = explode('_', $this->request->get['path']);
					$this->offers_id = end($this->path);
					$this->session->data['path'] = $this->request->get['path'];
			} else {
				unset($this->session->data['path']);
			}
		} elseif (isset($this->session->data['path'])) {
				$this->path = explode('_', $this->session->data['path']);
				$this->offers_id = end($this->path);
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

		$offers_total = $this->model_catalog_offers->getTotalCategories();

		$data['results'] = $this->language->get('text_offers_total') . ($offers_total);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/offers_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['offers_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['offers_id'])) {
			$data['action'] = $this->url->link('catalog/offers/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/offers/edit', 'user_token=' . $this->session->data['user_token'] . '&offers_id=' . $this->request->get['offers_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['offers_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$offers_info = $this->model_catalog_offers->getOffers($this->request->get['offers_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['offers_description'])) {
			$data['offers_description'] = $this->request->post['offers_description'];
		} elseif (isset($this->request->get['offers_id'])) {
			$data['offers_description'] = $this->model_catalog_offers->getOffersDescriptions($this->request->get['offers_id']);
		} else {
			$data['offers_description'] = array();
		}

		$language_id = $this->config->get('config_language_id');
		if (isset($data['offers_description'][$language_id]['name'])) {
			$data['heading_title'] = $data['offers_description'][$language_id]['name'];
		}

		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($offers_info)) {
			$data['path'] = $offers_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($offers_info)) {
			$data['parent_id'] = $offers_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		$this->load->model('catalog/filter');

		if (isset($this->request->post['offers_filter'])) {
			$filters = $this->request->post['offers_filter'];
		} elseif (isset($this->request->get['offers_id'])) {
			$filters = $this->model_catalog_offers->getOffersFilters($this->request->get['offers_id']);
		} else {
			$filters = array();
		}

		$data['offers_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['offers_filters'][] = array(
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

		if (isset($this->request->post['offers_store'])) {
			$data['offers_store'] = $this->request->post['offers_store'];
		} elseif (isset($this->request->get['offers_id'])) {
			$data['offers_store'] = $this->model_catalog_offers->getOffersStores($this->request->get['offers_id']);
		} else {
			$data['offers_store'] = array(0);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($offers_info)) {
			$data['image'] = $offers_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($offers_info) && is_file(DIR_IMAGE . $offers_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($offers_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($offers_info)) {
			$data['top'] = $offers_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($offers_info)) {
			$data['column'] = $offers_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($offers_info)) {
			$data['sort_order'] = $offers_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($offers_info)) {
			$products = $this->model_catalog_offers->getProductRelated($this->request->get['offers_id']);
		} else {
			$products = array();
		}

		$data['product_related'] = array();

		$this->load->model('catalog/product');

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['product_related'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['article_related'])) {
			$articles = $this->request->post['article_related'];
		} elseif (isset($offers_info)) {
			$articles = $this->model_catalog_offers->getArticleRelated($this->request->get['offers_id']);
		} else {
			$articles = array();
		}

		$data['article_related'] = array();

		$this->load->model('blog/article');

		foreach ($articles as $article_id) {
			$related_info = $this->model_blog_article->getArticle($article_id);

			if ($related_info) {
				$data['article_related'][] = array(
					'article_id' => $related_info['article_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($offers_info)) {
			$data['status'] = $offers_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['offers_seo_url'])) {
			$data['offers_seo_url'] = $this->request->post['offers_seo_url'];
		} elseif (isset($this->request->get['offers_id'])) {
			$data['offers_seo_url'] = $this->model_catalog_offers->getOffersSeoUrls($this->request->get['offers_id']);
		} else {
			$data['offers_seo_url'] = array();
		}

		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($offers_info)) {
			$data['noindex'] = $offers_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['offers_layout'])) {
			$data['offers_layout'] = $this->request->post['offers_layout'];
		} elseif (isset($this->request->get['offers_id'])) {
			$data['offers_layout'] = $this->model_catalog_offers->getOffersLayouts($this->request->get['offers_id']);
		} else {
			$data['offers_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/offers_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/offers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['offers_description'] as $language_id => $value) {
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

		if (isset($this->request->get['offers_id']) && $this->request->post['parent_id']) {
			$results = $this->model_catalog_offers->getOffersPath($this->request->post['parent_id']);

			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['offers_id']) {
					$this->error['parent'] = $this->language->get('error_parent');

					break;
				}
			}
		}

		if ($this->request->post['offers_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['offers_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['offers_id']) || ($seo_url['query'] != 'offers_id=' . $this->request->get['offers_id']))) {
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
        $this->load->language('catalog/offers');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/offers');
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $offers_id) {
                $this->model_catalog_offers->editOffersStatus($offers_id, 1);
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
			$this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }

    public function disable() {
        $this->load->language('catalog/offers');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/offers');
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $offers_id) {
                $this->model_catalog_offers->editOffersStatus($offers_id, 0);
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
            $this->response->redirect($this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }

	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'catalog/offers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'catalog/offers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/offers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/offers')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/offers');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => $this->config->get('config_limit_autocomplete')
			);

			$results = $this->model_catalog_offers->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'offers_id' => $result['offers_id'],
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
		$offers_id = array_shift($this->path);
		$output = array();
		static $href_offers = null;
		static $href_action = null;
		if ($href_offers === null) {
			$href_offers = $this->url->link('catalog/offers', 'user_token=' . $this->session->data['user_token'] . '&path=', true);
			$href_action = $this->url->link('catalog/offers/update', 'user_token=' . $this->session->data['user_token'] . '&offers_id=', true);
		}
		$results = $this->model_catalog_offers->getCategoriesByParentId($parent_id);
		foreach ($results as $result) {
			$path = $parent_path . $result['offers_id'];
			$href = ($result['children']) ? $href_offers . $path : '';
			$name = $result['name'];
			if ($offers_id == $result['offers_id']) {
				$name = '<b>' . $name . '</b>';
				$data['breadcrumbs'][] = array(
					'text'      => $result['name'],
					'href'      => $href,
					'separator' => ' :: '
			);
				$href = '';
			}
			$selected = isset($this->request->post['selected']) && in_array($result['offers_id'], $this->request->post['selected']);
			$action = array();
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $href_action . $result['offers_id']
			);
			$output[$result['offers_id']] = array(
				'offers_id' => $result['offers_id'],
				'name'        => $name,
				'sort_order'  => $result['sort_order'],
				'noindex'  	  => $result['noindex'],
				'edit'        => $this->url->link('catalog/offers/edit', 'user_token=' . $this->session->data['user_token'] . '&offers_id=' . $result['offers_id'], true),
				'selected'    => $selected,
				'action'      => $action,
				'href'        => $href,
				'href_shop'   => HTTP_CATALOG . 'index.php?route=product/offers&path=' . ($result['offers_id']),
				'indent'      => $indent
			);
			if ($offers_id == $result['offers_id']) {
				$output += $this->getCategories($result['offers_id'], $path . '_', $indent . str_repeat('&nbsp;', 8));
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
			foreach ($categories[$parent_id] as $offers) {
				$output[$offers['offers_id']] = array(
					'offers_id' => $offers['offers_id'],
					'name'        => $parent_name . $offers['name']
				);
				$output += $this->getAllCategories($categories, $offers['offers_id'], $parent_name . $offers['name']);
			}
		}
		return $output;
		}
}