<?php

	class ControllerHelper extends BaseStatic{
		
		static private $_include_path 	= null;
		static private $_params			= array();
		static private $_objects		= array();
		
		static public function set_include_path($include_path){
			self::$_include_path = $include_path;
		}
		
		static public function getHelper($module, $name){			
			$class_name = ucfirst($name) . 'Helper';
			
			if(self::$_objects[$class_name]){
				return self::$_objects[$class_name];
			}
			
			self::loadHelper($module, $name);
			
			if(!class_exists($class_name)){
				parent::$_error->raise('Sorry, but class name dont found:'.$class_name, 'fatal' );
			}
			
			self::$_objects[$class_name] = new $class_name;
			return self::$_objects[$class_name];
		}
		
		static public function loadHelper( $module, $name ){	
			$class_file = MOLULES_PATH . DS . $module . DS . 'helpers' . DS . $name . '.php';
			if(!file_exists($class_file)){
				parent::$_error->raise('Sorry, but file dont exists:'.$class_file, 'fatal' );
			}
			
			require_once $class_file;
		}
		
	}
