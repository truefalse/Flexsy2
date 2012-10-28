<?php
	
	/*
	*	Flexsy2
	*	Ivan Gontarenko
	*	vania.gontarenko@gmail.com
	*/
	
	class Error extends Singleton{
		
		private $_buffer 			= array();
		private $_errorPages		= array();
		private $_errorTypes 		= array();
		
		function __construct(){
					
			$this->_buffer = & Registry::getInstance()->getInstance()->session->data['__m'];
			
			$this->_errorPages = dirname(__FILE__) . DS . 'pages';
			
			$this->_errorTypes = array(
				404,
				'fatal_php',				
				'fatal',
				
				'error',
				'warning'
			);
		}
		
		public function getErrors( $type = null ){
			$msg = $this->_buffer[$type];
			if( ! empty( $msg ) ){
				$this->_buffer[$type] = array();
				return $msg;
			}
		}
		
		public function raise( $msg = null, $type = 'error' ){			
			switch($type){
				case 404:
				case 'fatal':
					$back_trace = ! DEBUG ?: Debug::highlight_array( debug_backtrace() );
					$this->_register( $type, $msg, $back_trace );
					die( $this->_evalPHP($type) );
				break;
				case 'fatal_php':
					$back_trace = ! DEBUG ?: Debug::highlight( error_get_last() );
					$this->_register( $type, $msg, $back_trace );
					die( $this->_evalPHP() );
				break;
				case 'error':
				case 'warning':
					$back_trace = ! DEBUG ?: Debug::highlight_array( debug_backtrace() );
					$this->_register( $type, $msg, $back_trace );
				break;
			}
		}
		
		public function detectPHPError(){
			$error = error_get_last();
			if(empty($error)) return;
			if( $error['type'] === E_ERROR || $error['type'] === E_WARNING ){
				$this->raise( $error['message'], 'fatal_php' );
			}
		}
		
		public function getErrorTypes(){
			return $this->_errorTypes;
		}
		
		private function _register( $error_type, $msg, $back_trace = null ){
			$this->_buffer[$error_type][] = array(
				'__msg' 	=> (string) $msg,
				'__debug' 	=> (string) $back_trace
			);
		}
		
		private function _evalPHP($error_type = 'fatal'){
			ob_clean();
			ob_start();
				include $this->_errorPages.DS.'error_'.$error_type.'.php';
			return ob_get_clean();
		}
		
	}
