<?php


	if( ! class_exists( 'OutputHepler' ) ){
		$registry = Registry::getInstance();
		$outputLoader = clone $registry->loader;
		$outputLoader->addDirectory( 'application/output' );
		$outputLoader->loadClass( 'helper' );
		$outputLoader->loadClass( 'tmpl' );
	}

	class Output extends Base{

		private $_template		 	= null;
		private $_layout		 	= 'index';

		private $_template_vars 	= array();
		private $_header_vars	 	= array();

		function __construct(){
			parent::__construct();
			$this->_template_vars 	= & OutputHepler::get_tmpl_vars();
			$this->_header_vars 	= & OutputHepler::get_header_vars();
		}

		public function setTemplate($name){
			$this->_template = $name;
		}

		public function set_layout($name){
			$this->_layout = $name;
		}

		public function assign($key, $value){

			if( ! is_array($this->_template_vars[$key] ) ){
				$this->_template_vars[$key] = array();
			}

			$this->_template_vars[$key][] = $value;

		}

		public function assign_ref($key, & $value){

			if( ! is_array( $this->_template_vars[$key] ) ){
				$this->_template_vars[$key] = array();
			}

			$this->_template_vars[$key][] = & $value;

		}

		public function assignArray(array $values){

			foreach($values as $key => $value){
				$this->assign($key, $value);
			}

		}

		public function jsAddFile($js_file){
			$this->_header_vars['js']['files'][] = $js_file;
		}

		public function jsAddDeclaration($js_declaration){
			$this->_header_vars['js']['declaration'][] = $js_declaration;
		}

		public function cssAddFile($css_file){
			$this->_header_vars['css']['files'][] = $css_file;
		}

		public function cssAddDeclaration($css_declaration){
			$this->_header_vars['css']['declaration'][] = $css_declaration;
		}

		public function setTitle($title){
			$this->_header_vars['title'] = array($title);
		}

		public function addTitle($title){
			$this->_header_vars['title'][] = $title;
		}

		public function & getBuffer(){
			return array(
				'header' 	=> & $this->_header_vars,
				'data' 		=> & $this->_template_vars
			);
		}

		public function render_tmpl($use_tmpl = false){

			$layout = OutputHepler::getTemplateLayout( $this->_template, $this->_layout );

			if( file_exists( $layout ) ){

				$data = OutputHepler::prepareData( $this->getBuffer() );

				$tmpl = Tmpl::create(array(
					'layout' 		=> $layout,
					'tmpl_name'		=> $this->_template,
					'data'			=> $data
				));
				
				return $tmpl->render();

			}else{
				$this->raiseFatal( 'Layout not found <br />' . $layout );
			}
		}

	}

