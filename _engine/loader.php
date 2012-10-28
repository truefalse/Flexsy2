<?php

	class Loader extends Singleton{
		
		private $_libDirectories = array();
		
		public function __construct(){
			// Default library directories
			$this->_libDirectories = array(				
				'base',
				'system',
				'application',
				'default',
				'backendApplication'				
			);
			
			spl_autoload_register( array( $this, 'loadClass' ) );			
		}
		
		public function addDirectory( $dirPath = null ){
			
			if( ! (boolean) $dirPath || ! file_exists( ENGINE_PATH . DS . $dirPath ) ){
				return false;
			}
			
			$this->_libDirectories[] = $dirPath;
			
			return true;
		}
		
		public function loadClass( $className = null ){
			
			if( ! (boolean) $className ){
				return false;
			}
			
			$className = ucfirst( strtolower( $className ) );
			
			if( class_exists( $className ) ){
				return true;
			}
			
			$classFile = strtolower( $className ) .'.php';
			
			for( $i = 0, $c = sizeOf( $this->_libDirectories ); $i < $c; $i++ ){
				$directory = ENGINE_PATH . DS . $this->_libDirectories[$i];
				
				if( file_exists( $directory . DS . $classFile ) ){
					require_once $directory . DS . $classFile;
					
					if( class_exists( $className ) ){
						break;
					}
					
				}
				
			}
			
			return true;			
		}
		
	}