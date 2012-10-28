<?php


	class Session{
		
		public $data 			= array();
		
		private $_config 	= null,
				$_namespace	= null;
				
		
		public function __construct( $config = null ){
			
			// Delete alien session
			$this->_destroyAlreadyExistsSession();
			
			// Set site config object to property
			if( is_object( $config ) ){
				$this->_config = $config;
			}
			
			// Set default setting
			$this->_setDefaultSettings();
			
			session_start();
			
			if( ! isset( $_SESSION[$this->_namespace] ) ){
				$_SESSION[$this->_namespace] = array();
			}
			
			$this->data = & $_SESSION[$this->_namespace];
			
			$this->_updateDefaultInfo();
			
			if( false === $this->_checkSession() ){
				$this->destroy();
			}
			
		}
		
		public function & get( $key, $default = null ){
			$key = preg_replace( '/[^\w\d\.]+/', '', (string) $key );
			
			if( isset( $this->data[$key] ) ){
				return $this->data[$key];
			}else{
				return $default;
			}			
		}
		
		public function set( $key, $value = null ){
			$key = preg_replace( '/[^\w\d\.]+/', '', (string) $key );			
			$this->data[$key] = $value;		
		}
		
		public function getId(){
			return session_id();
		}
		
		public function setId( $id ){
			$id = preg_replace( '/[^\w\d]/i', '', (string) $id );
			session_id( $id );
		}
		
		public function destroy(){
			
			if( $_COOKIE[ session_name() ] ){
				$cookie_path 	= $this->_config->cookie_path;
				$cookie_domain 	= $this->_config->cookie_domain;
				// Remove session ID from cookie
				setcookie( session_name(), '', time() - 3600, $cookie_path, $cookie_domain );				
			}
			
			session_unset();
			session_destroy();
			
			return true;			
		}
		
		static public function getInstance(){
			static $instance;
			
			if( empty( $instance ) ){
				$instance = new Session( $GLOBALS['config'] );
			}
			
			return $instance;
		}
		
		public function expires(){
			
			$lifetime 			= ini_get( 'session.gc_maxlifetime' );			
			$expires_in			= $this->data['session_timer_last'] + $lifetime;
			$now				= $this->data['session_timer_now'];
			
			return ( $expires_in - $now );	
			
		}
		
		// Private methods
		
		private function _destroyAlreadyExistsSession(){
		
			$auto_start = ini_get( 'session.auto_start' );
			
			if ( session_id() || $auto_start == '1' || strtolower( $auto_start ) == 'on' ){
				session_unset();
				session_destroy();
			}
			
			return true;
			
		}
		
		private function _setDefaultSettings(){
			
			ini_set( 'session.use_trans_sid', '0' );
			
			// Set cookie lifetime
			if( isset( $this->_config->cookie_lifetime ) && (int) $this->_config->cookie_lifetime >= 0 ){
				ini_set( 'session.cookie_lifetime', (int) $this->_config->cookie_lifetime );
			}
			
			// Set session file lifetime
			if( isset( $this->_config->session_lifetime ) && (int) $this->_config->session_lifetime >= 0 ){
				ini_set( 'session.gc_maxlifetime', (int) $this->_config->session_lifetime );
			}
			
			// Set session name
			if( ! empty( $this->_config->session_name ) ){
				session_name( $this->_config->session_name );
			}
			
			// Set cookie setting
			$cookie = session_get_cookie_params();			
			// Set Cookie path
			if( ! empty( $this->_config->cookie_path ) ){
				$cookie['path'] = $this->_config->cookie_path;
			}			
			// Set Cookie domain
			if( ! empty( $this->_config->cookie_domain ) ){
				$cookie['domain'] = $this->_config->cookie_domain;
			}			
			session_set_cookie_params( $cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'] );
			
			// Set Session namespace
			if( ! empty( $this->_config->session_namespace ) ){
				$this->_namespace = $this->_config->session_namespace;
			}
			
			session_cache_limiter( 'none' );
			
		}
		
		private function _updateDefaultInfo(){
			//  Timestamp start
			$start = time();
			
			// Create if first load
			if( ! $this->get( 'session_timer_start', false ) ){
				$this->set( 'session_timer_start', 	$start );		
				$this->set( 'session_timer_last', 	$start );	
				$this->set( 'session_timer_now', 	$start );
			}
			
			// Update timers
			$this->set( 'session_timer_last', $this->get( 'session_timer_now', -1 ) );
			$this->set( 'session_timer_now', $start );
			
			$this->data['session_counter']++;
						
		}
		
		private function _checkSession(){
			// Validation of session data
			if( ! isset( $this->data['session_client_browser'] ) && ! isset( $this->data['session_client_ip'] ) ){
				$this->data = array(
					'session_client_browser' 	=> $_SERVER['HTTP_USER_AGENT'],
					'session_client_ip'			=> $_SERVER['REMOTE_ADDR']
				);
			}else{				
				if( $this->data['session_client_browser'] !== $_SERVER['HTTP_USER_AGENT'] ){
					return false;
				}				
				if( $this->data['session_client_ip'] !== $_SERVER['REMOTE_ADDR'] ){
					return false;
				}
			}
			
			if( 0 >= $this->expires() ){
				return false;
			}
			
			return true;
			
		}
		
		private function _uniqueId( $length = 32 ){
			
			// Control limit of string
			if( 0 >= (int) $length || 64 < (int) $length ){
				$length = 32;
			}
			
			// Array of chars
			$chars = array_merge( range( 0, 9 ), range( 'a', 'z' ) );
			
			// Shuffle array
			shuffle( $chars );
			
			$uniqueId = null;
			
			// Build string
			for( $i = 0; $i < $length; $i++ ){
				$uniqueId .= $chars[$i];
			}
			
			return $uniqueId;
			
		}
		
	}
