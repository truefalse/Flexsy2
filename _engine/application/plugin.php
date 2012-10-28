<?php
	
	class Plugin extends Base{
		
		protected 	$_params = null,
					$_config = null;
		
		public function __construct( $conf ){
			parent::__construct();
			
			$this->_config = $conf;
			$this->_params = self::getParams( $this->_config->name );
		}
		
		static public function getParams( $plugin = null ){
		
			if($plugin == null){
				return false;
			}
			
			$plugin = strtolower($plugin);
			$include_dir = PLUGINS_PATH . DS . $plugin;
			
			Params::set_include_path( $include_dir );			
			$params = Params::load( $plugin );			
			$key = 'plugin.'. $plugin;
			
			return $params->get( $key, new BlankObject );
			
		}
				
	}
