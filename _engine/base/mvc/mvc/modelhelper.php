<?php

	class ModelHelper extends BaseStatic{

		static private 	$_includePaths 		= array(),
						$_models			= array();

		static public function addIncludePath( $_path = null ){
			self::$_includePaths[md5($_path)] = $_path;
		}

		static public function setIncludePath( $_path = null ){
			self::$_includePaths = array( md5( $_path ) => $_path );
		}

		static public function add( $name ){

			$file 		= self::_modelFilePath( $name );
			$class 		= self::_modelClassName( $name );

			if( self::_exists( $class ) ){
				parent::$_error->raise( 'Model already exists', 'notify' );
				return;
			}

			if( ! $file ){
				parent::$_error->raise( 'Model file not found:'. $file, 'fatal' );
			}else{
				require_once $file;
			}

			if( ! class_exists( $class ) ){
				parent::$_error->raise( 'Model class not found:'. $class, 'fatal' );
			}else{
				self::$_models[$class] = new $class;
			}

		}

		static public function get( $name ){

			$class = self::_modelClassName( $name );
			
			if( self::_exists( $class ) ){
				return self::$_models[$class];
			}else{
				throw new Exception( 'Model dont exists:'. $class );
			}
		}

		static private function _exists( $classname ){
			return ( is_object( self::$_models[$classname] ) && self::$_models[$classname] instanceOf Model );
		}

		static private function _modelClassName( $name ){
			return ucfirst( strtolower( $name ) ) .'Model';
		}

		static private function _modelFilePath( $name ){
		
			if( ! empty( self::$_includePaths ) ){

				foreach( self::$_includePaths as $path ){
					$file = $path . DS . 'models' . DS . $name . '.php';

					if( file_exists( $file ) ){
						return $file;
					}
				}

			}

			return false;

		}

	}
