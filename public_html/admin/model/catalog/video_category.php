<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogVideoCategory extends Model {
	public function addvideo_category($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "video_category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW(), date_added = NOW()");

		$video_category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "video_category SET image = '" . $this->db->escape($data['image']) . "' WHERE video_category_id = '" . (int)$video_category_id . "'");
		}

		foreach ($data['video_category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_description SET video_category_id = '" . (int)$video_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "video_category_path` SET `video_category_id` = '" . (int)$video_category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "video_category_path` SET `video_category_id` = '" . (int)$video_category_id . "', `path_id` = '" . (int)$video_category_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['video_category_filter'])) {
			foreach ($data['video_category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_filter SET video_category_id = '" . (int)$video_category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['video_category_store'])) {
			foreach ($data['video_category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_to_store SET video_category_id = '" . (int)$video_category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (isset($data['video_category_seo_url'])) {
			foreach ($data['video_category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'video_category_id=" . (int)$video_category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		
		// Set which layout to use with this video_category
		if (isset($data['video_category_layout'])) {
			foreach ($data['video_category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_to_layout SET video_category_id = '" . (int)$video_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('video_category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $video_category_id;
	}

	public function editvideo_category($video_category_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "video_category SET parent_id = '" . (int)$data['parent_id'] . "', `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', `column` = '" . (int)$data['column'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', noindex = '" . (int)$data['noindex'] . "', date_modified = NOW() WHERE video_category_id = '" . (int)$video_category_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "video_category SET image = '" . $this->db->escape($data['image']) . "' WHERE video_category_id = '" . (int)$video_category_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_description WHERE video_category_id = '" . (int)$video_category_id . "'");

		foreach ($data['video_category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_description SET video_category_id = '" . (int)$video_category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE path_id = '" . (int)$video_category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $video_category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$video_category_path['video_category_id'] . "' AND level < '" . (int)$video_category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$video_category_path['video_category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "video_category_path` SET video_category_id = '" . (int)$video_category_path['video_category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$video_category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "video_category_path` SET video_category_id = '" . (int)$video_category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "video_category_path` SET video_category_id = '" . (int)$video_category_id . "', `path_id` = '" . (int)$video_category_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_filter WHERE video_category_id = '" . (int)$video_category_id . "'");

		if (isset($data['video_category_filter'])) {
			foreach ($data['video_category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_filter SET video_category_id = '" . (int)$video_category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_to_store WHERE video_category_id = '" . (int)$video_category_id . "'");

		if (isset($data['video_category_store'])) {
			foreach ($data['video_category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_to_store SET video_category_id = '" . (int)$video_category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'video_category_id=" . (int)$video_category_id . "'");

		if (isset($data['video_category_seo_url'])) {
			foreach ($data['video_category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'video_category_id=" . (int)$video_category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_to_layout WHERE video_category_id = '" . (int)$video_category_id . "'");

		if (isset($data['video_category_layout'])) {
			foreach ($data['video_category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "video_category_to_layout SET video_category_id = '" . (int)$video_category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('video_category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}
	
	public function editvideo_categoryStatus($video_category_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "video_category SET status = '" . (int)$status . "', date_modified = NOW() WHERE video_category_id = '" . (int)$video_category_id . "'");
        
		$this->cache->delete('video_category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
		
    }

	public function deletevideo_category($video_category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_path WHERE video_category_id = '" . (int)$video_category_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_path WHERE path_id = '" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$this->deletevideo_category($result['video_category_id']);
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_description WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_filter WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_to_store WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "video_category_to_layout WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_video_category WHERE video_category_id = '" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'video_category_id=" . (int)$video_category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_video_category WHERE video_category_id = '" . (int)$video_category_id . "'");

		$this->cache->delete('video_category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

	public function repairCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category WHERE parent_id = '" . (int)$parent_id . "'");

		foreach ($query->rows as $video_category) {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$video_category['video_category_id'] . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "video_category_path` WHERE video_category_id = '" . (int)$parent_id . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "video_category_path` SET video_category_id = '" . (int)$video_category['video_category_id'] . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "video_category_path` SET video_category_id = '" . (int)$video_category['video_category_id'] . "', `path_id` = '" . (int)$video_category['video_category_id'] . "', level = '" . (int)$level . "'");

			$this->repairCategories($video_category['video_category_id']);
		}
	}

	public function getvideo_category($video_category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "video_category_path cp LEFT JOIN " . DB_PREFIX . "video_category_description cd1 ON (cp.path_id = cd1.video_category_id AND cp.video_category_id != cp.path_id) WHERE cp.video_category_id = c.video_category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.video_category_id) AS path FROM " . DB_PREFIX . "video_category c LEFT JOIN " . DB_PREFIX . "video_category_description cd2 ON (c.video_category_id = cd2.video_category_id) WHERE c.video_category_id = '" . (int)$video_category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getAllCategories() {
		$result = $this->cache->get('video_category.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if (!$result || !is_array($result)) {
			$query = $this->db->query("SELECT c.video_category_id, c.parent_id, name FROM " . DB_PREFIX . "video_category c LEFT JOIN " . DB_PREFIX . "video_category_description cd ON (c.video_category_id = cd.video_category_id) LEFT JOIN " . DB_PREFIX . "video_category_to_store c2s ON (c.video_category_id = c2s.video_category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY c.parent_id, c.sort_order, cd.name");
		
			$categories = array();
		
			foreach ($query->rows as $row) {
				$categories[$row['parent_id']][$row['video_category_id']] = $row;
			}
		
			$result = $this->getCategories($categories);
		
			$this->cache->set('video_category.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $result);
		}

		return $result;
	}

	public function getCategories($data = array()) {
		$sql = "SELECT cp.video_category_id AS video_category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.noindex FROM " . DB_PREFIX . "video_category_path cp LEFT JOIN " . DB_PREFIX . "video_category c1 ON (cp.video_category_id = c1.video_category_id) LEFT JOIN " . DB_PREFIX . "video_category c2 ON (cp.path_id = c2.video_category_id) LEFT JOIN " . DB_PREFIX . "video_category_description cd1 ON (cp.path_id = cd1.video_category_id) LEFT JOIN " . DB_PREFIX . "video_category_description cd2 ON (cp.video_category_id = cd2.video_category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY cp.video_category_id";

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

	public function getvideo_categoryDescriptions($video_category_id) {
		$video_category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_description WHERE video_category_id = '" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$video_category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_title'       => $result['meta_title'],
				'meta_h1'      	   => $result['meta_h1'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'description'      => $result['description']
			);
		}

		return $video_category_description_data;
	}
	
	public function getvideo_categoryPath($video_category_id) {
		$query = $this->db->query("SELECT video_category_id, path_id, level FROM " . DB_PREFIX . "video_category_path WHERE video_category_id = '" . (int)$video_category_id . "'");

		return $query->rows;
	}
	
	public function getvideo_categoryFilters($video_category_id) {
		$video_category_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_filter WHERE video_category_id = '" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$video_category_filter_data[] = $result['filter_id'];
		}

		return $video_category_filter_data;
	}

	public function getvideo_categoryStores($video_category_id) {
		$video_category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_to_store WHERE video_category_id = '" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$video_category_store_data[] = $result['store_id'];
		}

		return $video_category_store_data;
	}
	
	public function getvideo_categorySeoUrls($video_category_id) {
		$video_category_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'video_category_id=" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$video_category_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $video_category_seo_url_data;
	}

	
	public function getvideo_categoryLayouts($video_category_id) {
		$video_category_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_to_layout WHERE video_category_id = '" . (int)$video_category_id . "'");

		foreach ($query->rows as $result) {
			$video_category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $video_category_layout_data;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "video_category");

		return $query->row['total'];
	}
	
	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "video_category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	public function getCategoriesByParentId($parent_id = 0) {
		$query = $this->db->query("SELECT *, (SELECT COUNT(parent_id) FROM " . DB_PREFIX . "video_category WHERE parent_id = c.video_category_id) AS children FROM " . DB_PREFIX . "video_category c LEFT JOIN " . DB_PREFIX . "video_category_description cd ON (c.video_category_id = cd.video_category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name");
		return $query->rows;
	}
	
	public function getCategoriesChildren($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "video_category_path WHERE path_id = '" . (int)$parent_id . "'");
		return $query->rows;
	}
	
}