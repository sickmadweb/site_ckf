<?php
class ModelCatalogOffers extends Model {
	public function getOffers($offers_id) {
		$query = $this->db->query("
		SELECT DISTINCT * , (SELECT views_id FROM " . DB_PREFIX . "views WHERE offers_id = '" . (int)$offers_id . "') AS views_id
		
		FROM " . DB_PREFIX . "offers c 
		
		LEFT JOIN " . DB_PREFIX . "offers_description cd ON (c.offers_id = cd.offers_id) 
		LEFT JOIN " . DB_PREFIX . "offers_to_store c2s ON (c.offers_id = c2s.offers_id) 
		
		WHERE c.offers_id = '" . (int)$offers_id . "' 
		AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'
		
		");

		return $query->row;
	}

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers c LEFT JOIN " . DB_PREFIX . "offers_description cd ON (c.offers_id = cd.offers_id) LEFT JOIN " . DB_PREFIX . "offers_to_store c2s ON (c.offers_id = c2s.offers_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function getOffersFilters($offers_id) {
		$implode = array();

		$query = $this->db->query("

		SELECT DISTINCT(of.filter_id) FROM " . DB_PREFIX . "offer_filter of

		LEFT JOIN " . DB_PREFIX . "offer o ON of.offer_id = o.offer_id
		LEFT JOIN " . DB_PREFIX . "offer_to_category o2c ON o.offer_id = o2c.offer_id

		WHERE o2c.offers_id = '" . (int)$offers_id . "'


		");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name, fd.color, fd.url FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'color'            => $filter['color'],
						'url'            => $filter['url'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}



		return $filter_group_data;
	}

	public function getProductsFilters($offers_id) {

		$filter_data = array();

		$filter_group_data = array();

		$query = $this->db->query("

		SELECT pf.product_id, fd.color, fd.url, pf.filter_id, fd.name AS filter_name, fgd.filter_group_id, fgd.name AS group_name

		FROM " . DB_PREFIX . "product_filter pf
		
		LEFT JOIN " . DB_PREFIX . "variants v ON pf.product_id = v.product_id
		
		LEFT JOIN " . DB_PREFIX . "filter_description fd ON pf.filter_id = fd.filter_id
		LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON  fgd.filter_group_id =fd.filter_group_id
		
		
		
		WHERE v.offer_id IN ( SELECT offer_id FROM " . DB_PREFIX . "offer_to_category WHERE offers_id = '" . (int)$offers_id . "' )
		AND fgd.language_id = 1
		AND fd.language_id = 1
		
		GROUP BY pf.filter_id

		");

		foreach ($query->rows as $result) {
	
			
			$filter_data[$result['filter_group_id']][] = array(
				'filter_id' => $result['filter_id'],
				'color'            => $result['color'],
				'url'            => $result['url'],
				'name'      => $result['filter_name']
			);

			$filter_group_data[] = array(
				'filter_group_id' => $result['filter_group_id'],
				'name'            => $result['group_name'],
				'filter'          => $filter_data[$result['filter_group_id']]
			);

		}

		return $filter_group_data;
	}


	public function getOffersLayoutId($offers_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "offers_to_layout WHERE offers_id = '" . (int)$offers_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByOffersId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "offers c LEFT JOIN " . DB_PREFIX . "offers_to_store c2s ON (c.offers_id = c2s.offers_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
}