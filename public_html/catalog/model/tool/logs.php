<?php
class ModelToolLogs extends Model {

	public function getLogs( ) {
		// Если не бот
		if (stripos(( isset($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : ''), "bot") > 0) {

				// есть session_user_id
				if (isset($this->session->data['session_user_id'])) {


				} else {
					// иначе  нету session_user_id
					
					$this->session->data['session_user_id'] = date("U");
					$this->getAction( 'start_time', $this->document->getTitle());

					// установили session_user_id
					$query = $this->db->query("
						SELECT * FROM `" . DB_PREFIX . "user_session` WHERE `user_id` = '". ( isset($this->session->data['user_id']) ? $this->session->data['user_id'] : $this->session->data['session_user_id']) ."'
					");
					// 1  если зашел сотрудник 
					if ($query->rows > 0) {
						$this->session->data['employee'] = 1 ;
					} else {
						$this->session->data['employee'] = 0 ;
					}

					if (isset($this->request->cookie['user_id'])) { 
						if(isset($this->request->cookie['repeated'])) {
							setcookie("repeated", $this->request->cookie['repeated']+1, time()+(10*365*24*60*60));
						} else { 
							setcookie("repeated", 1, time()+(10*365*24*60*60));
						}

					} else {
						if(isset($this->request->cookie['repeated'])) {
							setcookie("repeated", $this->request->cookie['repeated']+1, time()+(10*365*24*60*60));
						} else { 
							setcookie("repeated", 1, time()+(10*365*24*60*60));
						}

					}


				}
				
				//  если есть куки user_id
				if (isset($this->request->cookie['user_id'])) {
					$this->getAction( 'last_time', $this->document->getTitle());
					// если  session_user_id  == user_id
					if ($this->request->cookie['user_id'] == $this->session->data['session_user_id']) {					
						// устанавливаем куки user_id 
					//	setcookie("repeated", $this->request->cookie['repeated']+1, time()+(10*365*24*60*60));
					} else {
						$this->session->data['user_id'] = $this->request->cookie['user_id']; 
						setcookie("user_id", $this->session->data['user_id'], time()+(10*365*24*60*60));
						// устанавливаем следующее посещение 

					}		


				} else {
					// иначе если нету user_id
					$this->session->data['user_id'] =$this->session->data['session_user_id'] ;
					setcookie("user_id", $this->session->data['user_id'], time()+(10*365*24*60*60));
					// устанавливаем куки user_id 
					if(isset($this->request->cookie['repeated'])) {
						setcookie("repeated", $this->request->cookie['repeated']+1, time()+(10*365*24*60*60));
					} else { 
						setcookie("repeated", 1, time()+(10*365*24*60*60));
					}
					//setcookie("repeated", $this->request->cookie['repeated']+1, time()+(10*365*24*60*60));
					// устанавливаем куки repeated  1  как первый заход  
				}

		} 

		if (isset($this->session->data['employee'])) {
			$employee = $this->session->data['employee'] ;
		} else {
			$employee = 0;
		}

		$route = isset($this->request->get['route'])  ? $this->request->get['route'] : 'title';  

				if (isset($this->session->data['session_user_id'])) {
				} else {
					$this->session->data['session_user_id'] = date("U");
				}


		$this->db->query("

			INSERT INTO " . DB_PREFIX . "logs_user_statistics (date_first, date_last, host,session_user_id, user_agent, ip, user_id, store_id, route, repeated, employee, OCSESSID, title ) VALUES 
			( NOW(), NOW(), '".$this->request->server['HTTP_HOST']."', '".$this->session->data['session_user_id']."', '".( isset($this->request->server['HTTP_USER_AGENT']) ? $this->request->server['HTTP_USER_AGENT'] : '')."', '".$this->request->server['SERVER_ADDR']."', '".  ( isset($this->session->data['user_id']) ? $this->session->data['user_id'] : $this->session->data['session_user_id']) ."', '".$this->config->get('config_store_id')."', '".$route."', '". ( isset($this->request->cookie['repeated']) ?  $this->request->cookie['repeated'] :  '0' )  ."', " .$employee.  ", '". ( isset(  $this->request->cookie['OCSESSID'] ) ?  $this->request->cookie['OCSESSID'] :  ' ' ) ."', '".$this->document->getTitle() ."'  )			

		");

	}

	public function getAction( $name, $title ) {
	
		if ($name == "last_time") {

			$this->db->query("

			UPDATE `" . DB_PREFIX . "logs_user_action` SET date=NOW() , `title` = '".$title."' WHERE `action`='last_time' AND `session_user_id` ='".$this->session->data['session_user_id']."' AND `user_id` ='". ( isset($this->session->data['user_id']) ? $this->session->data['user_id'] : $this->session->data['session_user_id']) ."' AND  `store_id` = '".$this->config->get('config_store_id')."'

			");


		} else {


		}

		
		if ($name == "start_time") {
            if (empty($this->session->data['start_time'])) {
                $this->db->query("

				INSERT IGNORE INTO `" . DB_PREFIX . "logs_user_action`
				
				(`user_id`, `session_user_id`, `action`  , `date`, `store_id`, `title`) VALUES 
				
				('". (isset($this->session->data['user_id']) ? $this->session->data['user_id'] : $this->session->data['session_user_id']) ."','".$this->session->data['session_user_id']."','last_time',NOW()  ,'".$this->config->get('config_store_id')."','".$title."')

				");

				$this->db->query("

				INSERT IGNORE INTO `" . DB_PREFIX . "logs_user_action`
				
				(`user_id`, `session_user_id`, `action`  , `date`, `store_id`, `title`) VALUES 
				
				('". (isset($this->session->data['user_id']) ? $this->session->data['user_id'] : $this->session->data['session_user_id']) ."','".$this->session->data['session_user_id']."','".$name."',NOW()  ,'".$this->config->get('config_store_id')."','".$title."')

				");

				$this->session->data['start_time'] = 'start_time';

    	    }

		}

    }

}
