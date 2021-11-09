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
	
}