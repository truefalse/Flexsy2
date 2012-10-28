<?php
	
	class Request extends Base{
		
		private $_variables = array();
		
		public function __construct(){
			
			$variables 	= array(
				'get'		=> & $_GET,
				'post'		=> & $_POST,
				'cookie'	=> & $_COOKIE,
				'server'	=> & $_SERVER,
				'env'		=> & $_ENV,
				'file'		=> & $_FILES,
			);
			
			if( get_magic_quotes_gpc() ){
				$this->_stripSlashes( $variables );
			}
			
			$this->_variables = & $variables;
			
		}
		
		public function & getVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['get'];
			}else{
				return isset( $this->_variables['get'][$variableKey] ) 
					? $this->_variables['get'][$variableKey] 
					: null;
			}
			
		}
		
		public function & postVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['post'];
			}else{
				return isset( $this->_variables['post'][$variableKey] ) 
					? $this->_variables['post'][$variableKey] 
					: null;
			}
			
		}
		
		public function & cookieVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['cookie'];
			}else{
				return isset( $this->_variables['cookie'][$variableKey] ) 
					? $this->_variables['cookie'][$variableKey] 
					: null;
			}
			
		}
		
		public function & serverVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['server'];
			}else{
				return isset( $this->_variables['server'][$variableKey] ) 
					? $this->_variables['server'][$variableKey] 
					: null;
			}
			
		}
		
		public function & envVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['env'];
			}else{
				return isset( $this->_variables['env'][$variableKey] ) 
					? $this->_variables['env'][$variableKey] 
					: null;
			}
			
		}
		
		public function & fileVar( $variableKey = null ){
			
			if( ! (boolean) $variableKey ){
				return $this->_variables['file'];
			}else{
				return isset( $this->_variables['file'][$variableKey] ) 
					? $this->_variables['file'][$variableKey] 
					: null;
			}
			
		}
		
		private function _stripSlashes( & $datas ){
			
			if( is_array( $datas ) ){
				$this->_stripSlashes( $datas );
			}else{
				return stripcslashes( $datas );
			}
			
		}
		
	}
