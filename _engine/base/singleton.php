<?php

/**
 * Singleton class for all objects
 * @author Ivan Gontarenko
 * @version 1.0
 */

	class Singleton{
		
		/**
		* Array with all instances
		* @var array
		*/
		static private $_instances = array();
		
		/**
		* Static public method for return instance of any engine object
		* @param Variables for __construct $arguments
		* @return object
		*/
		static public function getInstance(){
			// Class name of called
			$className = get_called_class();
			
			// Get arguments
			$arguments = func_get_args();
			
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
							die( '!FIX!' . $exp->getMessage() );
						}
					}catch( Exception $exp ){
						die( '!FIX!' . $exp->getMessage() );
					}					
				}
			}
			
			// Return instance
			return self::$_instances[$hash];			
		}
		
	}