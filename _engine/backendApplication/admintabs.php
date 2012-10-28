<?php

	class AdminTabs extends Base{
		
		private $_tabs 				= array(),
				$_tab_panel_title 	= null;
		
		function __construct(){
			parent::__construct();
		}
		
		public function add_tab($title, $id){
			$this->_tabs[] = array(
				'title' => $title,
				'id'	=> $id
			);
			return $this;
		}
		
		public function setTitle($title){
			$this->_tab_panel_title = $title;
		}
		
		public function & get_tabs(){
			return $this->_tabs;
		}
		
		static public function add($title = '', $id = ''){
			$tabs = self::getInstance();
			$tabs->add_tab($title, $id);
		}
		
		static public function title($title = ''){
			$tabs = self::getInstance();
			$tabs->setTitle($title);
		}
		
		public function get_tab_title(){
			return $this->_tab_panel_title;
		}
		
		static public function render(){
			$out = '<ul id="admin-tabs">';
			
			foreach(self::getInstance()->get_tabs() as $i => $tab){
				$out .= '<li id="admin-tab-'. $tab['id'] .'" '. ($i==0?'class="active"':null) .'><a href="#'. $i .'">'. $tab['title'] .'</a></li>';
			}
			
			$out .= '</ul>';
			return $out;
		}
		
		static public function getInstance(){
			static $_instance;
			if(empty($_instance)){
				$_instance = new AdminTabs;
			}
			return $_instance;
		}
		
	}