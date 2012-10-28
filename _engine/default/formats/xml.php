<?php
	
	
	class FormatXml extends Base{
		
		public function to_string($data){
			$xml = XMLCreator::newXMLDocument('module', array( 'engine' => ENGINE . ' ' . VERSION . ' ('. STATUS . ')' ));
			self::_build_xml($xml, $data);
			return $xml->toString();
		}
		
		static private function _build_xml($xml, & $data){
			if(!empty($data)){
				foreach( $data as $name => $value ){					
					if(is_scalar($value)){						
						$xml->addChild($name, array(), $xml->level()+1)->setData($value);
					}else if(is_array($value)){
						self::_build_xml($xml->addChild($name, array(), $xml->level()+1), & $value);
					}					
				}
			}			
		}
		
	}