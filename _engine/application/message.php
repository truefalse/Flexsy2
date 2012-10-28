<?php
	
	/*
	*	Flexsy2
	*	Ivan Gontarenko
	*	vania.gontarenko@gmail.com
	*/
	
	class Message extends Singleton{
		
		private $_namespace 		= '__flexsy';
		private $_buffer 			= array();
		
		function __construct(){
			$this->_buffer = & Registry::getInstance()->getInstance()->session->data['__m'];
		}
		
		private function _register( $msg_type, $title, $msg ){
			$this->_buffer[$msg_type][] = array(
				'__title' 	=> (string) $title,
				'__msg' 	=> (string) $msg
			);
		}
		
		public function set_message($title, $msg, $type){
			$this->_register($type, $title, $msg);
		}
		
		public function fetch_messages(){
			$msg = $this->_buffer;
			$this->_buffer = array();
			return array(
				'info' 		=> $msg['info'],
				'success' 	=> $msg['success']
			);
		}
		
	}