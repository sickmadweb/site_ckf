<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogView extends Model {
	public function addView($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "view SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$view_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "view SET image = '" . $this->db->escape($data['image']) . "' WHERE view_id = '" . (int)$view_id . "'");
		}

		foreach ($data['view_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "view_description SET view_id = '" . (int)$view_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['view_store'])) {
			foreach ($data['view_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_store SET view_id = '" . (int)$view_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['view_attribute'])) {
			foreach ($data['view_attribute'] as $view_attribute) {
				if ($view_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "' AND attribute_id = '" . (int)$view_attribute['attribute_id'] . "'");

					foreach ($view_attribute['view_attribute_description'] as $language_id => $view_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "' AND attribute_id = '" . (int)$view_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "view_attribute SET view_id = '" . (int)$view_id . "', attribute_id = '" . (int)$view_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($view_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['view_option'])) {
			foreach ($data['view_option'] as $view_option) {
				if ($view_option['type'] == 'select' || $view_option['type'] == 'radio' || $view_option['type'] == 'checkbox' || $view_option['type'] == 'image') {
					if (isset($view_option['view_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "view_option SET view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', required = '" . (int)$view_option['required'] . "'");

						$view_option_id = $this->db->getLastId();

						foreach ($view_option['view_option_value'] as $view_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "view_option_value SET view_option_id = '" . (int)$view_option_id . "', view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', option_value_id = '" . (int)$view_option_value['option_value_id'] . "', quantity = '" . (int)$view_option_value['quantity'] . "', subtract = '" . (int)$view_option_value['subtract'] . "', price = '" . (float)$view_option_value['price'] . "', price_prefix = '" . $this->db->escape($view_option_value['price_prefix']) . "', points = '" . (int)$view_option_value['points'] . "', points_prefix = '" . $this->db->escape($view_option_value['points_prefix']) . "', weight = '" . (float)$view_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($view_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "view_option SET view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', value = '" . $this->db->escape($view_option['value']) . "', required = '" . (int)$view_option['required'] . "'");
				}
			}
		}

		if (isset($data['view_recurring'])) {
			foreach ($data['view_recurring'] as $recurring) {

				$query = $this->db->query("SELECT `view_id` FROM `" . DB_PREFIX . "view_recurring` WHERE `view_id` = '" . (int)$view_id . "' AND `customer_group_id = '" . (int)$recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "view_recurring` SET `view_id` = '" . (int)$view_id . "', customer_group_id = '" . (int)$recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");
				}
			}
		}
		
		if (isset($data['view_discount'])) {
			foreach ($data['view_discount'] as $view_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_discount SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$view_discount['customer_group_id'] . "', quantity = '" . (int)$view_discount['quantity'] . "', priority = '" . (int)$view_discount['priority'] . "', price = '" . (float)$view_discount['price'] . "', date_start = '" . $this->db->escape($view_discount['date_start']) . "', date_end = '" . $this->db->escape($view_discount['date_end']) . "'");
			}
		}

		if (isset($data['view_special'])) {
			foreach ($data['view_special'] as $view_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_special SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$view_special['customer_group_id'] . "', priority = '" . (int)$view_special['priority'] . "', price = '" . (float)$view_special['price'] . "', date_start = '" . $this->db->escape($view_special['date_start']) . "', date_end = '" . $this->db->escape($view_special['date_end']) . "'");
			}
		}

		if (isset($data['view_image'])) {
			foreach ($data['view_image'] as $view_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_image SET view_id = '" . (int)$view_id . "', image = '" . $this->db->escape($view_image['image']) . "', sort_order = '" . (int)$view_image['sort_order'] . "'");
			}
		}

		if (isset($data['view_download'])) {
			foreach ($data['view_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_download SET view_id = '" . (int)$view_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['view_category'])) {
			foreach ($data['view_category'] as $views_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_category SET view_id = '" . (int)$view_id . "', views_id = '" . (int)$views_id . "'");
			}
		}
		
		if (isset($data['main_views_id']) && $data['main_views_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "' AND views_id = '" . (int)$data['main_views_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_category SET view_id = '" . (int)$view_id . "', views_id = '" . (int)$data['main_views_id'] . "', main_category = 1");
				} elseif (isset($data['view_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "view_to_category SET main_category = 1 WHERE view_id = '" . (int)$view_id . "' AND views_id = '" . (int)$data['view_category'][0] . "'");
		}

		if (isset($data['view_filter'])) {
			foreach ($data['view_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_filter SET view_id = '" . (int)$view_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['view_related'])) {
			foreach ($data['view_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$view_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related SET view_id = '" . (int)$view_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$related_id . "' AND related_id = '" . (int)$view_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related SET view_id = '" . (int)$related_id . "', related_id = '" . (int)$view_id . "'");
			}
		}
		
		if (isset($data['view_related_article'])) {
			foreach ($data['view_related_article'] as $article_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related_article WHERE view_id = '" . (int)$view_id . "' AND article_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related_article SET view_id = '" . (int)$view_id . "', article_id = '" . (int)$article_id . "'");
			}
		}

		if (isset($data['view_reward'])) {
			foreach ($data['view_reward'] as $customer_group_id => $view_reward) {
				if ((int)$view_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "view_reward SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$view_reward['points'] . "'");
				}
			}
		}
		
		// SEO URL
		if (isset($data['view_seo_url'])) {
			foreach ($data['view_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'view_id=" . (int)$view_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
		
		if (isset($data['view_layout'])) {
			foreach ($data['view_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_layout SET view_id = '" . (int)$view_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}


		$this->cache->delete('view');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $view_id;
	}

	public function editView($view_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "view SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "view SET image = '" . $this->db->escape($data['image']) . "' WHERE view_id = '" . (int)$view_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_description WHERE view_id = '" . (int)$view_id . "'");

		foreach ($data['view_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "view_description SET view_id = '" . (int)$view_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_store WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_store'])) {
			foreach ($data['view_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_store SET view_id = '" . (int)$view_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "'");

		if (!empty($data['view_attribute'])) {
			foreach ($data['view_attribute'] as $view_attribute) {
				if ($view_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "' AND attribute_id = '" . (int)$view_attribute['attribute_id'] . "'");

					foreach ($view_attribute['view_attribute_description'] as $language_id => $view_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "view_attribute SET view_id = '" . (int)$view_id . "', attribute_id = '" . (int)$view_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($view_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_option WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_option_value WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_option'])) {
			foreach ($data['view_option'] as $view_option) {
				if ($view_option['type'] == 'select' || $view_option['type'] == 'radio' || $view_option['type'] == 'checkbox' || $view_option['type'] == 'image') {
					if (isset($view_option['view_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "view_option SET view_option_id = '" . (int)$view_option['view_option_id'] . "', view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', required = '" . (int)$view_option['required'] . "'");

						$view_option_id = $this->db->getLastId();

						foreach ($view_option['view_option_value'] as $view_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "view_option_value SET view_option_value_id = '" . (int)$view_option_value['view_option_value_id'] . "', view_option_id = '" . (int)$view_option_id . "', view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', option_value_id = '" . (int)$view_option_value['option_value_id'] . "', quantity = '" . (int)$view_option_value['quantity'] . "', subtract = '" . (int)$view_option_value['subtract'] . "', price = '" . (float)$view_option_value['price'] . "', price_prefix = '" . $this->db->escape($view_option_value['price_prefix']) . "', points = '" . (int)$view_option_value['points'] . "', points_prefix = '" . $this->db->escape($view_option_value['points_prefix']) . "', weight = '" . (float)$view_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($view_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "view_option SET view_option_id = '" . (int)$view_option['view_option_id'] . "', view_id = '" . (int)$view_id . "', option_id = '" . (int)$view_option['option_id'] . "', value = '" . $this->db->escape($view_option['value']) . "', required = '" . (int)$view_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "view_recurring` WHERE view_id = " . (int)$view_id);

		if (isset($data['view_recurring'])) {
			foreach ($data['view_recurring'] as $view_recurring) {
				$query = $this->db->query("SELECT `view_id` FROM `" . DB_PREFIX . "view_recurring` WHERE `view_id` = '" . (int)$view_id . "' AND `customer_group_id` = '" . (int)$view_recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$view_recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "view_recurring` SET `view_id` = '" . (int)$view_id . "', `customer_group_id` = '" . (int)$view_recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$view_recurring['recurring_id'] . "'");
				}				
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_discount WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_discount'])) {
			foreach ($data['view_discount'] as $view_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_discount SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$view_discount['customer_group_id'] . "', quantity = '" . (int)$view_discount['quantity'] . "', priority = '" . (int)$view_discount['priority'] . "', price = '" . (float)$view_discount['price'] . "', date_start = '" . $this->db->escape($view_discount['date_start']) . "', date_end = '" . $this->db->escape($view_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_special WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_special'])) {
			foreach ($data['view_special'] as $view_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_special SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$view_special['customer_group_id'] . "', priority = '" . (int)$view_special['priority'] . "', price = '" . (float)$view_special['price'] . "', date_start = '" . $this->db->escape($view_special['date_start']) . "', date_end = '" . $this->db->escape($view_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_image WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_image'])) {
			foreach ($data['view_image'] as $view_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_image SET view_id = '" . (int)$view_id . "', image = '" . $this->db->escape($view_image['image']) . "', sort_order = '" . (int)$view_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_download WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_download'])) {
			foreach ($data['view_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_download SET view_id = '" . (int)$view_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_category'])) {
			foreach ($data['view_category'] as $views_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_category SET view_id = '" . (int)$view_id . "', views_id = '" . (int)$views_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_filter WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['main_views_id']) && $data['main_views_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "' AND views_id = '" . (int)$data['main_views_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_category SET view_id = '" . (int)$view_id . "', views_id = '" . (int)$data['main_views_id'] . "', main_category = 1");
		} elseif (isset($data['view_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "view_to_category SET main_category = 1 WHERE view_id = '" . (int)$view_id . "' AND views_id = '" . (int)$data['view_category'][0] . "'");
		}
		
		if (isset($data['view_filter'])) {
			foreach ($data['view_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_filter SET view_id = '" . (int)$view_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE related_id = '" . (int)$view_id . "'");

		if (isset($data['view_related'])) {
			foreach ($data['view_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$view_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related SET view_id = '" . (int)$view_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$related_id . "' AND related_id = '" . (int)$view_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related SET view_id = '" . (int)$related_id . "', related_id = '" . (int)$view_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related_article WHERE view_id = '" . (int)$view_id . "'");
		
		if (isset($data['view_related_article'])) {
			foreach ($data['view_related_article'] as $article_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "view_related_article WHERE view_id = '" . (int)$view_id . "' AND article_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_related_article SET view_id = '" . (int)$view_id . "', article_id = '" . (int)$article_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "view_reward WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_reward'])) {
			foreach ($data['view_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "view_reward SET view_id = '" . (int)$view_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'view_id=" . (int)$view_id . "'");
		
		if (isset($data['view_seo_url'])) {
			foreach ($data['view_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'view_id=" . (int)$view_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_layout WHERE view_id = '" . (int)$view_id . "'");

		if (isset($data['view_layout'])) {
			foreach ($data['view_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "view_to_layout SET view_id = '" . (int)$view_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('view');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}
	
	public function editViewStatus($view_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "view SET status = '" . (int)$status . "', date_modified = NOW() WHERE view_id = '" . (int)$view_id . "'");
        
		$this->cache->delete('view');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
		
		return $view_id;
    }

	public function copyView($view_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "view p WHERE p.view_id = '" . (int)$view_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';
			$data['noindex'] = '0';

			$data['view_attribute'] = $this->getViewAttributes($view_id);
			$data['view_description'] = $this->getViewDescriptions($view_id);
			$data['view_discount'] = $this->getViewDiscounts($view_id);
			$data['view_filter'] = $this->getViewFilters($view_id);
			$data['view_image'] = $this->getViewImages($view_id);
			$data['view_option'] = $this->getViewOptions($view_id);
			$data['view_related'] = $this->getViewRelated($view_id);
			$data['view_related_article'] = $this->getArticleRelated($view_id);
			$data['view_reward'] = $this->getViewRewards($view_id);
			$data['view_special'] = $this->getViewSpecials($view_id);
			$data['view_category'] = $this->getViewCategories($view_id);
			$data['view_download'] = $this->getViewDownloads($view_id);
			$data['view_layout'] = $this->getViewLayouts($view_id);
			$data['view_store'] = $this->getViewStores($view_id);
			$data['view_recurrings'] = $this->getRecurrings($view_id);

			$this->addView($data);
		}
	}

	public function deleteView($view_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "view WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_description WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_discount WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_filter WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_image WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_option WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_option_value WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related WHERE related_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_related_article WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_reward WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_special WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_download WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_layout WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_to_store WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "view_recurring WHERE view_id = " . (int)$view_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE view_id = '" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'view_id=" . (int)$view_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_view WHERE view_id = '" . (int)$view_id . "'");

		$this->cache->delete('view');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

	public function getView($view_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) WHERE p.view_id = '" . (int)$view_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getViews($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			preg_match('/(.*)(WHERE pd\.language_id.*)/', $sql, $sql_crutch_matches);
		if (isset($sql_crutch_matches[2])) {
		$sql = $sql_crutch_matches[1] . " LEFT JOIN " . DB_PREFIX . "view_to_category p2c ON (p.view_id = p2c.view_id)" . $sql_crutch_matches[2];
		} else {
			$data['filter_category'] = null;
			}
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			if (!empty($data['filter_category']) && !empty($data['filter_sub_category'])) {
				$implode_data = array();
				
				$this->load->model('catalog/category');
				
				$categories = $this->model_catalog_category->getCategoriesChildren($data['filter_category']);
				
				foreach ($categories as $category) {
					$implode_data[] = "p2c.views_id = '" . (int)$category['views_id'] . "'";
				}
				
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
			} else {
				if ((int)$data['filter_category'] > 0) {
					$sql .= " AND p2c.views_id = '" . (int)$data['filter_category'] . "'";
				} else {
					$sql .= " AND p2c.views_id IS NULL";
				}
			}
		}
	
		if (isset($data['filter_manufacturer_id']) && !is_null($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}
		
		if (isset($data['filter_price_min']) && !is_null($data['filter_price_min'])) {
			$sql .= " AND p.price >= '" . (float)$data['filter_price_min'] . "'";
		}
		
		if (isset($data['filter_price_max']) && !is_null($data['filter_price_max'])) {
			$sql .= " AND p.price <= '" . (float)$data['filter_price_max'] . "'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}
		
		if (isset($data['filter_quantity_min']) && !is_null($data['filter_quantity_min'])) {
			$sql .= " AND p.quantity >= '" . (int)$data['filter_quantity_min'] . "'";
		}
		
		if (isset($data['filter_quantity_max']) && !is_null($data['filter_quantity_max'])) {
			$sql .= " AND p.quantity <= '" . (int)$data['filter_quantity_max'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_noindex']) && $data['filter_noindex'] !== '') {
			$sql .= " AND p.noindex = '" . (int)$data['filter_noindex'] . "'";
		}

		if (isset($data['filter_image']) && $data['filter_image'] !== '') {
			$sql .= " AND p.image IN ('', 'no_image.png')";
		}
		
		$sql .= " GROUP BY p.view_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.noindex',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getViewsByCategoryId($views_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) LEFT JOIN " . DB_PREFIX . "view_to_category p2c ON (p.view_id = p2c.view_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.views_id = '" . (int)$views_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getViewDescriptions($view_id) {
		$view_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_description WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_h1'	       => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $view_description_data;
	}

	public function getViewCategories($view_id) {
		$view_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_category_data[] = $result['views_id'];
		}

		return $view_category_data;
	}
	
	public function getViewMainCategoryId($view_id) {
		$query = $this->db->query("SELECT views_id FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "' AND main_category = '1' LIMIT 1");
		
		return ($query->num_rows ? (int)$query->row['views_id'] : 0);
	}

	public function getViewFilters($view_id) {
		$view_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_filter WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_filter_data[] = $result['filter_id'];
		}

		return $view_filter_data;
	}

	public function getViewAttributes($view_id) {
		$view_attribute_data = array();

		$view_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "' GROUP BY attribute_id");

		foreach ($view_attribute_query->rows as $view_attribute) {
			$view_attribute_description_data = array();

			$view_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_attribute WHERE view_id = '" . (int)$view_id . "' AND attribute_id = '" . (int)$view_attribute['attribute_id'] . "'");

			foreach ($view_attribute_description_query->rows as $view_attribute_description) {
				$view_attribute_description_data[$view_attribute_description['language_id']] = array('text' => $view_attribute_description['text']);
			}

			$view_attribute_data[] = array(
				'attribute_id'                  => $view_attribute['attribute_id'],
				'view_attribute_description' => $view_attribute_description_data
			);
		}

		return $view_attribute_data;
	}

	public function getViewOptions($view_id) {
		$view_option_data = array();

		$view_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "view_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.view_id = '" . (int)$view_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order ASC");

		foreach ($view_option_query->rows as $view_option) {
			$view_option_value_data = array();

			$view_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.view_option_id = '" . (int)$view_option['view_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($view_option_value_query->rows as $view_option_value) {
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

			$view_option_data[] = array(
				'view_option_id'    => $view_option['view_option_id'],
				'view_option_value' => $view_option_value_data,
				'option_id'            => $view_option['option_id'],
				'name'                 => $view_option['name'],
				'type'                 => $view_option['type'],
				'value'                => $view_option['value'],
				'required'             => $view_option['required']
			);
		}

		return $view_option_data;
	}

	public function getViewOptionValue($view_id, $view_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "view_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.view_id = '" . (int)$view_id . "' AND pov.view_option_value_id = '" . (int)$view_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getViewImages($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_image WHERE view_id = '" . (int)$view_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getViewDiscounts($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_discount WHERE view_id = '" . (int)$view_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getViewSpecials($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_special WHERE view_id = '" . (int)$view_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getViewRewards($view_id) {
		$view_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_reward WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $view_reward_data;
	}

	public function getViewDownloads($view_id) {
		$view_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_download WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_download_data[] = $result['download_id'];
		}

		return $view_download_data;
	}

	public function getViewStores($view_id) {
		$view_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_store WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_store_data[] = $result['store_id'];
		}

		return $view_store_data;
	}
	
	public function getViewSeoUrls($view_id) {
		$view_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'view_id=" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $view_seo_url_data;
	}
	
	public function getViewLayouts($view_id) {
		$view_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_layout WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $view_layout_data;
	}

	public function getViewRelated($view_id) {
		$view_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_related WHERE view_id = '" . (int)$view_id . "'");

		foreach ($query->rows as $result) {
			$view_related_data[] = $result['related_id'];
		}

		return $view_related_data;
	}
	
	public function getArticleRelated($view_id) {
		$article_related_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_related_article WHERE view_id = '" . (int)$view_id . "'");
		
		foreach ($query->rows as $result) {
			$article_related_data[] = $result['article_id'];
		}
		
		return $article_related_data;
	}

	public function getRecurrings($view_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "view_recurring` WHERE view_id = '" . (int)$view_id . "'");

		return $query->rows;
	}

	public function getTotalViews($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.view_id) AS total FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id)";

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "view_to_category p2c ON (p.view_id = p2c.view_id)";
		}
		
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			if (!empty($data['filter_category']) && !empty($data['filter_sub_category'])) {
				$implode_data = array();
				
				$this->load->model('catalog/category');
				
				$categories = $this->model_catalog_category->getCategoriesChildren($data['filter_category']);
				
				foreach ($categories as $category) {
					$implode_data[] = "p2c.views_id = '" . (int)$category['views_id'] . "'";
				}
				
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
			} else {
				if ((int)$data['filter_category'] > 0) {
					$sql .= " AND p2c.views_id = '" . (int)$data['filter_category'] . "'";
				} else {
					$sql .= " AND p2c.views_id IS NULL";
				}
			}
		}
		
		if (isset($data['filter_manufacturer_id']) && !is_null($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}
		
		if (isset($data['filter_price_min']) && !is_null($data['filter_price_min'])) {
			$sql .= " AND p.price >= '" . (float)$data['filter_price_min'] . "'";
		}
		
		if (isset($data['filter_price_max']) && !is_null($data['filter_price_max'])) {
			$sql .= " AND p.price <= '" . (float)$data['filter_price_max'] . "'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}
		
		if (isset($data['filter_quantity_min']) && !is_null($data['filter_quantity_min'])) {
			$sql .= " AND p.quantity >= '" . (int)$data['filter_quantity_min'] . "'";
		}
		
		if (isset($data['filter_quantity_max']) && !is_null($data['filter_quantity_max'])) {
			$sql .= " AND p.quantity <= '" . (int)$data['filter_quantity_max'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_noindex']) && $data['filter_noindex'] !== '') {
			$sql .= " AND p.noindex = '" . (int)$data['filter_noindex'] . "'";
		}

		if (isset($data['filter_image']) && $data['filter_image'] !== '') {
			$sql .= " AND p.image IN ('', 'no_image.png')";
		}
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalViewsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalViewsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "view_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getVariants($view_id) {
		
		$query = $this->db->query("

		SELECT * FROM " . DB_PREFIX . "variants v

		LEFT JOIN " . DB_PREFIX . "product_description pd ON (v.product_id = pd.product_id)
		
		WHERE v.view_id = '". $view_id ."'

		");

		return $query->rows;
	}

}
