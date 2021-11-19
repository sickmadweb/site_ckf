<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogOffer extends Model {
	public function updateViewed($offer_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "offer SET viewed = (viewed + 1) WHERE offer_id = '" . (int)$offer_id . "'");
	}

	public function getOffer($offer_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, p.noindex AS noindex, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "offer_discount pd2 WHERE pd2.offer_id = p.offer_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "offer_special ps WHERE ps.offer_id = p.offer_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "offer_reward pr WHERE pr.offer_id = p.offer_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, p.sort_order FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.offer_id = '" . (int)$offer_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'offer_id'       => $query->row['offer_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'noindex'          => $query->row['noindex'],
				'meta_h1'	       => $query->row['meta_h1'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

	public function getOffers($data = array()) {
		$sql = "SELECT p.offer_id ";

		if (!empty($data['filter_offers_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "offer_to_category p2c ON (cp.offers_id = p2c.offers_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "offer_to_category p2c LEFT JOIN " . DB_PREFIX . "variants v ON p2c.offer_id = v.offer_id ";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " 
				LEFT JOIN " . DB_PREFIX . "product_filter pf ON (v.product_id = pf.product_id) 
				";

				$sql .= " LEFT JOIN " . DB_PREFIX . "offer p ON (v.offer_id = p.offer_id)";				
				
				$grouped_filters = $this->getGroupedFilters($data['filter_filter']);

				foreach ($grouped_filters as $filter_group_id => $filters) {
					$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf" . $filter_group_id . " ON (v.product_id = pf" . $filter_group_id . ".product_id)";					
				}

			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "offer p ON (p2c.offer_id = p.offer_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "offer p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_offers_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_offers_id'] . "'";
			} else {
				$sql .= " AND p2c.offers_id = '" . (int)$data['filter_offers_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
				

			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {

				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				
				$pdname = "REPLACE((REPLACE(REPLACE(pd.name, '(', ''), '-', ' ')), '\"', '')";

				foreach ($words as $word) {
	
					$implode[] = " ( ".$pdname." LIKE '" . $this->db->escape($word) . "%' OR ".$pdname." LIKE '% " . $this->db->escape($word) . "%' )";					
					
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

			}


			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY p.offer_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$offer_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$offer_data[$result['offer_id']] = $this->getOffer($result['offer_id']);
		}

		return $offer_data;
	}

	public function getOfferSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.offer_id, FROM " . DB_PREFIX . "offer_special ps LEFT JOIN " . DB_PREFIX . "offer p ON (ps.offer_id = p.offer_id) LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.offer_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$offer_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$offer_data[$result['offer_id']] = $this->getOffer($result['offer_id']);
		}

		return $offer_data;
	}

	public function getLatestOffers($limit) {
		$offer_data = $this->cache->get('offer.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$offer_data) {
			$query = $this->db->query("SELECT p.offer_id FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$offer_data[$result['offer_id']] = $this->getOffer($result['offer_id']);
			}

			$this->cache->set('offer.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $offer_data);
		}

		return $offer_data;
	}

	public function getPopularOffers($limit) {
		$offer_data = $this->cache->get('offer.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$offer_data) {
			$query = $this->db->query("SELECT p.offer_id FROM " . DB_PREFIX . "offer p LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$offer_data[$result['offer_id']] = $this->getOffer($result['offer_id']);
			}
			
			$this->cache->set('offer.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $offer_data);
		}
		
		return $offer_data;
	}

	public function getBestSellerOffers($limit) {
		$offer_data = $this->cache->get('offer.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$offer_data) {
			$offer_data = array();

			$query = $this->db->query("SELECT op.offer_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_offer op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "offer` p ON (op.offer_id = p.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.offer_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$offer_data[$result['offer_id']] = $this->getOffer($result['offer_id']);
			}

			$this->cache->set('offer.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $offer_data);
		}

		return $offer_data;
	}

	public function getOfferAttributes($offer_id) {
		$offer_attribute_group_data = array();

		$offer_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "offer_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.offer_id = '" . (int)$offer_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

		foreach ($offer_attribute_group_query->rows as $offer_attribute_group) {
			$offer_attribute_data = array();

			$offer_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "offer_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.offer_id = '" . (int)$offer_id . "' AND a.attribute_group_id = '" . (int)$offer_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($offer_attribute_query->rows as $offer_attribute) {
				$offer_attribute_data[] = array(
					'attribute_id' => $offer_attribute['attribute_id'],
					'name'         => $offer_attribute['name'],
					'text'         => $offer_attribute['text']
				);
			}

			$offer_attribute_group_data[] = array(
				'attribute_group_id' => $offer_attribute_group['attribute_group_id'],
				'name'               => $offer_attribute_group['name'],
				'attribute'          => $offer_attribute_data
			);
		}

		return $offer_attribute_group_data;
	}

	public function getOfferOptions($offer_id) {
		$offer_option_data = array();

		$offer_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.offer_id = '" . (int)$offer_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($offer_option_query->rows as $offer_option) {
			$offer_option_value_data = array();

			$offer_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.offer_id = '" . (int)$offer_id . "' AND pov.offer_option_id = '" . (int)$offer_option['offer_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($offer_option_value_query->rows as $offer_option_value) {
				$offer_option_value_data[] = array(
					'offer_option_value_id' => $offer_option_value['offer_option_value_id'],
					'option_value_id'         => $offer_option_value['option_value_id'],
					'name'                    => $offer_option_value['name'],
					'image'                   => $offer_option_value['image'],
					'quantity'                => $offer_option_value['quantity'],
					'subtract'                => $offer_option_value['subtract'],
					'price'                   => $offer_option_value['price'],
					'price_prefix'            => $offer_option_value['price_prefix'],
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

	public function getOfferDiscounts($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_discount WHERE offer_id = '" . (int)$offer_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getOfferImages($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_image WHERE offer_id = '" . (int)$offer_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getOfferRelated($offer_id) {
		$offer_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_related pr LEFT JOIN " . DB_PREFIX . "offer p ON (pr.related_id = p.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE pr.offer_id = '" . (int)$offer_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$offer_data[$result['related_id']] = $this->getOffer($result['related_id']);
		}

		return $offer_data;
	}

	public function getOfferLayoutId($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_layout WHERE offer_id = '" . (int)$offer_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offer_to_category WHERE offer_id = '" . (int)$offer_id . "'");

		return $query->rows;
	}

	public function getTotalOffers($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.offer_id) AS total";

		if (!empty($data['filter_offers_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "offer_to_category p2c ON (cp.offers_id = p2c.offers_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "offer_to_category p2c LEFT JOIN " . DB_PREFIX . "variants v ON p2c.offer_id = v.offer_id";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " 
				LEFT JOIN " . DB_PREFIX . "product_filter pf ON (v.product_id = pf.product_id) 
				";

				$sql .= " LEFT JOIN " . DB_PREFIX . "offer p ON (v.offer_id = p.offer_id)";				
				
				$grouped_filters = $this->getGroupedFilters($data['filter_filter']);

				foreach ($grouped_filters as $filter_group_id => $filters) {
					$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf" . $filter_group_id . " ON (v.product_id = pf" . $filter_group_id . ".product_id)";					
				}

			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "offer p ON (p2c.offer_id = p.offer_id)";
			}

		} else {
			$sql .= " FROM " . DB_PREFIX . "offer p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "offer_description pd ON (p.offer_id = pd.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_offers_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_offers_id'] . "'";
			} else {
				$sql .= " AND p2c.offers_id = '" . (int)$data['filter_offers_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {

				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				
				$pdname = "REPLACE((REPLACE(REPLACE(pd.name, '(', ''), '-', ' ')), '\"', '')";

				foreach ($words as $word) {
	
					$implode[] = " ( ".$pdname." LIKE '" . $this->db->escape($word) . "%' OR ".$pdname." LIKE '% " . $this->db->escape($word) . "%' )";					
					
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($offer_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "offer_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.offer_id = '" . (int)$offer_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($offer_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "offer_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.offer_id = " . (int)$offer_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalOfferSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.offer_id) AS total FROM " . DB_PREFIX . "offer_special ps LEFT JOIN " . DB_PREFIX . "offer p ON (ps.offer_id = p.offer_id) LEFT JOIN " . DB_PREFIX . "offer_to_store p2s ON (p.offer_id = p2s.offer_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getVariants($offer_id) {
		
		$query = $this->db->query("

		SELECT *, vd.description AS description FROM " . DB_PREFIX . "variants v

		LEFT JOIN " . DB_PREFIX . "product_description pd ON (v.product_id = pd.product_id)
		LEFT JOIN " . DB_PREFIX . "product p ON (v.product_id = p.product_id)
		LEFT JOIN " . DB_PREFIX . "view_description vd ON (v.view_id = vd.view_id)		

		WHERE v.offer_id = '". $offer_id ."'

		");

		return $query->rows;
	}

	public function getNavigat($offer_id) {
		
		$query = $this->db->query("

			SELECT 
			offer_id AS id, 
			(SELECT MIN(offer_id) FROM " . DB_PREFIX . "offer) AS firts, 
			(SELECT MAX(offer_id) FROM " . DB_PREFIX . "offer) AS last,
			(SELECT offer_id FROM " . DB_PREFIX . "offer WHERE offer_id > id ORDER BY offer_id ASC  LIMIT 1) AS next,
			(SELECT offer_id FROM " . DB_PREFIX . "offer WHERE offer_id < id ORDER BY offer_id DESC LIMIT 1) AS prev
			
			FROM " . DB_PREFIX . "offer
			
			WHERE offer_id = '". $offer_id ."'
		");

		return $query->row;
	}

	public function getFilters($offer_id) {

		$query = $this->db->query("

			SELECT pf.product_id, pf.filter_id, fd.name AS filter_name, fgd.filter_group_id, fgd.name AS group_name

			FROM " . DB_PREFIX . "product_filter pf
			
			LEFT JOIN " . DB_PREFIX . "variants v ON pf.product_id = v.product_id
			
			LEFT JOIN " . DB_PREFIX . "filter_description fd ON pf.filter_id = fd.filter_id
			LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON  fgd.filter_group_id =fd.filter_group_id
			
			WHERE v.offer_id = '". $offer_id ."'
			AND fgd.language_id = 1
			AND fd.language_id = 1
			
			GROUP BY pf.filter_id

		");

		return $query->rows;

	}
	
	private function getGroupedFilters ($filters) {
        
		$implode = array();

		$filters = explode(',', $filters);

		foreach ($filters as $filter_id) {
			$implode[] = (int)$filter_id;
		}
						
		$query = $this->db->query("SELECT `filter_id`, `filter_group_id` FROM `" . DB_PREFIX . "filter` WHERE `filter_id` IN (" . implode(',', $implode) . ")");

		$grouped_filters = array();
		foreach ($query->rows as $row) {
			$grouped_filters[$row['filter_group_id']][] = $row['filter_id'];
		}

		return $grouped_filters;
	}

}
