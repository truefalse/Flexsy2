<?php
	
	class Event extends Base{
		
		private $_evt 		= array();
		private $_handlers 	= array();
		
		function __construct(){
			parent::__construct();
			$this->_load_events();
		}
		
		public function register( Plugin $handler ){
			$methods = array_intersect($this->_evt, get_class_methods($handler));	
			foreach($methods as $event){
				$this->_handlers[$event][] = $handler;
			}
		}
		
		public function trigger( $event, array $args = array() ){
			if( ! in_array($event, $this->_evt) ){
				$this->warningMessage( 'Event: \''. $event .'\' is unregistered' );
			}			
			$handlers = & $this->_handlers[$event];
			if(!empty($handlers)){			
				foreach($handlers as $handler){				
					call_user_method_array($event, $handler, $args);
				}
			}
		}
		
		private function _load_events(){
			$dbo = Registry::getInstance()->db;			
			$query = 'SELECT * FROM #P_events AS e WHERE e.enable >= 1';			
			$events = $dbo->objectList($query);
			
			if(!empty($events)){
				foreach($events as $event){
					$this->_evt[] = $event->name;
				}
			}
		}
		
	}
