<?php

	class SiteApplication extends Application{
		
		function __construct(){
		
			parent::__construct();
			
			self::$_client = 'site';
			
		}
		
	}