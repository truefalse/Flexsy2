<?php

	class AdminApplication extends Application{
		
		function __construct(){
		
			parent::__construct();
			
			$this->_cfg->set( 'home_url', 'index/site/config' );
			$this->_cfg->set( 'addLangCodeToURL', false );
			$this->_cfg->set( 'useSmarty', false );
			
			self::$_client = 'admin';
			
		}
		
		
		public function run(){	
			parent::run();			
			// Include jQuery
			$this->_output->jsAddFile( '/assets/js/jquery-1.7.2.min.js' );				
			//Include Flesxy-form
			$this->_output->jsAddFile(URI::base() . 'assets/js/flexsy-form.js');			
			//Include system js file
			$this->_output->jsAddFile(URI::base() . 'assets/js/flexsy-admin.js');
			// Attach admin object
			$this->_output->assign_ref('tabs', AdminTabs::render());
			$this->_output->assign_ref('tab_panel_title', AdminTabs::getInstance()->get_tab_title());
			$this->_output->assign_ref('control_btn', AdminFooButtons::render());
		}
		
	}