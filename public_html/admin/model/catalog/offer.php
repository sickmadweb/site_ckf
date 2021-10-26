<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogOffer extends Model {
	public function addOffer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "offer SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW(), date_modified = NOW()");

		$offer_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offer SET image = '" . $this->db->escape($data['image']) . "' WHERE offer_id = '" . (int)$offer_id . "'");
		}

		foreach ($data['offer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "offer_description SET offer_id = '" . (int)$offer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['offer_store'])) {
			foreach ($data['offer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_store SET offer_id = '" . (int)$offer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['offer_attribute'])) {
			foreach ($data['offer_attribute'] as $offer_attribute) {
				if ($offer_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "' AND attribute_id = '" . (int)$offer_attribute['attribute_id'] . "'");

					foreach ($offer_attribute['offer_attribute_description'] as $language_id => $offer_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "' AND attribute_id = '" . (int)$offer_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "offer_attribute SET offer_id = '" . (int)$offer_id . "', attribute_id = '" . (int)$offer_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($offer_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['offer_option'])) {
			foreach ($data['offer_option'] as $offer_option) {
				if ($offer_option['type'] == 'select' || $offer_option['type'] == 'radio' || $offer_option['type'] == 'checkbox' || $offer_option['type'] == 'image') {
					if (isset($offer_option['offer_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option SET offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', required = '" . (int)$offer_option['required'] . "'");

						$offer_option_id = $this->db->getLastId();

						foreach ($offer_option['offer_option_value'] as $offer_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option_value SET offer_option_id = '" . (int)$offer_option_id . "', offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', option_value_id = '" . (int)$offer_option_value['option_value_id'] . "', quantity = '" . (int)$offer_option_value['quantity'] . "', subtract = '" . (int)$offer_option_value['subtract'] . "', price = '" . (float)$offer_option_value['price'] . "', price_prefix = '" . $this->db->escape($offer_option_value['price_prefix']) . "', points = '" . (int)$offer_option_value['points'] . "', points_prefix = '" . $this->db->escape($offer_option_value['points_prefix']) . "', weight = '" . (float)$offer_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($offer_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option SET offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', value = '" . $this->db->escape($offer_option['value']) . "', required = '" . (int)$offer_option['required'] . "'");
				}
			}
		}

		if (isset($data['offer_recurring'])) {
			foreach ($data['offer_recurring'] as $recurring) {

				$query = $this->db->query("SELECT `offer_id` FROM `" . DB_PREFIX . "offer_recurring` WHERE `offer_id` = '" . (int)$offer_id . "' AND `customer_group_id = '" . (int)$recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "offer_recurring` SET `offer_id` = '" . (int)$offer_id . "', customer_group_id = '" . (int)$recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$recurring['recurring_id'] . "'");
				}
			}
		}
		
		if (isset($data['offer_discount'])) {
			foreach ($data['offer_discount'] as $offer_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_discount SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$offer_discount['customer_group_id'] . "', quantity = '" . (int)$offer_discount['quantity'] . "', priority = '" . (int)$offer_discount['priority'] . "', price = '" . (float)$offer_discount['price'] . "', date_start = '" . $this->db->escape($offer_discount['date_start']) . "', date_end = '" . $this->db->escape($offer_discount['date_end']) . "'");
			}
		}

		if (isset($data['offer_special'])) {
			foreach ($data['offer_special'] as $offer_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_special SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$offer_special['customer_group_id'] . "', priority = '" . (int)$offer_special['priority'] . "', price = '" . (float)$offer_special['price'] . "', date_start = '" . $this->db->escape($offer_special['date_start']) . "', date_end = '" . $this->db->escape($offer_special['date_end']) . "'");
			}
		}

		if (isset($data['offer_image'])) {
			foreach ($data['offer_image'] as $offer_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_image SET offer_id = '" . (int)$offer_id . "', image = '" . $this->db->escape($offer_image['image']) . "', sort_order = '" . (int)$offer_image['sort_order'] . "'");
			}
		}

		if (isset($data['offer_download'])) {
			foreach ($data['offer_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_download SET offer_id = '" . (int)$offer_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['offer_category'])) {
			foreach ($data['offer_category'] as $offers_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_category SET offer_id = '" . (int)$offer_id . "', offers_id = '" . (int)$offers_id . "'");
			}
		}
		
		if (isset($data['main_offers_id']) && $data['main_offers_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "' AND offers_id = '" . (int)$data['main_offers_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_category SET offer_id = '" . (int)$offer_id . "', offers_id = '" . (int)$data['main_offers_id'] . "', main_category = 1");
				} elseif (isset($data['offer_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offer_to_category SET main_category = 1 WHERE offer_id = '" . (int)$offer_id . "' AND offers_id = '" . (int)$data['offer_category'][0] . "'");
		}

		if (isset($data['offer_filter'])) {
			foreach ($data['offer_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_filter SET offer_id = '" . (int)$offer_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['offer_related'])) {
			foreach ($data['offer_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$offer_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related SET offer_id = '" . (int)$offer_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$related_id . "' AND related_id = '" . (int)$offer_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related SET offer_id = '" . (int)$related_id . "', related_id = '" . (int)$offer_id . "'");
			}
		}
		
		if (isset($data['offer_related_article'])) {
			foreach ($data['offer_related_article'] as $article_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related_article WHERE offer_id = '" . (int)$offer_id . "' AND article_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related_article SET offer_id = '" . (int)$offer_id . "', article_id = '" . (int)$article_id . "'");
			}
		}

		if (isset($data['offer_reward'])) {
			foreach ($data['offer_reward'] as $customer_group_id => $offer_reward) {
				if ((int)$offer_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "offer_reward SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$offer_reward['points'] . "'");
				}
			}
		}
		
		// SEO URL
		if (isset($data['offer_seo_url'])) {
			foreach ($data['offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'offer_id=" . (int)$offer_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
		
		if (isset($data['offer_layout'])) {
			foreach ($data['offer_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_layout SET offer_id = '" . (int)$offer_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}


		$this->cache->delete('offer');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $offer_id;
	}

	public function editOffer($offer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "offer SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offer SET image = '" . $this->db->escape($data['image']) . "' WHERE offer_id = '" . (int)$offer_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_description WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($data['offer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "offer_description SET offer_id = '" . (int)$offer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_store WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_store'])) {
			foreach ($data['offer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_store SET offer_id = '" . (int)$offer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "'");

		if (!empty($data['offer_attribute'])) {
			foreach ($data['offer_attribute'] as $offer_attribute) {
				if ($offer_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "' AND attribute_id = '" . (int)$offer_attribute['attribute_id'] . "'");

					foreach ($offer_attribute['offer_attribute_description'] as $language_id => $offer_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "offer_attribute SET offer_id = '" . (int)$offer_id . "', attribute_id = '" . (int)$offer_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($offer_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_option WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_option_value WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_option'])) {
			foreach ($data['offer_option'] as $offer_option) {
				if ($offer_option['type'] == 'select' || $offer_option['type'] == 'radio' || $offer_option['type'] == 'checkbox' || $offer_option['type'] == 'image') {
					if (isset($offer_option['offer_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option SET offer_option_id = '" . (int)$offer_option['offer_option_id'] . "', offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', required = '" . (int)$offer_option['required'] . "'");

						$offer_option_id = $this->db->getLastId();

						foreach ($offer_option['offer_option_value'] as $offer_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option_value SET offer_option_value_id = '" . (int)$offer_option_value['offer_option_value_id'] . "', offer_option_id = '" . (int)$offer_option_id . "', offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', option_value_id = '" . (int)$offer_option_value['option_value_id'] . "', quantity = '" . (int)$offer_option_value['quantity'] . "', subtract = '" . (int)$offer_option_value['subtract'] . "', price = '" . (float)$offer_option_value['price'] . "', price_prefix = '" . $this->db->escape($offer_option_value['price_prefix']) . "', points = '" . (int)$offer_option_value['points'] . "', points_prefix = '" . $this->db->escape($offer_option_value['points_prefix']) . "', weight = '" . (float)$offer_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($offer_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "offer_option SET offer_option_id = '" . (int)$offer_option['offer_option_id'] . "', offer_id = '" . (int)$offer_id . "', option_id = '" . (int)$offer_option['option_id'] . "', value = '" . $this->db->escape($offer_option['value']) . "', required = '" . (int)$offer_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "offer_recurring` WHERE offer_id = " . (int)$offer_id);

		if (isset($data['offer_recurring'])) {
			foreach ($data['offer_recurring'] as $offer_recurring) {
				$query = $this->db->query("SELECT `offer_id` FROM `" . DB_PREFIX . "offer_recurring` WHERE `offer_id` = '" . (int)$offer_id . "' AND `customer_group_id` = '" . (int)$offer_recurring['customer_group_id'] . "' AND `recurring_id` = '" . (int)$offer_recurring['recurring_id'] . "'");

				if (!$query->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "offer_recurring` SET `offer_id` = '" . (int)$offer_id . "', `customer_group_id` = '" . (int)$offer_recurring['customer_group_id'] . "', `recurring_id` = '" . (int)$offer_recurring['recurring_id'] . "'");
				}				
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_discount WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_discount'])) {
			foreach ($data['offer_discount'] as $offer_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_discount SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$offer_discount['customer_group_id'] . "', quantity = '" . (int)$offer_discount['quantity'] . "', priority = '" . (int)$offer_discount['priority'] . "', price = '" . (float)$offer_discount['price'] . "', date_start = '" . $this->db->escape($offer_discount['date_start']) . "', date_end = '" . $this->db->escape($offer_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_special WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_special'])) {
			foreach ($data['offer_special'] as $offer_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_special SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$offer_special['customer_group_id'] . "', priority = '" . (int)$offer_special['priority'] . "', price = '" . (float)$offer_special['price'] . "', date_start = '" . $this->db->escape($offer_special['date_start']) . "', date_end = '" . $this->db->escape($offer_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_image WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_image'])) {
			foreach ($data['offer_image'] as $offer_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_image SET offer_id = '" . (int)$offer_id . "', image = '" . $this->db->escape($offer_image['image']) . "', sort_order = '" . (int)$offer_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_download WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_download'])) {
			foreach ($data['offer_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_download SET offer_id = '" . (int)$offer_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_category'])) {
			foreach ($data['offer_category'] as $offers_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_category SET offer_id = '" . (int)$offer_id . "', offers_id = '" . (int)$offers_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_filter WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['main_offers_id']) && $data['main_offers_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "' AND offers_id = '" . (int)$data['main_offers_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_category SET offer_id = '" . (int)$offer_id . "', offers_id = '" . (int)$data['main_offers_id'] . "', main_category = 1");
		} elseif (isset($data['offer_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offer_to_category SET main_category = 1 WHERE offer_id = '" . (int)$offer_id . "' AND offers_id = '" . (int)$data['offer_category'][0] . "'");
		}
		
		if (isset($data['offer_filter'])) {
			foreach ($data['offer_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_filter SET offer_id = '" . (int)$offer_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE related_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_related'])) {
			foreach ($data['offer_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$offer_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related SET offer_id = '" . (int)$offer_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$related_id . "' AND related_id = '" . (int)$offer_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related SET offer_id = '" . (int)$related_id . "', related_id = '" . (int)$offer_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related_article WHERE offer_id = '" . (int)$offer_id . "'");
		
		if (isset($data['offer_related_article'])) {
			foreach ($data['offer_related_article'] as $article_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related_article WHERE offer_id = '" . (int)$offer_id . "' AND article_id = '" . (int)$article_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_related_article SET offer_id = '" . (int)$offer_id . "', article_id = '" . (int)$article_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_reward WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_reward'])) {
			foreach ($data['offer_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "offer_reward SET offer_id = '" . (int)$offer_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'offer_id=" . (int)$offer_id . "'");
		
		if (isset($data['offer_seo_url'])) {
			foreach ($data['offer_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'offer_id=" . (int)$offer_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_layout WHERE offer_id = '" . (int)$offer_id . "'");

		if (isset($data['offer_layout'])) {
			foreach ($data['offer_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offer_to_layout SET offer_id = '" . (int)$offer_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('offer');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}
	
	public function editOfferStatus($offer_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "offer SET status = '" . (int)$status . "', date_modified = NOW() WHERE offer_id = '" . (int)$offer_id . "'");
        
		$this->cache->delete('offer');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
		
		return $offer_id;
    }

	public function copyOffer($offer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "offer p WHERE p.offer_id = '" . (int)$offer_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';
			$data['noindex'] = '0';

			$data['offer_attribute'] = $this->getOfferAttributes($offer_id);
			$data['offer_description'] = $this->getOfferDescriptions($offer_id);
			$data['offer_discount'] = $this->getOfferDiscounts($offer_id);
			$data['offer_filter'] = $this->getOfferFilters($offer_id);
			$data['offer_image'] = $this->getOfferImages($offer_id);
			$data['offer_option'] = $this->getOfferOptions($offer_id);
			$data['offer_related'] = $this->getOfferRelated($offer_id);
			$data['offer_related_article'] = $this->getArticleRelated($offer_id);
			$data['offer_reward'] = $this->getOfferRewards($offer_id);
			$data['offer_special'] = $this->getOfferSpecials($offer_id);
			$data['offer_category'] = $this->getOfferCategories($offer_id);
			$data['offer_download'] = $this->getOfferDownloads($offer_id);
			$data['offer_layout'] = $this->getOfferLayouts($offer_id);
			$data['offer_store'] = $this->getOfferStores($offer_id);
			$data['offer_recurrings'] = $this->getRecurrings($offer_id);

			$this->addOffer($data);
		}
	}

	public function deleteOffer($offer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_description WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_discount WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_filter WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_image WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_option WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_option_value WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related WHERE related_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_related_article WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_reward WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_special WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_download WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_layout WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_to_store WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offer_recurring WHERE offer_id = " . (int)$offer_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE offer_id = '" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'offer_id=" . (int)$offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_offer WHERE offer_id = '" . (int)$offer_id . "'");

		$this->cache->delete('offer');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

	public function getOffer($offer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) WHERE p.offer_id = '" . (int)$offer_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOffers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			preg_match('/(.*)(WHERE pd\.language_id.*)/', $sql, $sql_crutch_matches);
		if (isset($sql_crutch_matches[2])) {
		$sql = $sql_crutch_matches[1] . " LEFT JOIN " . DB_PREFIX . "offer_to_category p2c ON (p.offer_id = p2c.offer_id)" . $sql_crutch_matches[2];
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
					$implode_data[] = "p2c.offers_id = '" . (int)$category['offers_id'] . "'";
				}
				
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
			} else {
				if ((int)$data['filter_category'] > 0) {
					$sql .= " AND p2c.offers_id = '" . (int)$data['filter_category'] . "'";
				} else {
					$sql .= " AND p2c.offers_id IS NULL";
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
		
		$sql .= " GROUP BY p.offer_id";

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

	public function getOffersByCategoryId($offers_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_category p2c ON (p.offer_id = p2c.offer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.offers_id = '" . (int)$offers_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getOfferDescriptions($offer_id) {
		$offer_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_description WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_h1'	       => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $offer_description_data;
	}

	public function getOfferCategories($offer_id) {
		$offer_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_category_data[] = $result['offers_id'];
		}

		return $offer_category_data;
	}
	
	public function getOfferMainCategoryId($offer_id) {
		$query = $this->db->query("SELECT offers_id FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "' AND main_category = '1' LIMIT 1");
		
		return ($query->num_rows ? (int)$query->row['offers_id'] : 0);
	}

	public function getOfferFilters($offer_id) {
		$offer_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_filter WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_filter_data[] = $result['filter_id'];
		}

		return $offer_filter_data;
	}

	public function getOfferAttributes($offer_id) {
		$offer_attribute_data = array();

		$offer_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "' GROUP BY attribute_id");

		foreach ($offer_attribute_query->rows as $offer_attribute) {
			$offer_attribute_description_data = array();

			$offer_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_attribute WHERE offer_id = '" . (int)$offer_id . "' AND attribute_id = '" . (int)$offer_attribute['attribute_id'] . "'");

			foreach ($offer_attribute_description_query->rows as $offer_attribute_description) {
				$offer_attribute_description_data[$offer_attribute_description['language_id']] = array('text' => $offer_attribute_description['text']);
			}

			$offer_attribute_data[] = array(
				'attribute_id'                  => $offer_attribute['attribute_id'],
				'offer_attribute_description' => $offer_attribute_description_data
			);
		}

		return $offer_attribute_data;
	}

	public function getOfferOptions($offer_id) {
		$offer_option_data = array();

		$offer_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offer_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.offer_id = '" . (int)$offer_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order ASC");

		foreach ($offer_option_query->rows as $offer_option) {
			$offer_option_value_data = array();

			$offer_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.offer_option_id = '" . (int)$offer_option['offer_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($offer_option_value_query->rows as $offer_option_value) {
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

			$offer_option_data[] = array(
				'offer_option_id'    => $offer_option['offer_option_id'],
				'offer_option_value' => $offer_option_value_data,
				'option_id'            => $offer_option['option_id'],
				'name'                 => $offer_option['name'],
				'type'                 => $offer_option['type'],
				'value'                => $offer_option['value'],
				'required'             => $offer_option['required']
			);
		}

		return $offer_option_data;
	}

	public function getOfferOptionValue($offer_id, $offer_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "offer_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.offer_id = '" . (int)$offer_id . "' AND pov.offer_option_value_id = '" . (int)$offer_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOfferImages($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_image WHERE offer_id = '" . (int)$offer_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getOfferDiscounts($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_discount WHERE offer_id = '" . (int)$offer_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getOfferSpecials($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_special WHERE offer_id = '" . (int)$offer_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getOfferRewards($offer_id) {
		$offer_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_reward WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $offer_reward_data;
	}

	public function getOfferDownloads($offer_id) {
		$offer_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_download WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_download_data[] = $result['download_id'];
		}

		return $offer_download_data;
	}

	public function getOfferStores($offer_id) {
		$offer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_store WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_store_data[] = $result['store_id'];
		}

		return $offer_store_data;
	}
	
	public function getOfferSeoUrls($offer_id) {
		$offer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'offer_id=" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $offer_seo_url_data;
	}
	
	public function getOfferLayouts($offer_id) {
		$offer_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_layout WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $offer_layout_data;
	}

	public function getOfferRelated($offer_id) {
		$offer_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_related WHERE offer_id = '" . (int)$offer_id . "'");

		foreach ($query->rows as $result) {
			$offer_related_data[] = $result['related_id'];
		}

		return $offer_related_data;
	}
	
	public function getArticleRelated($offer_id) {
		$article_related_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_related_article WHERE offer_id = '" . (int)$offer_id . "'");
		
		foreach ($query->rows as $result) {
			$article_related_data[] = $result['article_id'];
		}
		
		return $article_related_data;
	}

	public function getRecurrings($offer_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offer_recurring` WHERE offer_id = '" . (int)$offer_id . "'");

		return $query->rows;
	}

	public function getTotalOffers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.offer_id) AS total FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id)";

		if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "offer_to_category p2c ON (p.offer_id = p2c.offer_id)";
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
					$implode_data[] = "p2c.offers_id = '" . (int)$category['offers_id'] . "'";
				}
				
				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
			} else {
				if ((int)$data['filter_category'] > 0) {
					$sql .= " AND p2c.offers_id = '" . (int)$data['filter_category'] . "'";
				} else {
					$sql .= " AND p2c.offers_id IS NULL";
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

	public function getTotalOffersByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalOffersByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offer_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getVariants($offer_id) {
		
		$query = $this->db->query("

		SELECT * FROM " . DB_PREFIX . "variants v

		LEFT JOIN " . DB_PREFIX . "product_description pd ON (v.product_id = pd.product_id)
		
		WHERE v.offer_id = '". $offer_id ."'

		");

		return $query->rows;
	}

}
