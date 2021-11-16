<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogView extends Model {
	public function updateViewed($view_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "view SET viewed = (viewed + 1) WHERE view_id = '" . (int)$view_id . "'");
	}

	public function getView($view_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, p.noindex AS noindex, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "view_discount pd2 WHERE pd2.view_id = p.view_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "view_special ps WHERE ps.view_id = p.view_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "view_reward pr WHERE pr.view_id = p.view_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, p.sort_order FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.view_id = '" . (int)$view_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'view_id'       => $query->row['view_id'],
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

	public function getViews($data = array()) {
		$sql = "SELECT p.view_id";

		if (!empty($data['filter_views_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "view_to_category p2c ON (cp.views_id = p2c.views_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "view_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "view_filter pf ON (p2c.view_id = pf.view_id) LEFT JOIN " . DB_PREFIX . "view p ON (pf.view_id = p.view_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "view p ON (p2c.view_id = p.view_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "view p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_views_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_views_id'] . "'";
			} else {
				$sql .= " AND p2c.views_id = '" . (int)$data['filter_views_id'] . "'";
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

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$sql .= " GROUP BY p.view_id";

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

		$view_data = array();




		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$view_data[$result['view_id']] = $this->getView($result['view_id']);
		}

		return $view_data;


	}

	public function getViewSpecials($data = array()) {
		$sql = "SELECT DISTINCT ps.view_id, FROM " . DB_PREFIX . "view_special ps LEFT JOIN " . DB_PREFIX . "view p ON (ps.view_id = p.view_id) LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.view_id";

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

		$view_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$view_data[$result['view_id']] = $this->getView($result['view_id']);
		}

		return $view_data;
	}

	public function getLatestViews($limit) {
		$view_data = $this->cache->get('view.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$view_data) {
			$query = $this->db->query("SELECT p.view_id FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$view_data[$result['view_id']] = $this->getView($result['view_id']);
			}

			$this->cache->set('view.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $view_data);
		}

		return $view_data;
	}

	public function getPopularViews($limit) {
		$view_data = $this->cache->get('view.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
	
		if (!$view_data) {
			$query = $this->db->query("SELECT p.view_id FROM " . DB_PREFIX . "view p LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
	
			foreach ($query->rows as $result) {
				$view_data[$result['view_id']] = $this->getView($result['view_id']);
			}
			
			$this->cache->set('view.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $view_data);
		}
		
		return $view_data;
	}

	public function getBestSellerViews($limit) {
		$view_data = $this->cache->get('view.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$view_data) {
			$view_data = array();

			$query = $this->db->query("SELECT op.view_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_view op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "view` p ON (op.view_id = p.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.view_id ORDER BY total DESC LIMIT " . (int)$limit);

			foreach ($query->rows as $result) {
				$view_data[$result['view_id']] = $this->getView($result['view_id']);
			}

			$this->cache->set('view.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $view_data);
		}

		return $view_data;
	}

	public function getViewAttributes($view_id) {
		$view_attribute_group_data = array();

		$view_attribute_group_query = $this->db->query("
		SELECT ag.attribute_group_id, agd.name 
		
		FROM " . DB_PREFIX . "product_attribute pa 
		LEFT JOIN " . DB_PREFIX . "variants v ON pa.product_id =v.product_id
		LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) 
		LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) 
		LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) 
		
		WHERE v.view_id = '" . (int)$view_id . "' 
		AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
		
		GROUP BY ag.attribute_group_id 
		
		ORDER BY ag.sort_order, agd.name
		
		
		");

		foreach ($view_attribute_group_query->rows as $view_attribute_group) {
			$view_attribute_data = array();

			$view_attribute_query = $this->db->query("
			
				SELECT a.attribute_id,ad.name, GROUP_CONCAT( DISTINCT(pa.text) SEPARATOR ', ' ) AS text

				FROM " . DB_PREFIX . "product_attribute pa 

				LEFT JOIN " . DB_PREFIX . "variants v ON pa.product_id =v.product_id 
				LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) 
				LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) 
				
				WHERE v.view_id = '" . (int)$view_id . "' 
				AND a.attribute_group_id = '" . (int)$view_attribute_group['attribute_group_id'] . "' 
				AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				
				GROUP BY  a.attribute_id

				ORDER BY a.sort_order, ad.name
			
			");

			foreach ($view_attribute_query->rows as $view_attribute) {
				$view_attribute_data[] = array(
					'attribute_id' => $view_attribute['attribute_id'],
					'name'         => $view_attribute['name'],
					'text'         => $view_attribute['text']
				);
			}

			$view_attribute_group_data[] = array(
				'attribute_group_id' => $view_attribute_group['attribute_group_id'],
				'name'               => $view_attribute_group['name'],
				'attribute'          => $view_attribute_data
			);
		}



		return $view_attribute_group_data;
	}

	public function getViewOptions($view_id) {
		$view_option_data = array();

		$view_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.view_id = '" . (int)$view_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($view_option_query->rows as $view_option) {
			$view_option_value_data = array();

			$view_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.view_id = '" . (int)$view_id . "' AND pov.view_option_id = '" . (int)$view_option['view_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($view_option_value_query->rows as $view_option_value) {
				$view_option_value_data[] = array(
					'view_option_value_id' => $view_option_value['view_option_value_id'],
					'option_value_id'         => $view_option_value['option_value_id'],
					'name'                    => $view_option_value['name'],
					'image'                   => $view_option_value['image'],
					'quantity'                => $view_option_value['quantity'],
					'subtract'                => $view_option_value['subtract'],
					'price'                   => $view_option_value['price'],
					'price_prefix'            => $view_option_value['price_prefix'],
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

	public function getViewDiscounts($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_discount WHERE view_id = '" . (int)$view_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");

		return $query->rows;
	}

	public function getViewImages($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_image WHERE view_id = '" . (int)$view_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getViewRelated($view_id) {
		$view_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_related pr LEFT JOIN " . DB_PREFIX . "view p ON (pr.related_id = p.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE pr.view_id = '" . (int)$view_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$view_data[$result['related_id']] = $this->getView($result['related_id']);
		}

		return $view_data;
	}

	public function getViewLayoutId($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_layout WHERE view_id = '" . (int)$view_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($view_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "view_to_category WHERE view_id = '" . (int)$view_id . "'");

		return $query->rows;
	}

	public function getTotalViews($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.view_id) AS total";

		if (!empty($data['filter_views_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "view_to_category p2c ON (cp.views_id = p2c.views_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "view_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "view_filter pf ON (p2c.view_id = pf.view_id) LEFT JOIN " . DB_PREFIX . "view p ON (pf.view_id = p.view_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "view p ON (p2c.view_id = p.view_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "view p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "view_description pd ON (p.view_id = pd.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_views_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_views_id'] . "'";
			} else {
				$sql .= " AND p2c.views_id = '" . (int)$data['filter_views_id'] . "'";
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

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_tag'])));

				foreach ($words as $word) {
					$implode[] = "pd.tag LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProfile($view_id, $recurring_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r JOIN " . DB_PREFIX . "view_recurring pr ON (pr.recurring_id = r.recurring_id AND pr.view_id = '" . (int)$view_id . "') WHERE pr.recurring_id = '" . (int)$recurring_id . "' AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

		return $query->row;
	}

	public function getProfiles($view_id) {
		$query = $this->db->query("SELECT rd.* FROM " . DB_PREFIX . "view_recurring pr JOIN " . DB_PREFIX . "recurring_description rd ON (rd.language_id = " . (int)$this->config->get('config_language_id') . " AND rd.recurring_id = pr.recurring_id) JOIN " . DB_PREFIX . "recurring r ON r.recurring_id = rd.recurring_id WHERE pr.view_id = " . (int)$view_id . " AND status = '1' AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getTotalViewSpecials() {
		$query = $this->db->query("SELECT COUNT(DISTINCT ps.view_id) AS total FROM " . DB_PREFIX . "view_special ps LEFT JOIN " . DB_PREFIX . "view p ON (ps.view_id = p.view_id) LEFT JOIN " . DB_PREFIX . "view_to_store p2s ON (p.view_id = p2s.view_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getVariants($view_id) {
		
		$query = $this->db->query("

			SELECT * FROM " . DB_PREFIX . "variants v

			LEFT JOIN " . DB_PREFIX . "offer_description pd ON (v.offer_id = pd.offer_id)
			LEFT JOIN " . DB_PREFIX . "offer p ON (v.offer_id = p.offer_id)
			LEFT JOIN " . DB_PREFIX . "offer_location_cache olc ON (v.offer_id = olc.offer_id)


			WHERE v.view_id = '". $view_id ."'
			AND olc.location_id ='". $this->session->data['location_id'] ."'

			GROUP BY v.offer_id

		");

		return $query->rows;
	}

	public function getNavigat($view_id) {
		
		$query = $this->db->query("

		SELECT 
		view_id AS id, 
		(SELECT MIN(view_id) FROM " . DB_PREFIX . "view) AS firts, 
		(SELECT MAX(view_id) FROM " . DB_PREFIX . "view) AS last,
		(SELECT view_id FROM " . DB_PREFIX . "view WHERE view_id > id ORDER BY view_id ASC  LIMIT 1) AS next,
		(SELECT view_id FROM " . DB_PREFIX . "view WHERE view_id < id ORDER BY view_id DESC LIMIT 1) AS prev
		
		FROM " . DB_PREFIX . "view
		
		WHERE view_id = '". $view_id ."'
		");

		return $query->row;
	}

	
	public function getVariantImage($view_id) {
		
		$query = $this->db->query("

		SELECT p.image FROM " . DB_PREFIX . "variants v

		LEFT JOIN " . DB_PREFIX . "product p ON v.product_id = p.product_id
		
		WHERE v.view_id = '". $view_id ."'
		 
		");

		return $query->rows;
	}


}
