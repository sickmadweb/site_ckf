<?php
class ControllerMailForm extends Controller {
			
	public function index() {

		$this->load->model('mail/form');	

		if (isset($this->request->post['key'])) {
			
		}

		if (isset($this->request->post['keypres']) > strlen($this->request->post['comment'])) {
			
		}	

		if (isset($this->request->post['name'])) {

			$data['name'] = $this->request->post['name'];

		}

		if (isset($this->request->post['comment'])) {

			$data['comment'] = $this->request->post['comment'];

		}
		
		if (isset($this->request->post['email'])) {

			$data['contact'] = $this->request->post['email'];


		} 
		
		if (isset($this->request->post['contact'])) {

			$data['contact'] = $this->request->post['contact'];

		} 

		if (isset($this->request->post['forms'])) {
			
			$data['forms'] = $this->request->post['forms'];

		} 
		$data['email'] = $this->config->get('config_email');

		$data['page'] = $this->request->post['title'];
	
		// покупатель
		$data['buyer'] = $data['contact'].', Pro_kolR@mail.ru';
		
		// менеджер (сотрудник)
	
		$data['employee'] = 'Pro_kolR@mail.ru';
		

		if (isset($this->session->data['location'])) {

//			$data['employee'] = $this->model_mail_form->getSalesEmails($this->session->data['location']);	

		} else  {
			
			$data['employee'] = $this->config->get('config_email');	

		}
	
		// директор
		$data['head_email'] = $this->model_mail_form->getHeadEmail($this->session->data['location_id']);
		
		// администратор магазина
		$data['suport_mail'] = $this->config->get('config_email');		
	
if ($data['forms'] == 'callback_form') {

	$data['employee'] =  $data['head_email'];
	
}		
		$sendlog = $this->model_mail_form->getInsertLogMassage($data);


		$data['employee'] = 'Pro_kolR@mail.ru';

		$data['buyer'] = 'Pro_kolR@mail.ru';
		
		
//  форма запросы цены		
if ($data['forms'] == 'query_price_form') {

        if (isset($this->request->post['keypres']) >= strlen($this->request->post['comment']) and  $this->request->post['key'] == 'u7XkxgqYJ' ) {

			$to      = $data['employee'].',Pro_kolR@mail.ru';
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено!</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		} else {
					
			// возможный спам		
			
			$to      = $data['suport_mail'];
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено.</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		}
	
}

// форма тех поддержки
if ($data['forms'] == 'support_form') {

			$to      = $data['suport_mail'];
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено!</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}
			
	
}

// форма обратной связи (письмо директору)
if ($data['forms'] == 'callback_form') {



        if (isset($this->request->post['keypres']) >= strlen($this->request->post['comment']) and  $this->request->post['key'] == 'u7XkxgqYJ' ) {

			$to      = 'Pro_kolR@mail.ru';
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено!</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		} else {
					
			// возможный спам		
			
			$to      = $data['suport_mail'];
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено.</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		}
	
}





/*
        if (isset($this->request->post['keypres']) >= strlen($this->request->post['comment']) and  $this->request->post['key'] == 'u7XkxgqYJ' ) {

			$to      = $data['employee'].',Pro_kolR@mail.ru';
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено!</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		} else {
					
			// возможный спам		
			
			$to      = 'Pro_kolR@mail.ru';
			$subject = html_entity_decode(sprintf('Сообщение с сайта', $this->request->post['name']), ENT_QUOTES, 'UTF-8');
			$message = $this->load->view('mail/form', $data);
			$headers = 
			    	   'Reply-To: Pro_kolR@mail.ru' . "\r\n" .
			    	   'X-Mailer: PHP/' . phpversion();

			if (mail($to, $subject, $message, $headers)) {
				$json['return'] = '<p class="bg-success text-color-black">Письмо отправлено.</p>';	
			} else {
				$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';	
			}

		}
*/

//		$json['return'] = '<div class="alert alert-danger alert-dismissible">Произошла ошибка повторите позже!<button type="button" class="close" data-dismiss="alert">×</button></div>';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	
	}
		
}
