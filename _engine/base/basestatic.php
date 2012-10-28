<?php

	class BaseStatic{
		
		static protected 	$_error		= null,
							$_loader 	= null;
							
		static private 		$_conf	 	= null;
		
		static public function set_error_handler(Error $error){
			if(empty(self::$_error)){
				self::$_error = $error;
			}
		}
		
		static public function set_loader(Loader $loader){
			if(empty(self::$_loader)){
				self::$_loader = $loader;
			}
		}
		
		static protected function conf($key){
			if(empty(self::$_conf)){
				self::$_conf = Registry::getInstance()->get('config');
			}
			return self::$_conf->get($key);
		}
		
		static protected function r(){
			if(func_num_args() == 2){
				$args = func_get_args();
				if(is_string($args[0]) && is_object($args[1])){
					Registry::getInstance()->set($args[0], $args[1]);
				}
			}else{
				$args = func_get_args();
				return Registry::getInstance()->get($args[0]);
			}			
		}
		
		static protected function raise_fatal($msg=null){
			self::$_error->raise( $msg, 'fatal', false );
		}

	}
