<?php
namespace Cart;
class Currency {
	private $currencies = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->session = $registry->get('session');

		$this->language = $registry->get('language');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency");

		foreach ($query->rows as $result) {
			$this->currencies[$result['code']] = array(
				'currency_id'   => $result['currency_id'],
				'title'         => $result['title'],
				'symbol_left'   => $result['symbol_left'],
				'symbol_right'  => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value'         => $result['value']
			);
		}
	}

	public function format($number, $currency, $value = '', $format = true) {
		$symbol_left = $this->currencies[$currency]['symbol_left'];
		$symbol_right = $this->currencies[$currency]['symbol_right'];
		$decimal_place = $this->currencies[$currency]['decimal_place'];

		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		$amount = $value ? (float)$number * $value : (float)$number;
		
		$amount = round($amount, (int)$decimal_place);
		
		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, $this->language->get('decimal_point'), ' ');

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		return $string;
	}

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}
	
	public function getId($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['currency_id'];
		} else {
			return 0;
		}
	}

	public function getSymbolLeft($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}

	public function product_package($product_id ) {


		$query = $this->db->query("

		SELECT p.product_id, p.value, d1.name, d1.abbr, d1.package_id AS parent_id ,  p.parent_value, d2.name AS parent_name, d2.abbr AS parent_abbr , d2.package_id AS package_id
		
		FROM " . DB_PREFIX . "product_package p

		LEFT JOIN " . DB_PREFIX . "package_description d1 ON (p.parent_id = d1.package_id)
		LEFT JOIN " . DB_PREFIX . "package_description d2 ON (p.package_id = d2.package_id)
		WHERE product_id= '" . (int)$product_id . "'

		");

		return $query->rows;

	}

	public function local_price($product_id, $location_id = false ) {


		if ($location_id == false ) {
			$location_id = $this->session->data['location_id'];
		}

		$query = $this->db->query("

		SELECT price ,
		(
			SELECT MIN(price ) 
			FROM " . DB_PREFIX . "product_location_price, " . DB_PREFIX . "location 
			WHERE 
			" . DB_PREFIX . "product_location_price.location_id = " . DB_PREFIX . "location.location_id 
			AND " . DB_PREFIX . "product_location_price.product_id='" . (int)$product_id . "' AND " . DB_PREFIX . "location.area = 'ABK'
		) AS abk_price
		
		FROM " . DB_PREFIX . "product_location_price 
		
		WHERE product_id='" . (int)$product_id . "'
		AND   location_id = '".$location_id."'

		");

		return $query->row;

	}

	public function local_status($product_id, $location_id = false ) {

		$query = $this->db->query("

		SELECT ss.name AS status, pw.quantity, ss.visible,
		(
			SELECT SUM(" . DB_PREFIX . "product_to_warehouse.quantity) FROM " . DB_PREFIX . "product_to_warehouse, " . DB_PREFIX . "location
			WHERE 
			" . DB_PREFIX . "product_to_warehouse.location_id = " . DB_PREFIX . "location.location_id 
			AND product_id='" . (int)$product_id . "' AND " . DB_PREFIX . "location.area = 'ABK'
		) AS abk_quantity

		FROM " . DB_PREFIX . "product_to_warehouse pw
		LEFT JOIN " . DB_PREFIX . "stock_status ss ON pw.stock_status_id = ss.stock_status_id 	

		WHERE product_id='" . (int)$product_id . "' AND pw.location_id = '".$location_id."'

		");		
		
		return $query->row;

	}
	public function pricelist($product_id ) {

		$query = $this->db->query("

		SELECT " . DB_PREFIX . "product_pricelist_groups.group_id, " . DB_PREFIX . "product_pricelist_groups.price*" . DB_PREFIX . "product_package.parent_value AS pricelist_price 
		
		FROM " . DB_PREFIX . "hand_price_products, " . DB_PREFIX . "product_pricelist_groups, " . DB_PREFIX . "product_package


		WHERE " . DB_PREFIX . "hand_price_products.product_id = '" . (int)$product_id . "'
		AND " . DB_PREFIX . "product_package.product_id = " . DB_PREFIX . "hand_price_products.product_id
		AND " . DB_PREFIX . "hand_price_products.group_id = " . DB_PREFIX . "product_pricelist_groups.group_id

		");		
		
		return $query->row;

	}


	public function local_data($product_id, $location_id = false ) {

		if ($location_id == false ) {
			$location_id = $this->session->data['location_id'];
		}

		$data = array();

		$price = $this->local_price($product_id, $location_id);
				 
		$status = $this->local_status($product_id, $location_id);

		$pricelist = $this->pricelist($product_id);

		$data = array(
			'price'          => $price['price'],	
			'abk_price'      => $price['abk_price'],			
			'quantity'       => $status['quantity'],
			'status'         => $status['status'],
			'visible'         => $status['visible'],
			'abk_quantity'   => $status['abk_quantity'],
			'pricelist'      => isset($pricelist['pricelist_price']) ? $pricelist['pricelist_price'] : false,
			'pricelist_group_id'      => isset($pricelist['group_id']) ? $pricelist['group_id'] : false,
		);

		return $data;

	}

	
	


}
