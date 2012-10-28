<?php

	class Params extends BaseStatic{
		
		static private $_include_path = null;
		
		static public function set_include_path($include_path){
			self::$_include_path = $include_path;
		}
		
		static public function load( $name ){			
			$file = self::_ini_file( $name );
			// Return object of ini file
			return INI::toObject( FSO::File()->read( $file ) );
		}
		
		static private function _ini_file($name){
			return self::$_include_path . DS . $name . '.ini';
		}
		
	}
