<?php
class ControllerToolLogs extends Controller {
	public function index() {


	}

	public function getAction() {

		$this->load->model('tool/logs');
		
		$name = $this->request->get['name'];
		
		$title = $this->request->get['title'];

		$result = $this->model_tool_logs->getAction($name, $title);
		$json =$result ;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
	
	public function AllerJSError() {
				
		$to      = $this->config->get('config_email');
		$subject = 'JS ERROR';
		$message = $this->request->get['message'].'<br>'.$this->request->get['url'].'<br>'.$this->request->get['line'].'<br>';
		$headers = array(
			'From' => 'webmaster@example.com',
			'Reply-To' => 'webmaster@example.com',
			'X-Mailer' => 'PHP/' . phpversion()
		);
		
		mail($to, $subject, $message, $headers);
		
		$json = true ;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
		
			}
	
			

}