<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogOffers extends Model {
	public function addOffers($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "offers SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW(), date_added = NOW()");

		$offers_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offers SET image = '" . $this->db->escape($data['image']) . "' WHERE offers_id = '" . (int)$offers_id . "'");
		}

		foreach ($data['offers_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "offers_description SET offers_id = '" . (int)$offers_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "offers_path` SET `offers_id` = '" . (int)$offers_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "offers_path` SET `offers_id` = '" . (int)$offers_id . "', `path_id` = '" . (int)$offers_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['offers_filter'])) {
			foreach ($data['offers_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_filter SET offers_id = '" . (int)$offers_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['offers_store'])) {
			foreach ($data['offers_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_to_store SET offers_id = '" . (int)$offers_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['offers_seo_url'])) {
			foreach ($data['offers_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'offers_id=" . (int)$offers_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
				
		// Set which layout to use with this offers
		if (isset($data['offers_layout'])) {
			foreach ($data['offers_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_to_layout SET offers_id = '" . (int)$offers_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('offers');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $offers_id;
	}

	public function editOffers($offers_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "offers SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW() WHERE offers_id = '" . (int)$offers_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "offers SET image = '" . $this->db->escape($data['image']) . "' WHERE offers_id = '" . (int)$offers_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_description WHERE offers_id = '" . (int)$offers_id . "'");

		foreach ($data['offers_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "offers_description SET offers_id = '" . (int)$offers_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE path_id = '" . (int)$offers_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $offers_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$offers_path['offers_id'] . "' AND level < '" . (int)$offers_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$offers_path['offers_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "offers_path` SET offers_id = '" . (int)$offers_path['offers_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$offers_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "offers_path` SET offers_id = '" . (int)$offers_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "offers_path` SET offers_id = '" . (int)$offers_id . "', `path_id` = '" . (int)$offers_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_filter WHERE offers_id = '" . (int)$offers_id . "'");

		if (isset($data['offers_filter'])) {
			foreach ($data['offers_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_filter SET offers_id = '" . (int)$offers_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_to_store WHERE offers_id = '" . (int)$offers_id . "'");

		if (isset($data['offers_store'])) {
			foreach ($data['offers_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_to_store SET offers_id = '" . (int)$offers_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'offers_id=" . (int)$offers_id . "'");

		if (isset($data['offers_seo_url'])) {
			foreach ($data['offers_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'offers_id=" . (int)$offers_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
						
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_to_layout WHERE offers_id = '" . (int)$offers_id . "'");

		if (isset($data['offers_layout'])) {
			foreach ($data['offers_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "offers_to_layout SET offers_id = '" . (int)$offers_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('offers');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}
	
	public function editOffersStatus($offers_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "offers SET status = '" . (int)$status . "', date_modified = NOW() WHERE offers_id = '" . (int)$offers_id . "'");
        
		$this->cache->delete('offers');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
		
    }

	public function deleteOffers($offers_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_path WHERE offers_id = '" . (int)$offers_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_path WHERE path_id = '" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteOffers($result['offers_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "offers WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_description WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_filter WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_to_store WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "offers_to_layout WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_offers WHERE offers_id = '" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'offers_id=" . (int)$offers_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_offers WHERE offers_id = '" . (int)$offers_id . "'");

		$this->cache->delete('offers');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $offers) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$offers['offers_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "offers_path` WHERE offers_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "offers_path` SET offers_id = '" . (int)$offers['offers_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "offers_path` SET offers_id = '" . (int)$offers['offers_id'] . "', `path_id` = '" . (int)$offers['offers_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($offers['offers_id']);
		}
	}

	public function getOffers($offers_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "offers_path cp LEFT JOIN " . DB_PREFIX . "offers_description cd1 ON (cp.path_id = cd1.offers_id AND cp.offers_id != cp.path_id) WHERE cp.offers_id = c.offers_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.offers_id) AS path FROM " . DB_PREFIX . "offers c LEFT JOIN " . DB_PREFIX . "offers_description cd2 ON (c.offers_id = cd2.offers_id) WHERE c.offers_id = '" . (int)$offers_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getAllCategories() {
		$result = $this->cache->get('offers.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if (!$result || !is_array($result)) {
			$query = $this->db->query("SELECT c.offers_id, c.parent_id, name FROM " . DB_PREFIX . "offers c LEFT JOIN " . DB_PREFIX . "offers_description cd ON (c.offers_id = cd.offers_id) LEFT JOIN " . DB_PREFIX . "offers_to_store c2s ON (c.offers_id = c2s.offers_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY c.parent_id, c.sort_order, cd.name");
		
			$categories = array();
		
			foreach ($query->rows as $row) {
				$categories[$row['parent_id']][$row['offers_id']] = $row;
			}
		
			$result = $this->getCategories($categories);
		
			$this->cache->set('offers.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $result);
		}

		return $result;
	}

	public function getCategories($data = array()) {
		$sql = "SELECT cp.offers_id AS offers_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.noindex FROM " . DB_PREFIX . "offers_path cp LEFT JOIN " . DB_PREFIX . "offers c1 ON (cp.offers_id = c1.offers_id) LEFT JOIN " . DB_PREFIX . "offers c2 ON (cp.path_id = c2.offers_id) LEFT JOIN " . DB_PREFIX . "offers_description cd1 ON (cp.path_id = cd1.offers_id) LEFT JOIN " . DB_PREFIX . "offers_description cd2 ON (cp.offers_id = cd2.offers_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.offers_id";

		$sort_data = array(
			'name',
			'sort_order',
			'noindex'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

	public function getOffersDescriptions($offers_id) {
		$offers_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_description WHERE offers_id = '" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$offers_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_h1'      	   => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $offers_description_data;
	}
	
	public function getOffersPath($offers_id) {
		$query = $this->db->query("SELECT offers_id, path_id, level FROM " . DB_PREFIX . "offers_path WHERE offers_id = '" . (int)$offers_id . "'");

		return $query->rows;
	}
	
	public function getOffersFilters($offers_id) {
		$offers_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_filter WHERE offers_id = '" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$offers_filter_data[] = $result['filter_id'];
		}

		return $offers_filter_data;
	}

	public function getOffersStores($offers_id) {
		$offers_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_to_store WHERE offers_id = '" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$offers_store_data[] = $result['store_id'];
		}

		return $offers_store_data;
	}
	
	public function getOffersSeoUrls($offers_id) {
		$offers_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'offers_id=" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$offers_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $offers_seo_url_data;
	}
	
	public function getOffersLayouts($offers_id) {
		$offers_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_to_layout WHERE offers_id = '" . (int)$offers_id . "'");

		foreach ($query->rows as $result) {
			$offers_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $offers_layout_data;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offers");

		return $query->row['total'];
	}
	
	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offers_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getCategoriesByParentId($parent_id = 0) {
		$query = $this->db->query("SELECT *, (SELECT COUNT(parent_id) FROM " . DB_PREFIX . "offers WHERE parent_id = c.offers_id) AS children FROM " . DB_PREFIX . "offers c LEFT JOIN " . DB_PREFIX . "offers_description cd ON (c.offers_id = cd.offers_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name");
		return $query->rows;
	}
	
	public function getCategoriesChildren($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_path WHERE path_id = '" . (int)$parent_id . "'");
		return $query->rows;
	}
		
}