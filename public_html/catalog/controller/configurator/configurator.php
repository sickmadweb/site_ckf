<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerConfiguratorConfigurator extends Controller {
	public function index() {
		$this->load->language('configurator/configurator');

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('configurator/configurator')
		);

		$data['houses'] = array();

		$results = $this->model_configurator_configurator->getHouses();

		foreach ($results as $result) {
			$image =$result['image'];

			$images = array(			
				'popup' => $this->model_tool_image->resize($image, 600, 400),
				'thumb' => $this->model_tool_image->resize($image, 150, 100)
			);

			$data['houses'][] = array(
				'house_id'   => $result['house_id'],
				'name'   => $result['name'],
				'images' => $images,
				'href'   => $this->url->link('configurator/configurator/assembly', 'house_id=' . $result['house_id'])
			);
		}

		$data['continue'] = $this->url->link('common/home');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('configurator/configurator_list', $data));
	}

	public function assembly() {

		$this->load->language('configurator/configurator');

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');


		if (isset($this->request->get['house_id'])) {
			$house_id = (int)$this->request->get['house_id'];
		} else {
			$house_id = 0;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('configurator/configurator')
		);

		$house_info = $this->model_configurator_configurator->getHouse($house_id);

		if ($house_info) {

			if (isset($house_info['image'])) {
				$data['thumb'] = $this->model_tool_image->resize($house_info['image'], 1500,1000 );
			} else {
				$data['thumb'] = '';
			}
			$data['thumb_none'] = $this->model_tool_image->resize('configurator/none.png', 1500,1000 );;			

			$data['breadcrumbs'][] = array(
				'text' => $house_info['name'],
				'href' => $this->url->link('configurator/configurator/info', 'house_id=' . $this->request->get['house_id'] )
			);

			$data['elements'] = array();

			$results = $this->model_configurator_configurator->getElements($house_id);

			$data['views'] = array();

            foreach ($results as $result) {

                $materials = $this->model_configurator_configurator->getMaterials($result['element_id']);

                $data['elements'][] = array(
                    'element_id'  => $result['element_id'],
                    'name'        => $result['name'],
					'sort'        => $result['sort'],
                    'materials'   => $materials,
                );
                
                $series = $this->model_configurator_configurator->getSeries($result['element_id']);

                $data['series'][$result['element_id']] = $series;
            }
		
			$views = $this->model_configurator_configurator->getViews($result['element_id']);


            foreach ($views as $view) {

				$products = array();
		
				$items = $this->model_configurator_configurator->getItem($view['view_id'], $this->request->get['house_id']);

                foreach ($items as $item) {

					$products[] = array(
						'item_id'  => $item['item_id'],
						'name'     => $item['name'],
						'prewiev'    => $this->model_tool_image->resize($item['prewiev'], 50 , 50),
					);

                }

				$data['views'][$view['series_id']][]  = array(
						'view_id'   => $view['view_id'],
						'name'      => $view['name'],
						'material_id'  => $view['material_id'],					
						'item'      => $products,

				);
			
            }

			$data['continue'] = $this->url->link('common/home');
			$data['house_id'] = $house_id;

			$data['header'] = $this->load->controller('configurator/header');
			$data['footer'] = $this->load->controller('configurator/footer');


			$this->response->setOutput($this->load->view('configurator/configurator_assembly', $data));

		} else {
			$url = '';

			if (isset($this->request->get['house_id'])) {
				$url .= '&house_id=' . $this->request->get['house_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('configurator/configurator/info', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function item( $view_id = 0, $house_id = 0 ) {

		$this->load->language('configurator/configurator');

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		if (isset($this->request->get['house_id'])) {
			$house_id = (int)$this->request->get['house_id'];
		} else {
			$house_id = 0;
		}



		if (isset($this->request->get['catalog_id'])) {
			$view_id = (int)$this->request->get['catalog_id'];
		} else {
			$view_id = 0;
		}
		$data['success'] = true;

		$data['items'] = array();
		
		$items = $this->model_configurator_configurator->getItem($view_id , $house_id );
	
//		print_r($items);
		$product_id = 5 ;
		foreach ($items as $item) {
			

			$data['items'][] = array(
				'item_id'  => $item['item_id'],
				'name'     => $item['title'],
				'prewiev'    => $this->model_tool_image->resize($item['prewiev'], 200 , 200),
				'image'    => $this->model_tool_image->resize($item['image'], 1500 , 1000),
				'element_id'     => $item['element_id'],
			);

		}


		$data['href'] = $this->url->link('product/view', 'view_id=' . $product_id) ;

		$data['html'] = $this->load->view('configurator/configurator_items', $data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
		 
	}

	public function materials() {

		$this->load->language('configurator/configurator');

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		if (isset($this->request->get['house_id'])) {
			$house_id = (int)$this->request->get['house_id'];
		} else {
			$house_id = 0;
		}



		if (isset($this->request->get['material_id'])) {
			$material_id = (int)$this->request->get['material_id'];
		} else {
			$material_id = 0;
		}

		$data['success'] = true;

		$data['house_id'] = $house_id;



		$views = $this->model_configurator_configurator->getViews($material_id);


		foreach ($views as $view) {


			$data['series'][$view['series_id']] = array(
				'series_id'   => $view['series_id'],
				'name'      => $view['serie'],
				'element_id'   => $view['element_id'],
			); 

			$products = array();
		
			$items = $this->model_configurator_configurator->getItem($view['view_id'], $this->request->get['house_id']);

			foreach ($items as $item) {

				$products[] = array(
					'item_id'  => $item['item_id'],
					'name'     => $item['name'],
					'prewiev'    => $this->model_tool_image->resize($item['prewiev'], 50 , 50),

				);

			}

			$data['views'][$view['series_id']][]  = array(
				'view_id'   => $view['view_id'],
				'name'      => $view['view'],
				'material_id'  => $view['material_id'],		
				'item'      => $products,		
			);

			$data['element_id']   = $view['element_id'];
	


		}

		$data['text'] = implode($this->request->get);


		$data['html'] = $this->load->view('configurator/configurator_materials', $data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	


	}

	public function saveImage(  ) {

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		$data['success'] = true;

		$layout_image = $this->request->get['layout'];
		$data['html'] = $this->load->view('configurator/configurator_materials', $data);

		$house = $this->model_configurator_configurator->getHouse($this->request->get['house_id']);

		$base_image =  $this->model_tool_image->resize($house['image'], 1500 , 1500);
		$image_path =  $this->model_tool_image->resize('/configurator/none.png', 1500 , 1500);
		$im = imagecreatetruecolor(1500, 1500);
		$x = (int)20;
		$y = (int)20;
		$x_step = (int)710;
		$y_step = (int)70;


		$name = strtotime("now").'_configurator_'.$this->request->get['house_id'];

		$output = imagecreatetruecolor(1500, 1500);
		$src = imagecreatefrompng($base_image);
		imagecopy($output, $src, 0, 0, 0, 0, 1500, 1500); // наложение штампа на основное изображение


		foreach ($layout_image as $image ) {


			if (!empty($image)) {
				$name .= '_'.$image;
				$items = $this->model_configurator_configurator->getImage($image);

				// i.item_id, i.image, i.name, v.name AS view, m.name AS material, s.name AS series, e.name AS elements

				$temp_name = wordwrap( $items['elements'].':'. $items['view'] .' '. $items['series'] .' '. $items['name'], 56, "\n", 1);
				$temp_image =  $this->model_tool_image->resize($items['image'], 1500 , 1500);
				//---
				// шаблонное изображение

				$dest = imagecreatefrompng($base_image);

				// обложка
				$src = imagecreatefrompng($temp_image);
				
				
				// наложение текста

				// Создание цветов

				$black = imagecolorallocate($im, 0, 0, 0);
				$white = imagecolorallocate($im, 255, 255, 255);

				$y = $y + $y_step;

	//			imagefilledrectangle($im, 0, 0, 399, 29, $white);

				// Замена пути к шрифту на пользовательский
				$font = "/home/c/ch14928/site_ckf/public_html/HelveticaRegular.ttf";


				// Текст

				imagettftext($output, 20, 0, $x, (int)$y, $white, $font, $temp_name);
		//		imagettftext($output, 20, 0, 10, 20, $white, $font, $temp_name);
				// наложение текста
				imagecopy($output, $src, 0, 0, 0, 0, imagesx($src), imagesy($src)); // наложение штампа на основное изображение
		//		imagecopy($dest, $im, 0, 0, 0, 0, imagesx($src), imagesy($src)); // наложение штампа на основное изображение




			}	

		}
		imagepng($output, 'image/cache/configurator/'.$name.'.png');


		// Сохраняем изображение в 'simpletext.jpg'


		// Освобождаем память
	//	imagedestroy($dest);

		$data['text'] ='image/cache/configurator/'.$name.'.png';

		$this->response->setOutput($this->load->view('configurator/configurator_materials', $data));	

		$this->response->addHeader('Content-Type: application/json');
	
		$this->response->setOutput(json_encode($data));

    }

	public function itemInfo() {

		$this->load->language('configurator/configurator');

		$this->load->model('configurator/configurator');

		$this->load->model('tool/image');

		$item = $this->model_configurator_configurator->getItemInfo($this->request->get['item_id']);

		if ( $item[0]['related'] ) {

			$data['cokol'] =  $item[0]['related'];

		}



		

    	$material_info  = array(

			'prewiev'   => $this->model_tool_image->resize($item[0]['prewiev'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
			'name'   => $item[0]['name'],
			'seria'   => $item[0]['seria'],
			'title'   => $item[0]['title'],
			'material'   => $item[0]['material'],
			'collection'   => $item[0]['collection'],
			'element_id'   => $item[0]['element_id'],
			

			
			
	
		);
		$data['success'] = true;
		


		$data['html'] = $this->load->view('configurator/configurator_material_info', $material_info);	

		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	

	}





}
