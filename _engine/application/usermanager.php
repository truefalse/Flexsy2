<?php

	class UserManager extends Base{
		
		private $_userData 		= null,
				
				$_loader		= null,
				$_request		= null,
				$_db 			= null,
				$_session		= null,
				$_access		= null,
				$_config		= null,
				
				$_userTables 	= array();
				
		
		public function __construct( $userId = 0 ){
			
			// Base construct
			parent::__construct();
			
			// Bind main objects
			$this->_loader 		= Registry::getInstance()->get( 'loader' );
			$this->_request 	= Request::getInstance();
			$this->_db 			= Registry::getInstance()->db;
			$this->_session 	= Session::getInstance();
			$this->_access 		= Access::getInstance();
			$this->_config 		= Registry::getInstance()->get( 'config' );
			
			// Get user tables
			$this->_userTables = array(
				'users'					=> Table::getInstance( 'users' ),
				'session'				=> Table::getInstance( 'users' ),
				'actions'				=> Table::getInstance( 'access_actions' ),
				'groups'				=> Table::getInstance( 'access_groups' ),
				'accessXref'			=> Table::getInstance( 'access_xref' )
			);
			
		}
		
		public function getGroup(){
			
		}
		
		public function chageGroup(){
			
		}
		
		public function chagePermissions(){
			
		}
		
		public function getPermissions(){
			
		}
		
		public function save(){
			
		}
		
		public function delete(){
			
		}
		
		public function bind(){
			
		}
		
		public function ban(){
			
		}
		
		public function disable(){
			
		}
		
		private function _loadUserData(){
			
		}
		
		private function _getTable( $tableName = '' ){
			
		}
		
		static public function getInstance( $userId = 0 ){
		
			if( 0 >= (int) $userId ){
				throw new Exception( 'Enter the user ID' );
				return false;
			}
			
			static $instances;
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			if( empty( $instances[$id] ) ){
				$instances[$id] = new UserManager( $id );
			}
			
			return $instances[$id];
			
		}
		
	}