<?php

	class URI extends Base{
		
		static private $_instances 	= array();
		
		private $_uri_vars 			= array();
		
		function __construct( $uri ){
			$this->parse( $uri );
		}
		
		private function parse( $uri ){
			
			$_parts = parse_url( $uri );
			
			if( ! isset( $_parts['scheme'] ) ){			
				$this->_uri_vars['scheme'] = 'http';				
				if( !! $_SERVER['HTTPS'] && strtolower( $_SERVER['HTTPS'] ) == 'on' ){
					$this->_uri_vars['scheme'] .= 's';
				}							
			}else{
				$this->_uri_vars['scheme'] = $_parts['scheme'];
			}
			
			if( ! isset( $_parts['host'] ) ){			
				$this->_uri_vars['host'] = $_SERVER['HTTP_HOST'];					
			}else{
				$this->_uri_vars['host'] = $_parts['host'];
			}
			
			if( ! empty( $_parts['path'] ) ){
				$this->_uri_vars['path'] = array();
				$this->_uri_vars['path']['base'] = str_replace( basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME'] ); 
				$this->_uri_vars['path']['root'] = str_replace( $this->_uri_vars['path']['base'], '', $_parts['path'] );
			}
			
			if( ! empty( $_parts['query'] ) ){
				$this->_uri_vars['query'] = array();
				$this->_uri_vars['query']['string'] = $_parts['query'];
				parse_str( $_parts['query'], $this->_uri_vars['query']['vars'] );
			}else{
				$this->_uri_vars['query'] = array();
			}
			
		}
		
		static public function getInstance( $uri = false ){
			
			$hash = md5( $uri );
			
			if(is_object( self::$_instances[$hash] )){
				return self::$_instances[$hash];
			}
			
			if( $uri === false ){
				$uri = null;				
				$uri = 'http';
				
				if( !! $_SERVER['HTTPS'] && strtolower( $_SERVER['HTTPS'] ) == 'on' ){
					$uri .= 's';
				}
				
				$uri .= '://';
				$uri .= $_SERVER['HTTP_HOST'] . '' . $_SERVER['REQUEST_URI'];
			}
					
			self::$_instances[$hash] = new URI($uri);
			
			return self::$_instances[$hash];
			
		}
		
		public function setVar(){
		
			$args = func_get_args();
			
			if( func_num_args() == 1 && is_array($args[0] ) ){			
				foreach( $args[0] as $key => $value ){
					$this->_uri_vars['query']['vars'][$key] = $value; 
				}				
			}else{			
				$this->_uri_vars['query']['vars'][$args[0]] = $args[1]; 				
			}
			
			$this->_uri_vars['query']['string'] = http_build_query( $this->_uri_vars['query']['vars'] ); 
			
			return $this;
			
		}
		
		public function buildURL(){
			$parts = func_get_args();
			$uri = null;
			
			foreach( $parts as $part ){
				switch(strtolower($part)){
					case 'scheme':
						$uri .= $this->_uri_vars['scheme'] . '://';
					break;
					case 'host':
						$uri .= $this->_uri_vars['host'];
					break;
					case 'path':
						$uri .= implode( $this->_uri_vars['path'] );
					break;
					case 'path_base':
						$uri .= $this->_uri_vars['path']['base'];
					break;
					case 'path_root':
						$uri .= $this->_uri_vars['path']['root'];
					break;
					case 'query':
						$uri .= '?' . $this->_uri_vars['query']['string'];
					break;
				}
			}
			
			return urldecode($uri);
		}
		
		public function link( $host = false ){
		
			if( ! is_object( $this ) ){
				$uri = self::getInstance();
			}else{
				$uri = $this;
			}
			
			if($host){
				return $uri->buildURL('scheme','host','path','query');
			}else{
				return $uri->buildURL('path','query');
			}
			
		}
		
		static public function root( $host = false ){
		
			$uri = self::getInstance( false );
			
			if($host){
				return $uri->buildURL('scheme','host','path_root');
			}else{
				return $uri->buildURL('path_root');
			}
			
		}
		
		static public function base($host = false){
		
			$uri = self::getInstance( false );
			
			if($host){
				return $uri->buildURL('scheme','host','path_base');
			}else{
				return $uri->buildURL('path_base');
			}
			
		}
		
		static public function current( $host = false ){
			
			$uri = self::getInstance( false );
			
			if( $host ){
				return $uri->buildURL( 'scheme', 'host', 'path', 'query' );
			}else{
				return $uri->buildURL( 'path', 'query' );
			}		
			
		}
		
		static public function back( $host = false ){
		
			if( ! $_SERVER['HTTP_REFERER'] ){
				return self::base( $host );
			}
			
			$uri = self::getInstance($_SERVER['HTTP_REFERER']);
			
			if($host){
				return $uri->buildURL( 'scheme', 'host', 'path', 'query' );
			}else{
				return $uri->buildURL( 'path', 'query' );
			}
		}
		
		static public function sef_url(){
			$uri = self::getInstance( false );
			return $uri->buildURL( 'path_root' );
		}
		
	}
