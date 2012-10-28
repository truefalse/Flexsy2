<?php

	class FormatData extends BaseStatic{
		
		static private $_objects = array();
		
		static public function to_string($format = 'json', array $data){
		
			if(empty(self::$_objects[$format])){	
			
				$class_name = 'Format' . ucfirst(strtolower($format));
				
				if(!class_exists($class_name)){
					parent::$_error->raise( 'Format \''. $format .'\' dont support', 'fatal' );
				}
				
				self::$_objects[$format] = new $class_name;
				
			}
			
			return self::$_objects[$format]->to_string($data);
		}
		
	}