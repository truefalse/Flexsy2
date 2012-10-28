<?php
	
	class XML extends Base{
	   
		private $_parser         = null;       
		private $_stack          = array();       
		private $_rootNode       = null;
		private $_rootName       = null;

		function __construct( $XMLFile = null ){         

			$this->_parser = xml_parser_create(''); 
			xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);           
			xml_set_element_handler( $this->_parser, array( & $this, '_startTag' ), array( & $this, '_endTag' ) );
			xml_set_character_data_handler( $this->_parser, array( & $this, '_charData' ) );        
			
			$this->_rootNode = new stdClass;    
			
			// Check variable
			if( ! (boolean) $XMLFile ){
				throw new Exception( 'File path is empty' );
				return;
			}
			
			// Check file extension
			if( strtolower( end( explode( '.', $XMLFile ) ) ) !== 'xml' ){
				throw new Exception( 'Wrong extension of the file' );
				return;
			}
			
			// Check the existence of the file
			if( ! file_exists( $XMLFile ) ){
				throw new Exception( 'File not exists: '. $file );
				return;
			}
			
			// Check if file readable
			if( ! is_readable( $XMLFile ) ){
				throw new Exception( 'I can`t read this file: '. $file );
				return;
			}else{
				$content = file_get_contents( $XMLFile );
			}
			
			$this->_parse( $content );
			
		}

		public function & getXMLNode(){
			return $this->_rootNode;
		}

		private function _parse( $data = '' ){
			if( xml_parse( $this->_parser, $data ) ){
				xml_parser_free( $this->_parser );
			}else{
				throw new Exception( 'Parse was failed' );
			}
		}

		private function _startTag( $parser, $name, $attrs = array() ){

			$name = strtolower( $name );
			
			if( count( $this->_stack ) == 0 ){			
				$this->_rootNode->{ $name } = new XML_Element( $name, $attrs, 1 );
				$this->_stack = array( '_rootNode', $name );				
			}else{			
				$parent = implode( '->', $this->_stack );
				eval( '$this->' . $parent . '->addChild( $name, $attrs, ' . count( $this->_stack ) . ' );' );  
				eval( '$this->_stack[] = $name . "[" . ( count( $this->' . $parent . '->' . $name . ' ) - 1 ) . "]";' ); 
			}
			
		}

		private function _endTag( $parser, $data ){
			array_pop( $this->_stack );
		}

		private function _charData( $parser, $data ){
			eval( '$this->' . implode( '->', $this->_stack ) . '->addData( $data );' );
		}

		static public function getInstance( $XMLFile = null ){
			
			// Get md5 hash
			$hash = md5( $XMLFile );
			
			static $instances;
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new XML( $XMLFile );
			}
			
			return $instances[$hash];
			
		}
	   
	}
    
    class XMLCreator{
        
        static public function & newXMLDocument( $name, $attrs = array() ){
            $xmlDoc = new XML_Element( $name, $attrs, 0 );
            return $xmlDoc;
        }
        
    }
    
    class XML_Element extends Base{
        
        private $_name  = null;
        private $_attrs = array();
        private $_data  = null;
        private $_child = null;
        private $_level = null;
        
        function __construct( $name, $attrs = array(), $level = 0 ){
           
			if( ! empty( $attrs ) ){
				$this->_attrs = array_change_key_case( $attrs, CASE_LOWER );
			}
			
            $this->_name = $name;
            $this->_level = $level;    
			
        }
        
        public function & addChild( $name, $attrs = array(), $level = 0 ){   
		
            $children = new XML_Element( $name, $attrs, $level );
			
            $this->{$name}[] =& $children;
            $this->_child[] =& $children;
			
            return $children;
        }
        
        public function setAttr( $name, $value ){
            $this->_attrs[$name] = $value;
        }
        
        public function removeAttr( $name ){
            unset( $this->_attrs[$name] );
        }
        
        public function data(){
            return $this->_data;
        }
		
		public function child(){
            return $this->_child;
        }
        
        public function setData( $data = '' ){
            $this->_data = $data;
        }
        
        public function addData( $data = '' ){
			$this->_data .= $data;            
        }
        
        public function name(){
            return $this->_name;
        }
        
        public function level(){
            return $this->_level;
        }
        
        public function attr( $name ){
            return isset( $this->_attrs[$name] ) ? $this->_attrs[$name] : null;
        }
        
        public function toString(){
			
            $output .= "\n" . str_repeat( "\t", $this->_level ) . '<' . $this->_name;
            
            foreach( $this->_attrs as $attr => $value ){
				$output .= ' ' . $attr . '="' . htmlspecialchars( $value ) . '"';
			}
              
            if( empty( $this->_child ) && empty( $this->_data ) ){
				$output .= ' />';
			}                
            else{
			
                if( ! empty( $this->_child ) ){
				
                    $output .= '>';
                    $count = count( $this->_child );
					
                    for( $i=0; $i<$count; $i++ ){
						$output .= $this->_child[$i]->toString();
					}
                       
                    $output .= "\n" . str_repeat( "\t", $this->_level );
					
                }else if( ! empty( $this->_data ) ){
                    $output .= '>' . htmlspecialchars( $this->_data );
                }
				
                $output .= '</' . $this->_name . '>';
            }
            
            return $output;
        }
       
	}
