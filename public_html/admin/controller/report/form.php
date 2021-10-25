<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt


class ControllerReportForm extends Controller {


    public function index() {
        $this->load->language('report/form');

        $this->document->setTitle($this->language->get('heading_title'));

        
        $this->load->model('report/form');

        $forms_info = $this->model_report_form->getForms();

        $data['forms'][] =   array();
        
		foreach ($forms_info as $form_info) {

			$data['forms'][] =   array(
				'mail_to'       => $form_info['mail_to'],
				'forms'         => $form_info['forms'],
				'date'          => $form_info['date'],
				'comment'       => $form_info['comment'],
				'contact_from'  => $form_info['contact_from'],
				'page'          => $form_info['page'],
                'store_id'      => $form_info['store_id'],
                'location_id'    => $form_info['location_id'],              
            );

        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/form', $data));  

    }
}
