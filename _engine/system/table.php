<?php

	class Table extends Base{
		
		protected 	$_db 			= null,
					$_table_name	= null,
					$_table_key		= null,
					$_fields		= array();
		
		public function __construct( $table = '', $key = '' ){
		
			parent::__construct();
			
			$this->_db = Registry::getInstance()->db;
			
			$this->_table_name 	= '#P_' . strtolower($table);	
			$this->_table_key 	= $key;
			
			try{
				$fields = $this->_db->objectList( 'SHOW COLUMNS FROM `'. $this->_table_name .'`' );	
				
				foreach($fields as $field){
					$this->_fields[$field->Field] = null;
				}
			}catch( Exception $e ){
				$this->raiseFatal( $e->getMessage() );
			}
			
		}
		
		static public function getInstance( $table = '', $key = 'id' ){
			
			static $instances;
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			$hash = md5( $table . $key );
			
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new Table( $table, $key );
			}
			
			return $instances[$hash];
			
		}
		
		public function setTableKey( $key = null ){
			// Check key
			if( (boolean) $key && array_key_exists( $key, $this->_fields ) ){
				$this->_table_key = $key;
			}else{
				return false;
			}
		}
		
		public function bind( array $data = array() ){			
			// Detect bad data
			if( ! empty( $data ) ){
				// Bind data to table
				foreach( $data as $name => $value ){				
					// Checking correctly assigning
					if( 
						$name !== $this->_table_key 
						and array_key_exists( $name, $this->_fields ) 
					){
						$this->_fields[$name] = $value;
					}					
				}				
			}			
		}
		
		public function load( $tableKey = null, array $condition = array() ){
			
			$query = 'SELECT * FROM `'. $this->_table_name  .'`';
			
			if( (boolean) $tableKey ){
				$condition[$this->_table_key] = $tableKey;
			}
			
			$where = & $this->_buildWhereCondition( $condition );
			
			if( false !== $where ){
				$query .= ' WHERE '. implode( ' AND ', $where );
			}
			
			return (boolean) $tableKey ? $this->_db->objectItem( $query ) : $this->_db->objectList( $query );
		}
		
		public function delete( $tableKey = null, array $condition = array()  ){
		
			if( (boolean) $tableKey || ( is_array( $tableKey ) && 0 < count( $tableKey ) ) ){
				$condition[$this->_table_key] = $tableKey;
			}else{
				$this->errorMessage( 'Метод "Table::update" нуждается в обязательном аргументе $tableKey' );
				return false;
			}
			
			$query = 'DELETE FROM `'. $this->_table_name  .'`';
			
			$where = & $this->_buildWhereCondition( $condition );
			
			$query .= "\n" . ' WHERE '. implode( ' AND ', $where );
			//die($query);
			return $this->_db->execute( $query );
			
		}
		
		public function update( $tableKey = null, array $condition = array() ){
			
			if( (boolean) $tableKey || ( is_array( $tableKey ) && 0 < count( $tableKey ) ) ){
				$condition[$this->_table_key] = $tableKey;
			}else{
				$this->errorMessage( 'Метод \'update\' нуждается в обязательном аргументе $tableKey' );
				return false;
			}
				
			$set = array();
			
			foreach( $this->_fields as $key => $value ){
				
				if( is_null( $value ) ){
					continue;
				}
				if( ! is_scalar( $value ) ){
					$value = serialize( $value );
				}				
				$set[] = "\n\t" . '`'. $this->_table_name  .'`.`'. $key .'` = ' . $this->_db->quote( $value );
			}
			
			$query = 'UPDATE `'. $this->_table_name  .'` SET '. implode( ', ', $set );
			
			$where = & $this->_buildWhereCondition( $condition );
			
			if( false !== $where ){
				$query .= "\n" . ' WHERE '. implode( ' AND ', $where );
			}
			
			return $this->_db->execute( $query );
		}
		
		public function insert( $ignore = false ){
			
			$columns 	= array();
			$values 	= array();

			foreach( $this->_fields as $key => $value ){
			
				if( empty( $value ) ){
					continue;
				}
				
				if( ! is_scalar( $value ) ){
					$value = serialize( $value );
				}		
				
				$columns[] 	= $key;
				$values[] 	= $this->_db->quote( $value );
			}
			
			$query = 'INSERT'. ( $ignore === true ? ' IGNORE ' : ' ' ) .'INTO `'. $this->_table_name  .'`';
			$query .= "\n\t" . '('. implode( ', ', $columns ) .')';
			$query .= "\n\t" . 'VALUES('. implode( ', ', $values ) .')';
			
			return $this->_db->execute( $query );
		}
		
		public function replace(){
			
			$columns 	= array();
			$values 	= array();

			foreach( $this->_fields as $key => $value ){
			
				if( is_null( $value ) ){
					continue;
				}
				
				if( ! is_scalar( $value ) ){
					$value = serialize( $value );
				}		
				
				$columns[] 	= $key;
				$values[] 	= $this->_db->quote( $value );
			}
			
			$query = 'REPLACE INTO `'. $this->_table_name  .'`';
			$query .= "\n\t" . '('. implode( ', ', $columns ) .')';
			$query .= "\n\t" . 'VALUES('. implode( ', ', $values ) .')';
			
			return $this->_db->execute( $query );
			
		}
		
		private function & _buildWhereCondition( array $condition = array() ){
			$where = array();
			
			if( 0 < count( $condition ) ){
				foreach( $condition as $key => & $value ){
				
					if( is_scalar( $value ) ){
						$where[] = "\n\t" . '`'. $this->_table_name  .'`.`'. $key .'` = '. $this->_db->quote( $value );
					}else if( is_array( $value ) ){
						if( 0 < count( $value ) ){
						
							foreach( $value as & $item ){
								$item = $this->_db->quote( $item );
							}
							
							$where[] = "\n\t" . '`'. $this->_table_name  .'`.`'. $key .'` IN('. implode( ', ', $value ) .')';
						}
					}
					
				}
			}
			
			return 0 >= count( $where ) ? false : $where;
			
		}
		
	}