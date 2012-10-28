<?php

	class Header{
		
		static $_headers = array();
		
		static public function getContentType( $format = '' ){
		
			$content_types = array(
				'html' 	=> 'text/html',
				'json' 	=> 'application/json',
				'xml' 	=> 'application/xhtml+xml',
				'ini'	=> 'text/plain',
				# --- images
				'png'	=> 'image/png'
			);
			
			if( ! empty( $format ) ){
				return $content_types[$format];
			}
			
		}
		
		static public function setHeader( $name, $value ){
			$name 	= (string) $name;
			$value	= (string) $value;
			
			self::$_headers[] = array(
				'name' 	=> $name,
				'value'	=> $value
			);			
		}
		
		static public function & getHeaders(){
			return self::$_headers;
		}
		
		static public function sendHeaders(){
			if( ! headers_sent() ){
				foreach( self::$_headers as $header ){
					header( $header['name'] .': '. $header['value'], false );
				}
			}
		}
		
		static public function getInstance(){
			
		}		
		
	}