<?php

/**
 * Kernel of Flexsy engine
 * @author Ivan Gontarenko
 * @version 1.0
 */

	class Kernel extends Singleton{
		
		static private $_instances = array();
		
		public function __get( $name ){			
			$value = $this->registry()->get( $name );
			
			if( empty( $value ) ){
				 $value = $this->factory( $name );
				 $this->registry()->set( $name, $value );
			}
			
			return $value;
			
		}
		
		public function factory( $className, array $arguments = array() ){
			
			// Hash for check already existing instance
			$hash = md5( $className . sizeOf( $arguments ) );
			
			if( empty( self::$_instances[$hash] ) ){
				if( empty( $arguments ) ){
					self::$_instances[$hash] = new $className;
				}else{
					try{
						$rfl = FleReflection::getInstance( $className );
						// Try to create instance
						try{
							self::$_instances[$hash] = $rfl->newInstanceArgs( $arguments );
						}catch( Exception $exp ){
							$this->registry()->error->raise( $exp->getMessage(), 'fatal' );
						}
					}catch( Exception $exp ){
						$this->registry()->error->raise( $exp->getMessage(), 'fatal' );
					}					
				}
			}
			
			// Return instance
			return self::$_instances[$hash];	
		}
		
		public function registry(){
			return Registry::getInstance();
		}
		
		public function getAPI(){
			return API::getInstance();
		}
		
		public function getConfig(){
			return $this->registry()->config;
		}
		
		public function getDBO(){
			return $this->registry()->db;
		}
		
		public function getTable( $tableName, $tableKey = 'id' ){
			$table = $this->factory( 'table', array( $tableName, $tableKey ) );
			return $table;
		}
		
		
	}
