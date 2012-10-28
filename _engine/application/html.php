<?php
	
	include_once 'element.php'; 

	class HTML extends Base{

		private $_elements_path 		= null;
		static private $_elements		= array();

		public function __construct(){
			parent::__construct();
			$this->_elements_path = dirname(__FILE__) . DS . 'elements';
		}
		
		static public function getUrlFromPath( $path, $host = false ){
			
			if( empty( $path ) ){
				return false;
			}
			
			$path = str_replace( ROOT_PATH, '', $path );
			$path = preg_replace( '/[\\\|\/]+/', '/', $path );
			
			if( $host === true ){
				$uri 	= URI::getInstance( $path );
				$path 	= $uri->buildURL( 'scheme', 'host', 'path' ); 
			}
			
			return $path;
			
		}
		
		public function e( $type, $name, $value, $variants, array $attrs = array() ){
			$e = HTML::getInstance();
			return $e->element( $type, $name, $value, $variants, $attrs );
		}

		private function element($type, $name, $value, $variants, array $attrs = array()){
		
			$type 	= strtolower($type);
			$file 	= $type . '.php';
			$class 	= ucfirst($type) . 'Element';
			$out 	= null; 
			
			if( empty( self::$_elements[$class] ) ){
			
				if( ! file_exists( $this->_elements_path . DS . $file ) ){
				
					$this->warningMessage('Element "'. $type .'" dosnt exists');
					$out = 'Element not found';
					
				}else{
				
					include_once $this->_elements_path . DS . $file;
					
				}

				if( ! class_exists( $class ) ){
				
					$this->warningMessage('Element class "'. $class .'" dosnt exists');
					$out = 'Element not found';
					
				}else{
				
					self::$_elements[$class] = new $class;
					
				}
			}
			
			$out = self::$_elements[$class]->fetch_element( $name, $value, $variants, $attrs );
			return $out;
		}
		
		static private function _buildAttrs( array $attrs = array() ){
			
			$out = null;
			
			if( empty( $attrs ) ){
				return null;
			}
			
			foreach ( $attrs as $key => $value ) {
				$out .= $key . '="' . $value .'" ';
			}
			
			return $out;
			
		}
		
		static public function select( $name, $value, array $options = array(), array $attrs = array() ){
			return self::e( 'select', $name, $value, $options, $attrs );
		}
		
		static public function chkBox( $name, $value, array $options = array(), array $attrs = array() ){
			return self::e( 'checkbox', $name, $value, $options, $attrs );
		}
		
		static public function yesNo( $name, $value, array $attrs = array() ){
			$html = HTML::getInstance();
			return $html->e( 'yesno', $name, $value, null, $attrs );
		}
		
		static public function image( $path, $title = '', $attrs = array() ){
			return '<img src="'. $path .'" alt="'. $title .'" '. self::_buildAttrs( $attrs ) .' />';
		}
		
		static public function uploader( $config ){
			
			$uploaderPath 	= URI::root( true ) . '/assets/js/plupload/';
			
			static 	$alreadyAddToHead,
					$counter = 0;
			
			$output = Kernel::getInstance()->factory( 'output' );
			
			if( ! (boolean) $alreadyAddToHead ){
				$alreadyAddToHead = true;				
				$output->jsAddFile( $uploaderPath . 'plupload.js' );
				$output->jsAddFile( $uploaderPath . 'plupload.flash.js' );
				$output->jsAddFile( $uploaderPath . 'plupload.html5.js' );
				$output->cssAddFile( HTML::getUrlFromPath( __DIR__ ) . '/html/uploader.css' );
			}
			
			
			$uploaderConfig = array();
			
			$uploaderConfig['id'] 			= md5( ++$counter );
			$uploaderConfig['swfPath'] 		= $uploaderPath . 'plupload.flash.swf';
			$uploaderConfig['maxFileSize'] 	= strtolower( ini_get( 'upload_max_filesize' ) );
			$uploaderConfig['uploadURL'] 	= $config['url'];
			$uploaderConfig['uploaderVar'] 	= isset( $config['uploaderVar'] ) ? $config['uploaderVar'] : 'UPLOADER_'. $counter;
			
			$jsFile 		= __DIR__ . DS . 'html' . DS . 'uploader.js.tpl';
			$htmlFile 		= __DIR__ . DS . 'html' . DS . 'uploader.html.tpl';
			
			$jsContent 		= file_get_contents( $jsFile );
			$htmlContent 	= file_get_contents( $htmlFile );
			
			foreach( $uploaderConfig as $key => $value ){
				$jsContent = str_replace( '%{'. $key .'}', $value, $jsContent );
			}
			
			$htmlContent = str_replace( '%{id}', $uploaderConfig['id'], $htmlContent );
			
			$output->jsAddDeclaration( $jsContent );
			
			return $htmlContent;
			
		}

	}