<?php
	
	class View extends Base{
		
		static private $_base_path		= null;
		
		private $_module_name	= null;
		private $_page_name		= null;
		private $_params		= null;
		
		private $_layout 		= null;
		private $_data			= array();
		
		function __construct( array & $config ){
		
			parent::__construct();
			
			self::$_base_path 		= $config['base_path'];

			$this->_name			= $config['name'];
			$this->_page			= $config['page'];
			
			$this->setParams( $config['params'] );
			$this->setLayout( $this->_params->get( 'layout', 'index' ) );
			
		}
		
		public function assign( $key, $value ){
			$this->_data[$key] = $value;
		}
		
		public function assign_ref( $key, & $value ){
			$this->_data[$key] = & $value;
		}
		
		public function assign_array( array & $values ){
			foreach( $values as $key => $value ){
				$this->assign_ref( $key, $value );
			}
		}
		
		public function render( $format = 'html' ){		
		
			$output = null;	
			
			$config = Registry::getInstance()->get( 'config' );
			
			if( $config->get( 'useSmarty', false ) ){
				
				include_once  LIBS_PATH . DS .'Smarty-3.1.12'. DS .'Smarty.class.php';
			
				$smarty = new Smarty;
				
				$smarty->template_dir 	 = TEMPLATES_PATH . DS . $config->get( 'template' );
				$smarty->compile_dir 	 = CACHE_PATH . DS . 'compiled';
				$smarty->cache_dir 	 	 = CACHE_PATH;
				
				foreach( $this->_data as $key => $value ){
					$smarty->assign( $key, $value );
				}
				
				$smarty->assign( 'config', 		$config );
				$smarty->assign( 'params', 		$this->_params );
				
				$output = $smarty->fetch( $this->getLayout() . '.' . $this->getExt() );
				
			}else{
			
				$layout = self::getLayoutPath( $this->_page, $this->_layout, $format );
		
				if( ! file_exists( $layout ) ){
					$this->raiseFatal( 'Layout not exists<br />'. $layout );
				}
				
				$output = self::_evalPHP( $layout, $this->_data );
				
			}
			
			return $output;
		}
		
		public function setLayout( $name = null ){
			$this->_layout = $name;
		}
		
		public function getLayout(){
			return $this->_layout;
		}
		
		public function getExt(){
			return Registry::getInstance()->get( 'config' )->get( 'useSmarty' ) 
					? 	'tpl'
					:	'php';
		}
		
		private function setParams( $params ){
			$this->_params = $params;
		}
		
		static public function getLayoutPath( $page = 'index', $layout = 'index', $format = 'html' ){
		
			$path 	= self::$_base_path . DS .'views';
			$path 	.= DS . $page . DS;
			
			$file = $layout;
			if( $format <> 'html' ){
				$file .= '.'. $format;
			}
			$file .= '.php';
			
			return $path . $file;
		}
		
		static private function _evalPHP( $file, array $data = array() ){
			
			ob_start();
				extract( $data );
				require $file;
			$output = ob_get_contents();
			ob_clean();
			
			return $output;
		}
	}
