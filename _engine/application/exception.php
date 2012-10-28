<?php

	class FleException extends Exception{
		
		function __construct($msg, $code){
			parent::__construct($msg, $code);
		}
		
	}
