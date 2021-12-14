<?php
class ModelReportTraffic extends Model {
	public function getReports( $store_id ) {


		if (isset($this->request->get['day'])) {
			$day =  $this->request->get['day'] ;
		} else {
			$day =  3 ;
		}




		$sql = "


		SELECT COUNT(DISTINCT(lus.session_user_id)) AS users, 
		
		(
			SELECT COUNT(DISTINCT(`repeated`))
			FROM " . DB_PREFIX . "logs_user_statistics 
			WHERE  repeated =1
			AND store_id = " .$store_id. "
			AND date_last > NOW() - INTERVAL " .$day. " DAY
		) AS repeated,
        
		(


		SELECT COUNT(DISTINCT(q.session_user_id))

		FROM " . DB_PREFIX . "logs_user_action AS w , " . DB_PREFIX . "logs_user_action AS q
		
		WHERE w.date > NOW() - INTERVAL " .$day. " DAY
		AND  q.date > NOW() - INTERVAL " .$day. " DAY
		AND w.session_user_id = q.session_user_id
		AND w.action IN ('last_time' )
		AND q.action IN ('start_time' )
		AND q.store_id = " .$store_id. "
		AND w.store_id = " .$store_id. "
		AND TIMEDIFF(w.date , q.date) <'00:05:00.000000'



		) AS time_less_1 ,

		(

			SELECT COUNT(DISTINCT(q.session_user_id))

			FROM " . DB_PREFIX . "logs_user_action AS w , " . DB_PREFIX . "logs_user_action AS q
			
			WHERE w.date > NOW() - INTERVAL " .$day. " DAY
			AND  q.date > NOW() - INTERVAL " .$day. " DAY
			AND w.session_user_id = q.session_user_id
			AND w.action IN ('last_time' )
			AND q.action IN ('start_time' )
			AND q.store_id = " .$store_id. "
			AND w.store_id = " .$store_id. "
			AND TIMEDIFF(w.date , q.date) >='00:01:00.000000'
			AND TIMEDIFF(w.date , q.date) <='00:05:00.000000'
			
		) AS time_1m_5m ,
		(
			SELECT COUNT(DISTINCT(q.session_user_id))

			FROM " . DB_PREFIX . "logs_user_action AS w , " . DB_PREFIX . "logs_user_action AS q
			
			WHERE w.date > NOW() - INTERVAL " .$day. " DAY
			AND  q.date > NOW() - INTERVAL " .$day. " DAY
			AND w.session_user_id = q.session_user_id
			AND w.action IN ('last_time' )
			AND q.action IN ('start_time' )
			AND q.store_id = " .$store_id. "
			AND w.store_id = " .$store_id. "
			AND TIMEDIFF(w.date , q.date) >'00:05:00.000000'
		) AS time_more_5m,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='telephone' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS telephone,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='viber' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS viber,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='whatsapp' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS whatsapp,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='cart' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS checkout,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='order' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS orders,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_action 
		WHERE  action ='mailto' 
		AND store_id = " .$store_id. "
		AND date > NOW() - INTERVAL " .$day. " DAY
		) AS email,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%configurator%')
		AND store_id = " .$store_id. "
		AND date_last > NOW() - INTERVAL " .$day. " DAY
		) AS configurator,
		(
		SELECT COUNT(DISTINCT(`session_user_id`))
		FROM " . DB_PREFIX . "logs_user_statistics 
		WHERE  route like ('%contact%')
		AND store_id = " .$store_id. "
		AND date_last > NOW() - INTERVAL " .$day. " DAY
		) AS contacts
		
        FROM " . DB_PREFIX . "logs_user_statistics lus

		WHERE 
		lus.date_last > NOW() - INTERVAL 3 DAY 
		AND lus.store_id = 1

		";

	print_r($sql);	$query = $this->db->query($sql);

		return $query->row;
	}


	
		
}
