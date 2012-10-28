<?php
	
	/**
		*
		* @package 		User
		* @name 		User
		* @description 	Class for User
		* @author 		Ivan Gonatrenko
		* @email 		vania.gontarenko@gmail.com
		* @website 		http://vk.com/flexsy
		*
	*/
	
	class User extends Base{
		
		private $_userId	= 0,
				$_groupId	= 0,
				$_isUser 	= false,
				$_isGuest	= true,
				$_userData 	= null,
				
				$_loader	= null,
				$_request	= null,
				$_db 		= null,
				$_session	= null,
				$_access	= null,
				$_config	= null;
				
		
		public function __construct(){
			
			// Run base construct
			parent::__construct();
			
			// Bind main objects
			$this->_loader 		= Registry::getInstance()->get( 'loader' );
			$this->_request 	= Request::getInstance();
			$this->_db 			= Registry::getInstance()->db;
			$this->_session 	= Session::getInstance();
			$this->_access 		= Access::getInstance();
			$this->_config 		= Registry::getInstance()->get( 'config' );
			
			// Identify the user
			$this->_identifyUser();
			
			// Update session table
			$this->_updateSessionData();
			
		}
		
		public function getData( $variable = null ){
			// Get user field end return
			if( ! (boolean) $variable or ! isset( $this->_userData[$variable] ) ){
				return $this->_userData;
			}else{
				return $this->_userData[$variable];
			}
		}
		
		public function isGuest(){
			return $this->_isGuest;
		}
		
		public function isUser(){
			return $this->_isUser;
		}
		
		public function isAdmin(){
			if( $this->_isUser and $this->_userId > 0 and $this->_groupId > 0 ){
				// Access AdminPanel
				return $this->_access->canDoIt( $this->_groupId, 1 );
			}else{
				return false;
			}
		}
		
		public function login( array $userData = array() ){
			
			// If user redirect to homepage
			if( $this->isUser() ){
				$this->infoMessage( 'You already logged on.' );
				$this->redirect( Router::link( array( 'index', 'index', 'index' ), array(), false ) );
			}
			
			// Check data
			if( empty( $userData['password'] ) || empty( $userData['login'] ) ){
				$this->warningMessage( 'Request is empty' );
			}
			
			// Prepare user data
			$password 	= User::getPasswordHash( $userData['password'] );
			$login 		= trim( $userData['login'] );
			
			// Get user table
			$userTable 	= Table::getInstance( 'users', 'login' );
			
			// Fetch result
			$userResult = $userTable->load( $login, array( 'passwd' => $password ) );
			
			// If user exists
			if( ! empty( $userResult ) ){
				
				// Authorize user
				if( $this->_authorizeUser( $userResult ) ){
					$this->successMessage( 'Authentication is successful' );
				}else{
					$this->warningMessage( 'Authentication is fail' );
					return false;
				}
				
				// Add to cookie
				if( (boolean) $this->_config->get( 'remember_user', false ) === true ){
					
					// Prepare data to cookie
					$cookieData = array(
						'id'		=> $userResult->id,
						'login' 	=> $login,
						'passwd'	=> $password,
						'group_id'	=> $userResult->group_id,
						'enable'	=> 1,
						'banned'	=> 0
					);
					
					// Serialize data
					$cookieData = serialize( $cookieData );
					
					// Encrypt data
					fCrypt::setMask( '@$#!1&%0XO' );
					$cookieData = fCrypt::encrypt( $cookieData, $this->_config->get( 'salt' ) );
					
					// Cookie setting
					$cookieDomain 	= $this->_config->get( 'cookie_domain' );
					$cookieLifetime = $this->_config->get( 'cookie_lifetime', 3600 * 24 * 14 );
					
					// Set cookie
					setcookie( 'user', $cookieData, time() + $cookieLifetime, '', $cookieDomain );
				}
				
				return true;
				
			}else{
				// Register message
				$this->infoMessage( 'The user with the requested data is not found' );
				return false;
			}
			
		}
		
		public function logout(){
		
			// Check user state
			if( $this->isGuest() && ! $this->isUser() ){			
				// Register message
				$this->infoMessage( 'You already guest' );
			}else{
				// Delete all user data
				$this->_deleteSessionData();	
				
				// Clean session
				$this->_session->set( 'user', null );	
				
				// Delete cookies
				$cookieData = & $this->_request->cookieVar( 'user' );
				
				if( ! empty( $cookieData ) ){
					
					// Cookie params
					$cookieDomain 	= $this->_config->get( 'cookie_domain' );
					
					// Delete cookies
					setcookie( 'user', null, time() - 3600, '', $cookieDomain );
				}				
				// Register message
				$this->successMessage( 'You have successfully logout' );
			}
		}
		
		private function _identifyUser(){
			
			// Checking if session not have user data
			if( ! isset( $this->_session->data['user'] ) || empty( $this->_session->data['user'] ) ){
				// Set default params
				$this->_session->data['user'] = array(
					'userId' 	=> 0,
					'guest'		=> true,
					'groupId'	=> 0
				);
			}
			
			// Check cookies
			if( 0 >= $this->_session->data['user']['userId'] ){
				if( $this->_checkCookies() ){
					$this->infoMessage( 'You are log in via cookies' );
					return;
				}
			}
			
			// User session parameters
			$sessionGuest 		= (boolean) $this->_session->data['user']['guest'];
			$sessionGroupId 	= (integer) $this->_session->data['user']['groupId'];
			$sessionUserId 		= (integer) $this->_session->data['user']['userId'];
			
			// If user
			if( $sessionUserId > 0 && $sessionGuest === false ){
			
				// User table instance
				$userTable 	= Table::getInstance( 'users' );
				
				// Set default table key
				$userTable->setTableKey( 'id' );
				
				$whereCond = array(
					'group_id' 			=> $sessionGroupId,
					'enable'			=> 1,
					'banned'			=> 0
				);
				
				// Fetch user from DB
				$userResult = $userTable->load( $sessionUserId, $whereCond );
				
				// If user aviable
				if( ! empty( $userResult ) ){					
					// Authorize user
					$this->_authorizeUser( $userResult );
				}else{
					// If user was banned or disabled
					$this->logout();
				}
				
			}
			
		}
		
		private function _checkCookies(){
			// Get cookie data
			$cookieData = $this->_request->cookieVar( 'user' );
			
			if( ! empty( $cookieData ) ){
				
				// Decode data
				fCrypt::setMask( '@$#!1&%0XO' );
				$cookieData = fCrypt::decrypt( $cookieData, $this->_config->get( 'salt' ) );
				$cookieData = @ unserialize( $cookieData );
				
				// If data is Ok
				if( false !== (boolean) $cookieData && is_array( $cookieData ) && 0 < count( $cookieData ) ){
					
					// User parameters
					$cookieUserId 		= (integer) $cookieData['id'];
					unset( $cookieData['id'] );
					
					// User table instance
					$userTable 	= Table::getInstance( 'users' );
					
					// Set default table key
					$userTable->setTableKey( 'id' );
					
					// Array for 'WHERE' condition
					$whereCondition = $cookieData;
					
					// Fetch user from DB
					$userResult = $userTable->load( $cookieUserId, $whereCondition );
					
					// If user aviable
					if( ! empty( $userResult ) ){					
						// Authorize user
						return $this->_authorizeUser( $userResult );
					}else{
						// If user was banned or disabled
						$this->logout();
					}
					
				}else{
					// Register warning
					$this->warningMessage( 'The data from the cookie could not be decrypted' );
					return false;
				}
				
			}
		}
		
		private function _authorizeUser( $userData = null ){
			
			// If data is empty, stop
			if( ! (boolean) $userData ){
				return false;
			}
			
			// If is object when convert to array
			if( is_object( $userData ) ){
				$userData = (array) $userData;
			}
			
			// Check array
			if( 0 >= count( $userData ) ){
				return false;
			}
			
			// Set session data
			$this->_session->data['user'] = array(
				'guest' 	=> false,
				'groupId' 	=> $userData['group_id'],
				'userId' 	=> $userData['id']
			);
			
			// Identify user status
			$this->_userId		= $userData['id'];
			$this->_groupId		= $userData['group_id'];
			$this->_isUser 		= true;
			$this->_isGuest		= false;
			
			// decode field `params` from JSON
			$userData['params'] = json_decode( $userData['params'] );
			
			// Add user data to user object
			$this->_userData 	= $userData;
			
			return true;
		}
		
		private function _updateSessionData(){
			
			// Data prepare
			$sessionId 		= $this->_session->getId();
			$userId 		= (int) $this->_session->data['user']['userId'];
			$guest 			= (int) $this->_session->data['user']['guest'];
			$last_update	= time();
			
			// Session table instance
			$sessionTable 	= Table::getInstance( 'session', 'sid' );
			
			// Result			
			$sessionRow 	= $sessionTable->load( $sessionId );
			
			// To array
			$data = array(
				'sid'			=> $sessionId,
				'user_id'		=> $userId,
				'guest'			=> $guest,
				'last_update'	=> $last_update
			);
			
			// Set default table key
			$sessionTable->setTableKey( 'id' );
			
			// Bind data
			$sessionTable->bind( $data );
			
			// Update data or insert new record
			if( empty( $sessionRow ) ){
				$sessionTable->insert( true );
			}else{
				$sessionTable->update( $sessionRow->id );
			}
			
		}
		
		private function _deleteSessionData(){
			// Session table instance
			$sessionTable 	= Table::getInstance( 'session', 'sid' );
			$sessionId		= $this->_session->getId();
			
			// Delete from session table
			$rowAffected 	= $sessionTable->delete( $sessionId );
			
			return $rowAffected;
		}
		
		static public function getPasswordHash( $password = null ){
			
			// If not empty password
			if( ! (boolean) $password ){
				return false;
			}
			
			// Create hash
			$salt 	= Registry::getInstance()->get( 'config' )->get( 'salt' );
			$salt 	= md5( $salt );
			$hash 	= md5( $password );
			$hash 	= md5( $salt . $hash );
			
			return $hash;
		}
		
		static public function getInstance(){
			
			static $instance;
			
			if( empty( $instance ) ){
				$instance = new User;
			}
			
			return $instance;
			
		}
		
	}