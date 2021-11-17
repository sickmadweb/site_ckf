<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Log class
*/
class Log {
	private $handle;
	
	/**
	 * Constructor
	 *
	 * @param	string	$filename
 	*/
	public function __construct($filename) {
		$this->handle = fopen(DIR_LOGS . $filename, 'a');
	}
	
	/**
     * 
     *
     * @param	string	$message
     */
	public function write($message) {
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
		/*
		$config = $this->registry->get('config');
		// отправка ошибок на почту
		$to      = $this->config->get('config_email');
		$subject = 'PHP ERROR';
		$message = $message.'<br>'.$this->request->get['url'].'<br>'. date('Y-m-d G:i:s').'<br>';
		$headers = array(
			'From' => 'webmaster@example.com',
			'Reply-To' => 'webmaster@example.com',
			'X-Mailer' => 'PHP/' . phpversion()
		);
		mail($to, $subject, $message, $headers);
		*/
	}
	
	/**
     * 
     *
     */
	public function __destruct() {
		fclose($this->handle);
	}
}