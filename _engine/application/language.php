<?php


	class Language extends Base{
		
		private $_map 				= array(),
				$_words				= array(),
				$_languageCode		= null,
				$_kernel			= null;
		
		function __construct(){		
		
			parent::__construct();
			
			$this->_kernel	= Kernel::getInstance();
			
			$langTable 		= Table::getInstance( 'languages' );			
			$langRows		= $langTable->load();
			
			foreach( $langRows as $lang ){
				$this->_map[$lang->languageCode] = $lang;
			}
			
			if( ! $this->_detectLanguage() ){
				$this->warningMessage( 'Can not detect language' );
			}else{
				$this->setLanguage( $this->_languageCode );
			}
			
		}
		
		public function setLanguage( $languageCode ){
			$this->_languageCode = $languageCode;
			Registry::getInstance()->session->data['language'] = $this->_map[$languageCode];
		}
		
		public function getLanguage(){
			return $this->_languageCode;
		}
		
		public function load( $languageFile = 'main' ){
						
			static $loaded;
			
			if( empty( $loaded ) ){
				$loaded = array();
			}
			
			$languageFile = LANG_PATH . DS . $this->_languageCode . DS . $languageFile . '.php';
			
			$hash = md5( $languageFile );
			
			if( isset( $loaded[$hash] ) ){
				return true;
			}else{
				$loaded[$hash] = true;
			}
			
			if( ! file_exists( $languageFile ) ){
				
				FSO::Folder()->create( dirname( $languageFile ) );
				
				if( ! @fopen( $languageFile, 'w' ) ){
					$this->warningMessage( 'Can not create file<br />'. $languageFile );
					return false;
				}
				
			}
			
			require_once $languageFile;
			
			if( isset( $_lang ) && is_array( $_lang ) ){
			
				$_lang = array_change_key_case( $_lang, CASE_UPPER );
				
				$this->_words = array_merge( $this->_words, $_lang );
				
			}	
			
		}
		
		private function _detectLanguage(){
			
			$sessionLang 	= Registry::getInstance()->session->data['language']->languageCode;
			$configLang		= $this->_kernel->getConfig()->get( 'defaultLang' );
			$browserLang 	= $this->_browserLanguage();
			
			if( ! empty( $sessionLang ) ){
				$this->_languageCode 	= $sessionLang;
			}else if( ! empty( $configLang ) ){
				$this->_languageCode 	= $configLang;
			}else if( ! empty( $browserLang ) ){
				$this->_languageCode 	= $browserLang;
			}else{
				return false;
			}
			
			return true;
			
		}
		
		private function _browserLanguage( $priority = 0 ){
			
			$browserLangs = array();
			list( $browserLangs[0], $browserLangs[1] ) = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			for( $i = 0, $c = count( $browserLangs ); $i < $c; $i++ ){
				preg_match( '/(\w+-\w+)/i', $browserLangs[$i], $out );
				list( $browserLangs[$i] ) = explode( '-', $out[1] );
			}
			
			return $browserLangs[$priority];
		}
		
		public function translate( $key = null ){
			$key = strtoupper($key);
			return isset($this->_words[$key]) ? $this->_words[$key] : $key;
		}
		
		
	}
