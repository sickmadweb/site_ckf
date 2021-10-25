<?php
class ControllerExtensionFormCallback extends Controller {
	public function index() {
		$this->load->language('extension/form/form');

		$data['title'] = $this->language->get('title_callback');

		$data['key'] = "u7XkxgqYJ";
		$data['email'] = $this->language->get('text_email');
		$data['phone'] = $this->language->get('text_phone');
		$data['comment'] = $this->language->get('text_comment');
		$data['password'] = $this->language->get('text_password');

		$data['director_mail'] = $this->config->get('director_mail');

		$data['title_page'] = $this->document->getTitle();
		
		$data['action'] = $this->url->link('mail/form');
		
		
		return $this->load->view('extension/form/callback', $data);
	}
}