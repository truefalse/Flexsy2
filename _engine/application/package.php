<?php

	class Package extends Base{
		
		private $_data = array(),
				$_type = null;
		
		static private $_instances = array();
		
		function __construct( $packageXML ){
			
			// Execute parent construct to access parent method
			parent::__construct();
			
			// Check the existence of the file
			if( ! file_exists( $packageXML ) ){
				$this->_data = false;
				return;
			}
			
			// Create new instance of XMLParser
			$xml 	= XML::getInstance( $packageXML );			
			$object = $xml->getXMLNode();
			
			// Build array data from XML node
			$this->_toArray( $object );
			
		}
		
		public function & get_data(){
			return $this->_data;
		}
		
		public function & get_config(){
			return $this->_data[$this->_data['type']]['config'];
		}
		
		public function get_name(){
			return $this->_data[$this->_data['type']]['name'];
		}
		
		public function get_author(){
			return $this->_data[$this->_data['type']]['author'];
		}
		
		public function get_email(){
			return $this->_data[$this->_data['type']]['email'];
		}
		
		public function get_site(){
			return $this->_data[$this->_data['type']]['site'];
		}
		
		public function get_date_create(){
			return $this->_data[$this->_data['type']]['create_date'];
		}
		
		public function get_description(){
			return $this->get_descr();
		}
		
		public function get_descr(){
			return $this->_data[$this->_data['type']]['description'];
		}
		
		public function get_system_name(){
			return $this->_data['system_name'];
		}
		
		public function get_type(){
			return $this->_data['type'];
		}
		
		public function get_sysname(){
			return $this->_data['system_name'];
		}
		
		public function get_key( $full = false ){
			if( $full == false ){
				return $this->_data['short_key'];
			}else{
				return $this->_data['full_key'];
			}
		}
		
		public function buildConfig( $configNode, & $configArray ){
			
			foreach( $configNode->child() as $i => $section ){
				
				$section_title = t($section->attr('lang'));
				$section_alias = $section->attr('name');
				
				$configArray[$i] = array(
					'data' 	=> array(),
					'name' 	=> $section_alias,
					'title' => $section_title
				);
				
				$configField = & $configArray[$i]['data'];
				
				foreach( $section->child() as $field ){
				
					$title 	= $field->attr('name');
					$type 	= $field->attr('type');
					$name 	= $field->attr('key');
					
					if( $field->child() ){
						$value 	= self::_subFields( $field );
					}else{
						$value 	= $field->attr( 'value' );
					}
					
					$configField[] = array(
						'title' 	=> $title,
						'name' 		=> $name,
						'type'		=> $type,
						'value'		=> $value
					);
					
				}				
			}
			
		}
		
		private function _toArray( $xml = null ){
			$package 	= $xml->package;
			
			if( ! ( $package instanceOf XML_Element ) ){
				$this->_data = false;
				return false;
			}
			
			$type 			= $package->attr('type');
			$system_name 	= $package->attr('name');
			$client 		= $package->attr('client');
			
			$this->_data	= 	array( 
									'system_name' 	=> $system_name,
									'type'			=> $type,
									'client'		=> $client,
									'short_key'		=> 'package.'. $type,
									'full_key'		=> 'package.'. $type .'.'. $system_name,
								);
									
			$data = & $this->_data[$type];
			
			foreach( $package->child() as $tag ){	
			
				if( $tag->name() == 'config' ){
					$data['config'] = array();
					$this->buildConfig( $tag, $data['config'] );
					continue;
				}
				
				$data[$tag->name()] = $tag->data();
				
			}
			
		}
		
		static private function _subFields( XML_Element $fields ){
			$options = array();
			
			foreach( $fields->child() as $field ){
				$options[ $field->attr( 'value' ) ] = $field->data();
			}
			
			return $options;
		}
		
		static public function getInstance( $packageXML = null ){
		
			// md5 hash of file
			$hash = md5( $packageXML );
			
			// Checking already existing object for this file
			if( empty( self::$_instances[$hash] )){
				self::$_instances[$hash] = new Package( $packageXML );
			}
			
			// Return package object
			return self::$_instances[$hash];
			
		}
		
	}