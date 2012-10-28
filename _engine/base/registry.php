<?php

	class Registry extends Singleton{
		
		private $_data = array();
		
		public function __set( $name, $value ){
			$this->set( $name, $value );
		}
		
		public function __get( $name ){
			return $this->get( $name );
		}
		
		public function __isset( $name ){
			return isset( $this->_data[$name] );
		}
		
		public function __unset($name){
			unset( $this->_data[$name] );
		}
		
		public function set( $name, $value ){
			if( empty( $value ) ){
				$value = null;
			}
			// Bind data
			$this->_data[$name] = $value;			
		}
		
		public function get( $name ){
			if( ! isset( $this->_data[$name] ) ){
				$this->_data[$name] = null;
			}
			// Return from data
			return $this->_data[$name];
		}
		
	}
