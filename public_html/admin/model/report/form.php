<?php
class ModelReportForm extends Model {

	public function getForms() {

		$query = $this->db->query("

			SELECT * FROM " . DB_PREFIX . "logs_forms

		");

		return $query->rows;

	}	
}
