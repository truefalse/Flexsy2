<?php

	function t( $key = null ){
		
		static $lang;
		
		if( empty( $lang ) ){
			$lang = Language::getInstance();
		}
		
		return $lang->translate( $key );
		
	}
	
	function u(){
		
		$args 	= func_get_args();
		
		$systemVars = array_slice( $args, 0, 3 );
		$otherVars 	= array_slice( $args, 3 );
		
		return Router::link( $systemVars, $otherVars, true );
		
	}
	
	function m( $route ){
		return Module::render( $route );
	}