<?php

	class ShutdownApp extends Singleton{
		
		private $_callbacks = array();
		
		function __construct(){
			register_shutdown_function( array( $this, 'callShutdown' ) );
		}
		
		public function registerShutdown(){			
			$callback = func_get_args();
			
			if( is_callable( $callback[0] ) ){
				$this->_callbacks[] = $callback;
			}			
		}
		
		public function callShutdown(){		
			foreach($this->_callbacks as $args){			
				$callback = array_shift( $args );
				call_user_func_array( $callback, $args );				
			}			
		}
		
	}
