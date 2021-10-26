<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogViews extends Model {
	public function addViews($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "views SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW(), date_added = NOW()");

		$views_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "views SET image = '" . $this->db->escape($data['image']) . "' WHERE views_id = '" . (int)$views_id . "'");
		}

		foreach ($data['views_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "views_description SET views_id = '" . (int)$views_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "views_path` SET `views_id` = '" . (int)$views_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "views_path` SET `views_id` = '" . (int)$views_id . "', `path_id` = '" . (int)$views_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['views_filter'])) {
			foreach ($data['views_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_filter SET views_id = '" . (int)$views_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['views_store'])) {
			foreach ($data['views_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_to_store SET views_id = '" . (int)$views_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['views_seo_url'])) {
			foreach ($data['views_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'views_id=" . (int)$views_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
					
		// Set which layout to use with this views
		if (isset($data['views_layout'])) {
			foreach ($data['views_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_to_layout SET views_id = '" . (int)$views_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('views');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $views_id;
	}

	public function editViews($views_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "views SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW() WHERE views_id = '" . (int)$views_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "views SET image = '" . $this->db->escape($data['image']) . "' WHERE views_id = '" . (int)$views_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "views_description WHERE views_id = '" . (int)$views_id . "'");

		foreach ($data['views_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "views_description SET views_id = '" . (int)$views_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE path_id = '" . (int)$views_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $views_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$views_path['views_id'] . "' AND level < '" . (int)$views_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$views_path['views_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "views_path` SET views_id = '" . (int)$views_path['views_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$views_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "views_path` SET views_id = '" . (int)$views_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "views_path` SET views_id = '" . (int)$views_id . "', `path_id` = '" . (int)$views_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "views_filter WHERE views_id = '" . (int)$views_id . "'");

		if (isset($data['views_filter'])) {
			foreach ($data['views_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_filter SET views_id = '" . (int)$views_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "views_to_store WHERE views_id = '" . (int)$views_id . "'");

		if (isset($data['views_store'])) {
			foreach ($data['views_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_to_store SET views_id = '" . (int)$views_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'views_id=" . (int)$views_id . "'");

		if (isset($data['views_seo_url'])) {
			foreach ($data['views_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'views_id=" . (int)$views_id . "', keyword = '" . $this->db->escape(trim($keyword)) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_to_layout WHERE views_id = '" . (int)$views_id . "'");

		if (isset($data['views_layout'])) {
			foreach ($data['views_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "views_to_layout SET views_id = '" . (int)$views_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('views');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}
	
	public function editViewsStatus($views_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "views SET status = '" . (int)$status . "', date_modified = NOW() WHERE views_id = '" . (int)$views_id . "'");
        
		$this->cache->delete('views');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
		
    }

	public function deleteViews($views_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_path WHERE views_id = '" . (int)$views_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_path WHERE path_id = '" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteViews($result['views_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "views WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_description WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_filter WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_to_store WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "views_to_layout WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_views WHERE views_id = '" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'views_id=" . (int)$views_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_views WHERE views_id = '" . (int)$views_id . "'");

		$this->cache->delete('views');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $views) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$views['views_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "views_path` WHERE views_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "views_path` SET views_id = '" . (int)$views['views_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "views_path` SET views_id = '" . (int)$views['views_id'] . "', `path_id` = '" . (int)$views['views_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($views['views_id']);
		}
	}

	public function getViews($views_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "views_path cp LEFT JOIN " . DB_PREFIX . "views_description cd1 ON (cp.path_id = cd1.views_id AND cp.views_id != cp.path_id) WHERE cp.views_id = c.views_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.views_id) AS path FROM " . DB_PREFIX . "views c LEFT JOIN " . DB_PREFIX . "views_description cd2 ON (c.views_id = cd2.views_id) WHERE c.views_id = '" . (int)$views_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getAllCategories() {
		$result = $this->cache->get('views.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if (!$result || !is_array($result)) {
			$query = $this->db->query("SELECT c.views_id, c.parent_id, name FROM " . DB_PREFIX . "views c LEFT JOIN " . DB_PREFIX . "views_description cd ON (c.views_id = cd.views_id) LEFT JOIN " . DB_PREFIX . "views_to_store c2s ON (c.views_id = c2s.views_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY c.parent_id, c.sort_order, cd.name");
		
			$categories = array();
		
			foreach ($query->rows as $row) {
				$categories[$row['parent_id']][$row['views_id']] = $row;
			}
		
			$result = $this->getCategories($categories);
		
			$this->cache->set('views.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $result);
		}

		return $result;
	}

	public function getCategories($data = array()) {
		$sql = "SELECT cp.views_id AS views_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.noindex FROM " . DB_PREFIX . "views_path cp LEFT JOIN " . DB_PREFIX . "views c1 ON (cp.views_id = c1.views_id) LEFT JOIN " . DB_PREFIX . "views c2 ON (cp.path_id = c2.views_id) LEFT JOIN " . DB_PREFIX . "views_description cd1 ON (cp.path_id = cd1.views_id) LEFT JOIN " . DB_PREFIX . "views_description cd2 ON (cp.views_id = cd2.views_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.views_id";

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

	public function getViewsDescriptions($views_id) {
		$views_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_description WHERE views_id = '" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$views_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_h1'      	   => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $views_description_data;
	}
	
	public function getViewsPath($views_id) {
		$query = $this->db->query("SELECT views_id, path_id, level FROM " . DB_PREFIX . "views_path WHERE views_id = '" . (int)$views_id . "'");

		return $query->rows;
	}
	
	public function getViewsFilters($views_id) {
		$views_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_filter WHERE views_id = '" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$views_filter_data[] = $result['filter_id'];
		}

		return $views_filter_data;
	}

	public function getViewsStores($views_id) {
		$views_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_to_store WHERE views_id = '" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$views_store_data[] = $result['store_id'];
		}

		return $views_store_data;
	}
	
	public function getViewsSeoUrls($views_id) {
		$views_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'views_id=" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$views_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $views_seo_url_data;
	}
			
	public function getViewsLayouts($views_id) {
		$views_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_to_layout WHERE views_id = '" . (int)$views_id . "'");

		foreach ($query->rows as $result) {
			$views_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $views_layout_data;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "views");

		return $query->row['total'];
	}
	
	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "views_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getCategoriesByParentId($parent_id = 0) {
		$query = $this->db->query("SELECT *, (SELECT COUNT(parent_id) FROM " . DB_PREFIX . "views WHERE parent_id = c.views_id) AS children FROM " . DB_PREFIX . "views c LEFT JOIN " . DB_PREFIX . "views_description cd ON (c.views_id = cd.views_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name");
		return $query->rows;
	}
	
	public function getCategoriesChildren($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "views_path WHERE path_id = '" . (int)$parent_id . "'");
		return $query->rows;
	}
	
}