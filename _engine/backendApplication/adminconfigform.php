<?php

	class AdminConfigForm extends Base{
		
		private $_xml_file 	= null,
				$_data  	= array();
		
	 	static private $_instances = array();
		
		public function __construct($xml_file){
			parent::__construct();
			$this->_xml_file = $xml_file;
			$this->_data = & $this->_build_array();
		}
		
		private function _find_config_tag($xml){
			$config = null;
			
			foreach($xml->child() as $tag){
				if($tag->name() == 'config'){
					$config = $tag;
					break;
				}
			}
		
			return $config;
		}
		
		private function & _build_array(){
		
			$xml = new XML;
			
			$xml->loadFile($this->_xml_file);
			
			$config = $this->_find_config_tag($xml->getXML()->package);
			
			$data = array();
			
			foreach($config->child() as $i => $section){
				
				$section_title = t($section->attr('lang'));
				$section_alias = $section->attr('name');
				
				$data[$i] = array(
					'data' 	=> array(),
					'name' 	=> $section_alias,
					'title' => $section_title
				);
				
				$data_field = & $data[$i]['data'];
				
				foreach($section->child() as $field){
				
					$title 	= $field->attr('name');
					$type 	= $field->attr('type');
					$name 	= $field->attr('key');
					
					$data_field[] = array(
						'title' 	=> $title,
						'name' 		=> $name,
						'type'		=> $type
					);
					
				}				
			}
			
			return $data;
			
		}
		
		public function & get_data(){
			return $this->_data;
		}
		
		static public function getInstance($xml_file){
			if(empty(self::$_instances[md5($xml_file)])){
				self::$_instances[md5($xml_file)] = new AdminConfigForm($xml_file);
			}
			return self::$_instances[md5($xml_file)];
		}
		
	}