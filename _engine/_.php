<?php
	
	class _{
		
		static private $cfg;
		
		static public function t($key=null){
			return t($key);
		}
		
		
		static public function cfg($key = null){
			static $cfg = null;
			if(empty(self::$cfg)){
				self::$cfg = Registry::getInstance()->config;
			}
			return self::$cfg->get($key);
		}
		
	}
	
	class F extends _{}
