<?php

	class RouteSef extends Base{
		
		private 		$_input			= null,
						$_output		= null,
						$_replace 		= array(),
						$_is_sef 		= false,
						$_route_rules 	= null,
						$_route_urls 	= null;
		
		function __construct( $link = false ){		
		
			if( ! $link ){
				return;
			}
			
			$this->_input = $link;
			
			$this->_replace = array(
				':num' 		=> '\d+',
				':any' 		=> '.+',
				':str' 		=> '\w+',
				'|'			=> parent::conf( 'var_separator' )
			);
			
			$this->_route_rules 	= $this->_load_route_rules();
			$this->_route_urls 		= $this->_load_route_urls();
			
		}
		
		public function is_sef( $is_sef = 0 ){
			$this->_is_sef = (boolean) $is_sef;
		}
		
		public function get_link(){
			
			if( $this->_is_sef !== true ){
				return false;
			}
			
			if( $this->_has_link() ){
				$this->_output = $this->_route_urls[$this->_input];
			}else{
				$this->_output = $this->_input;
			}
			
			return $this->_output;
		}
		
		public function get_sef( $parse = false ){
			
			if( $this->_is_sef !== false ){
				return false;
			}
			
			if( $this->_has_sef() ){			
				$this->_output = array_search( $this->_input, $this->_route_urls );				
			}else if( $parse === true ){
			
				if( ! $this->_create() ){
					$this->_output = $this->_input;
				}else{
					$this->_to_cache( $this->_input, $this->_output );
				}
				
			}else{
				$this->_output = $this->_input;
			}
			
			return $this->_output;
			
		}
		
		private function _has_link(){
			return isset( $this->_route_urls[$this->_input] );
		}
		
		private function _has_sef(){			
			return ( false !== (boolean) array_search( $this->_input, $this->_route_urls ) );
		}
		
		private function _create(){
			
			if( false === $this->_route_rules ){
				$this->_output = $this->_input;
				return false;
			}
			
			foreach( $this->_route_rules as $rule => $replace ){
				
				$regexp = str_replace( array_keys( $this->_replace ), array_values( $this->_replace ), $rule );
				$regexp = '%^'. $regexp .'$%';
				
				if( preg_match( $regexp, $this->_input ) ){					
					$this->_output = preg_replace( $regexp, $replace, $this->_input );
					return true;
				}
				
			}
			
			return false;
		}
		
		private function _load_route_rules(){
			
			static $rules;
			
			if( empty( $rules ) && ! is_array( $rules ) ){
				
				$rules 	= array();
				
				$table 	= Table::getInstance( 'route_rules' );
				$rows 	= $table->load( null, array( 'enable' => 1 ) );
				
				if( 0 >= count( $rows ) ){
					return false;
				}
				
				foreach( $rows as $row ){
					$rules[$row->url] = $row->prefix . '/' . $row->mask;
				}
				
			}
			
			return $rules;
			
		}
		
		private function _load_route_urls(){
			
			static $urls;
			
			if( empty( $urls ) && ! is_array( $urls ) ){
				
				$urls = array();
				
				$table = Table::getInstance( 'route_urls' );
				$rows = $table->load( null, array( 'enable' => 1 ) );
				
				if( 0 >= count( $rows ) ){
					return false;
				}
				
				foreach( $rows as $row ){
					$urls[$row->sef] = $row->url;
				}
			
			}
			
			return $urls;
			
		}
		
		private function _to_cache( $url, $sef ){
		
			$data = array(
				'url'	=> $url,
				'sef'	=> $sef
			);
			
			$table = Table::getInstance( 'route_urls' );
			$table->bind( $data );
			$table->insert( true );
		}
		
		static public function getInstance( $route ){
		
			static $instances;
			
			if( is_array( $instances ) ){
				$instances = array();
			}
			
			if( empty( $instances[$route] ) ){
				$instances[$route] = new self( $route );
			}
			
			return $instances[$route];
			
		}
		
	}