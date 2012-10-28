<?php
	
	/**
	*
	* @package FleReflection
	* @author Ivan Gonatrenko
	* @email vania.gontarenko@gmail.com
	* @website http://vk.com/flexsy
	*
	*/
	
	class FleReflection{
		
		static public function getInstance( $name, $reflectionClassName = 'Class' ){
			
			// Variable where will be stored all objects
			static $instances;
			
			// Set array to variable where store all object if the first call
			if( empty( $instances ) and is_array( $instances ) ){
				$instances = array();
			}
			
			// Make class names for reflection and our class
			$reflectionClassName 	= 'Reflection'. ucfirst( strtolower( (string) $reflectionClassName ) );
			$name 				= strtolower( (string) $name );
			
			// Check if not already exists object
			if( 
				empty( $instances[$reflectionClassName][$name] ) 
				or ! ( $instances[$reflectionClassName][$name] instanceOf $reflectionClassName ) 
			){
				
				// If not exists Relfaction class
				if( ! class_exists( $reflectionClassName ) ){
					throw new Exception( $reflectionClassName .' class not exists, maybe \'Reflection\' extention not support' );
				}
				
				$instances[$reflectionClassName][$name] = new $reflectionClassName( $name );
			}
			
			// return instance of reflection
			return $instances[$reflectionClassName][$name];
			
		}
		
	}