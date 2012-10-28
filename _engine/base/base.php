<?php

	class Base extends Singleton{
		
		private $_error 			= null;		
		private $_message 			= null;
		
		public function __construct(){
			$this->_error 	= Registry::getInstance()->get('error');
			$this->_message = Registry::getInstance()->get('message');
		}
		
		public function __call($name, $args){
			$error_handler = Registry::getInstance()->get('error');
			$error_handler->raise( 'Method \''. $name .'\' not exists in \''. get_class($this) .'\'', 'fatal', false );
		}
		
		public function __toString(){
			return get_class( $this ) . ': was called as string type';
		}
		//Error
		public function raiseFatal( $msg = null ){
			
			if( ! (boolean) $msg ){
				return false;
			}
			
			$this->_error->raise( $msg, 'fatal', false );
		}
		
		public function errorMessage( $msg = null ){
			$this->_error->raise( $msg, 'error' );
		}
		
		public function warningMessage( $msg = null ){
			$this->_error->raise( $msg, 'warning' );
		}
		
		public function page404(){
			$this->_error->raise( 'Page not found', 404, false );
		}
		
		public function phpInfo(){
			die( phpinfo() );
		}
		
		public function infoMessage($msg){
			$this->_message->set_message( null, $msg, 'info');
		}
		
		public function successMessage($msg){
			$this->_message->set_message( null, $msg, 'success');
		}
		
		protected function redirect( $url, $msg = null ){
		
			if( ! empty( $msg ) ){
				$this->infoMessage( $msg );
			}
			
			header( 'Location: ' . str_replace( '&amp;', '&', $url ) );
			die;
		}
		
		public function conf( $key, $default = null ){
			
			static $instance;
			
			if( empty( $instance ) ){
				$instance = Registry::getInstance()->get('config');
			}
			
			return $instance->get($key, $default);
		}
		
	}
