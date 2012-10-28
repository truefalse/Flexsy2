<?php

	class Router extends Base{
		
		private		$_route 		= null,
					$_module 		= null,
					$_controller 	= null,
					$_action		= null,
					$_parts			= array(),
					$_format		= null;
		
		
		public function __construct( $route = null ){
			
			// Base construct
			parent::__construct();
			
			// Set default route sting if $route is empty
			if( ! (boolean) $route ){
				$route = parent::conf( 'home_url', 'index/index/index' );
			}
			
			// Handle RouteSef
			if( (boolean) parent::conf( 'handle_route', false ) ){
				
				// Get RouteSef instance
				$sef = RouteSef::getInstance( $route );
				
				// Mode sef-link. 
				// Because maybe we get sef-link and we must get non-sef link for correctly request
				$sef->is_sef( true );
				
				// Get non-sef link
				$route = $sef->get_link();
			}
			
			// Processing the query string
			$this->_route( $route );
		}
		
		public function getModule(){
			return $this->_module;
		}
		
		public function getController(){
			return $this->_controller;
		}
		
		public function getAction(){
			return $this->_action;
		}
		
		public function getFormat(){
			return $this->_format;
		}
		
		public function getVars(){
			return array(
				'module' 		=> $this->_module,
				'format' 		=> $this->_format,
				'controller'	=> $this->_controller,
				'action'		=> $this->_action,
				'params'		=> $this->_params
			);
		}
		
		public function getPart( $index = 1 ){
			$index = $index;
			
			if( 0 >= $index ){
				$index = 1;
			}
			
			return $this->_parts[$index-1];
		}
		
		private function _route( $route = null ){
			
			// Set request string to internal variable
			$this->_route = $route;
			
			// Clear request string
			$this->_cleanRoute();	
			
			// Create all variables from request string
			call_user_method_array( '_createVars', $this, explode( '/', $this->_route ) );
			
		}
		
		private function _cleanRoute(){
			
			// Clear request string
			$this->_route = preg_replace( '/[\\/]+/ui', '/', $this->_route );
			$this->_route = preg_replace( '/\.+/', '.', $this->_route );
			$this->_route = trim( $this->_route, '/' );
		}
		
		private function _createVars(){
			
			// Get variables from cutting the query string
			$this->_parts = func_get_args();
			$size = count( $this->_parts );
			
			if( 3 < $size ){		
				
				if( true === (boolean) parent::conf( 'addLangCodeToURL' ) ){
				
					$langCode = array_shift( $this->_parts );
					Language::getInstance()->setLanguage( $langCode );
					
				}else if( $this->_parts[0] == Language::getInstance()->getLanguage() ){
				
					array_shift( $this->_parts );
					
				}
				
			}
			
			// Create module name and format
			list( $this->_module, $this->_format ) = explode( '.', $this->_parts[0] );	
			
			// Set default format
			if( empty( $this->_format ) ){
				$this->_format = 'html';
			}
			
			// Definition controller name
			$this->_controller 	= ! empty( $this->_parts[1] ) ? $this->_parts[1] : 'index';
			
			// Definition action name
			$this->_action 		= ! empty( $this->_parts[2] ) ? $this->_parts[2] : 'index';
			
			// If there are more variables
			if( $size > 3 ){
				
				// Separate the main variables
				$this->_params = array_slice( $this->_parts, 3, $size-1 );
				
				foreach ( $this->_params as $key => $value ) {
				
					// Divide the remaining variables on key => value
					if( strpos( $value, parent::conf( 'var_separator' ) ) !== false ){
						unset( $this->_params[$key] );
						list( $key, $value ) = explode( parent::conf( 'var_separator' ), $value );
					}
					
					$this->_params[$key] = $value;
					
				}
				
			}
			
		}
				
		static public function link( array $mainVariables = array(), array $additionalVariables = array(), $mod_rewrite = true, $get_var = 'route' ){	
		
			$link 		= null;;
			$separator 	= parent::conf( 'var_separator' );
			
			// Fill in missing values
			if( ( $size = sizeof( $mainVariables ) ) < 3 ){
				$mainVariables = array_merge( $mainVariables, array_fill( $size, 3 - $size, 'index' ) );
			}
			
			if( count( $additionalVariables ) > 0 ){
			
				// Adds a separator
				array_walk( $additionalVariables, function( & $value, $key ) use ( $separator ){
				
					// Adds a separator for each pair
					if( is_string( $key ) ){
						$value = $key . $separator . $value; 
					}
				});			
			}
			
			$vars = array_merge( $mainVariables, $additionalVariables );
			
			if( true === (boolean) parent::conf( 'addLangCodeToURL' ) ){
				$langCode = Language::getInstance()->getLanguage();
				array_unshift( $vars, $langCode );
			}
			
			$link = join( '/', $vars );
			
			// Additional processing links by 'RouteSef'
			if( (boolean) parent::conf( 'handle_route', false ) ){
				
				// Get 'RouteSef' instance
				$sef = RouteSef::getInstance( $link );
				
				// Mode non-sef. Because we should get a sef link from non-sef link
				$sef->is_sef( false );
				
				// Find and get sef-link
				$link = $sef->get_sef( true );
				
			}
			
			// We must add variable in start string
			if( ! $mod_rewrite ){
				$link = '?'. $get_var .'=' . $link;
			}
			
			// Return link with the base path
			return URI::base() . $link;
			
		}
		
		static public function getInstance( $route = null ){
			
			// Instances of Roter
			static $instances;
			
			// Set array
			if( empty( $instances ) ){
				$instances = array();
			}
			
			// Get self route from $_GET
			if( empty( $route ) ){				
				$route = Request::getInstance()->getVar( 'route' );
			}
			
			// Make hash
			$hash = md5($route);
			
			// New instance
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new Router( $route );
			}
			
			return $instances[$hash];
			
		}
		
	}
