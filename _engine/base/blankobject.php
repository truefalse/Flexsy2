<?php
	
	class BlankObject extends Base{
		
		public function get( $key, $default = null ){
			return isset( $this->$key ) ? $this->$key : $default;
		}
		
	}
