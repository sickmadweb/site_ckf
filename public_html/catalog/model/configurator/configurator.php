<?php
class ModelConfiguratorConfigurator extends Model {


	public function getHouses() {

		$query = $this->db->query("

		SELECT * FROM conf_house
		
		");

		return $query->rows;
	}

	public function getHouse($house_id) {

		$query = $this->db->query("

		SELECT * FROM conf_house
		WHERE house_id = ". (int)$house_id ."
		
		");

		return $query->row;
	}
	

	public function getElements($house_id) {

		$query = $this->db->query("

		SELECT * FROM conf_elements

		ORDER BY sort
		");

		return $query->rows;
	}

	public function getMaterials($element_id) {

		$query = $this->db->query("

		SELECT * FROM conf_materials
		
		WHERE element_id= ". (int)$element_id ."
		

		");

		return $query->rows;
	}
	
	public function getSeries($element_id) {

		$query = $this->db->query("


		SELECT * FROM conf_series 
		
		WHERE material_id =". (int)$element_id ."

		
		");

		return $query->rows;
	}

	
	public function getViews($material_id) {

		$query = $this->db->query("

		SELECT *,v.name AS view , s.name AS serie, v.product_id FROM conf_views v

		LEFT JOIN conf_series s ON (v.series_id = s.series_id)
		LEFT JOIN conf_materials m ON (v.material_id = m.material_id)   

		WHERE v.material_id = ". $material_id ."
		
		");

		return $query->rows;
	}
	public function getItem($view_id , $house_id ) {
    
		$query = $this->db->query("
		SELECT *, i.name AS title ,  v.product_id AS product_id FROM conf_item i
        
        LEFT JOIN conf_views v ON (i.view_id = v.view_id)    	
        LEFT JOIN conf_materials m ON (v.material_id = m.material_id)       
        LEFT JOIN conf_elements e ON (m.element_id = e.element_id)

		WHERE 
		house_id = ". $house_id ."

		AND v.view_id = ". $view_id ."
		AND i.status = 1

		");

		return $query->rows;	
	}


	public function getItemInfo($item_id) {
    
		$query = $this->db->query("
		SELECT *, i.name AS title, m.element_id AS element_id , s.name AS seria, v.name AS collection, m.name AS material
		
		FROM conf_item i
        
        LEFT JOIN conf_views v ON (i.view_id = v.view_id)    	
        LEFT JOIN conf_materials m ON (v.material_id = m.material_id)       
        LEFT JOIN conf_elements e ON (m.element_id = e.element_id)
		LEFT JOIN ckf_manufacturer manuf ON (i.manufacturer_id = manuf.manufacturer_id)
        LEFT JOIN conf_series s ON (v.series_id = s.series_id)   

		WHERE 
		house_id = ". $this->request->get['house_id'] ."

		AND  i.item_id  = ". $item_id ."
		AND i.status = 1

		");	

		return $query->rows;	
	}	

	public function getImage($image_id) {
    
		$query = $this->db->query("


		SELECT i.item_id, i.image, i.name, v.name AS view, m.name AS material, s.name AS series, e.name AS elements FROM conf_item i

		LEFT JOIN conf_views v ON i.view_id = v.view_id
		LEFT JOIN conf_materials m ON v.material_id = m.material_id
		LEFT JOIN conf_series s ON v.series_id = s.series_id
		LEFT JOIN conf_elements e ON m.element_id = e.element_id
		
		WHERE i.item_id= ". $image_id ."


		");	

		return $query->row;	
	}	


}






