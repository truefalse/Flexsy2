<?php

	class Date extends Base{
		
		private $_time = null;
		
		public function __construct( $date = null ){			
			if( empty( $date ) ){
				$this->_time = mktime();
			}else{
				$this->_time = strtotime( $date );
			}			
		}
		
		public function toMySQL(){
			$date = date('Y-m-d H:i:s', $this->_time);
			return $date;
		}
		
		public function toUnix(){
			$date = $this->_time;
			return $date;
		}
		
		public function toFormat( $format = 'd/m/Y H:i:s' ){
			$date = date( $format, $this->_time );
			return $date;
		}
		
		static public function getInstance( $date = 'now' ){
		
			static $instances;
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			$hash = md5( $date );
			
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new Date( $date );
			}
			
			return $instances[$hash];
			
		}
		
	}