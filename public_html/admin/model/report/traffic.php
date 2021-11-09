<?php
class ModelReportTraffic extends Model {
	public function getReports( $store_id ) {

		$query = $this->db->query("


		SELECT COUNT(DISTINCT(session_user_id)) AS users, 
		
		(
			SELECT COUNT(DISTINCT(`repeated`))
			FROM " . DB_PREFIX . "logs_user_statistics 
			WHERE  repeated =1
			AND store_id = s.store_id
		) AS repeated,

		s.store_id, 
		s.name AS site ,  
        
		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time' 
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) <'00:01:00.000000'
		) AS time_less_1 ,

		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time'  
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) >='00:01:00.000000'
		AND TIMEDIFF(last_time.date , start_time.date) <='00:05:00.000000'
		) AS time_1m_5m ,
		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time' 
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) >'00:05:00.000000'
		) AS time_more_5m,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='telephone' 
		AND store_id = s.store_id
		) AS telephone,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='viber' 
		AND store_id = s.store_id
		) AS viber,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='whatsapp' 
		AND store_id = s.store_id
		) AS whatsapp,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='cart' 
		AND store_id = s.store_id
		) AS checkout,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='order' 
		AND store_id = s.store_id
		) AS orders,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='mailto' 
		AND store_id = s.store_id
		) AS email,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%configurator%')
		AND store_id = s.store_id
		) AS configurator,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%contact%')
		AND store_id = s.store_id
		) AS contacts
		
        FROM " . DB_PREFIX . "logs_user_statistics lus
		
		LEFT JOIN " . DB_PREFIX . "store  s ON (lus.store_id = s.store_id)

		WHERE s.store_id =" .$store_id. "

		GROUP BY s.store_id

		
		
		");


		print_r("

<br><br>
		SELECT COUNT(DISTINCT(session_user_id)) AS users, 
		
		(
			SELECT COUNT(DISTINCT(`repeated`))
			FROM " . DB_PREFIX . "logs_user_statistics 
			WHERE  repeated =1
			AND store_id = s.store_id
		) AS repeated,

		s.store_id, 
		s.name AS site ,  
        
		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time' 
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) <'00:01:00.000000'
		) AS time_less_1 ,

		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time'  
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) >='00:01:00.000000'
		AND TIMEDIFF(last_time.date , start_time.date) <='00:05:00.000000'
		) AS time_1m_5m ,
		(
		SELECT COUNT(DISTINCT(last_time.session_user_id))
		FROM " . DB_PREFIX . "logs_user_action lua
		LEFT JOIN " . DB_PREFIX . "logs_user_action last_time  ON (lua.session_user_id = last_time.session_user_id)
		LEFT JOIN " . DB_PREFIX . "logs_user_action start_time ON (lua.session_user_id = start_time.session_user_id)
		WHERE  last_time.action ='last_time'  AND start_time.action ='start_time' 
		AND lua.store_id = s.store_id
		AND TIMEDIFF(last_time.date , start_time.date) >'00:05:00.000000'
		) AS time_more_5m,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='telephone' 
		AND store_id = s.store_id
		) AS telephone,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='viber' 
		AND store_id = s.store_id
		) AS viber,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='whatsapp' 
		AND store_id = s.store_id
		) AS whatsapp,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='cart' 
		AND store_id = s.store_id
		) AS checkout,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='order' 
		AND store_id = s.store_id
		) AS orders,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='mailto' 
		AND store_id = s.store_id
		) AS email,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%configurator%')
		AND store_id = s.store_id
		) AS configurator,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%contact%')
		AND store_id = s.store_id
		) AS contacts
		
        FROM " . DB_PREFIX . "logs_user_statistics lus
		
		LEFT JOIN " . DB_PREFIX . "store  s ON (lus.store_id = s.store_id)

		WHERE s.store_id =" .$store_id. "

		GROUP BY s.store_id

		
		
		");



		return $query->row;
	}


	
		
}
