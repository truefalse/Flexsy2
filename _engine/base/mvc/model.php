<?php
	
	class Model extends Base{
		
		protected 	$_dbo 		= null,
					$_request 	= null;
		
		public function __construct(){
		
			parent::__construct();		
			
			$this->_dbo 		= Registry::getInstance()->db;
			$this->_request 	= Registry::getInstance()->get('request');
			
		}
		
		protected function getTable( $table = '', $key = 'id' ){
			return Table::getInstance( $table, $key );
		}
		
	}
