<?php

	class AdminFooButtons extends Base{
		
		private $_buttons 	= array();
		
		function __construct(){
			parent::__construct();
		}
		
		public function add_button($type, $value = false, $form_id = 'fle-admin-form', $form_action = false){
			$this->_buttons[] = array(
				'type' 			=> $type,
				'value'			=> $value,
				'form_action'	=> $form_action,
				'form_id'		=> $form_id
			);
			return $this;
		}
		
		public function & get_buttons(){
			return $this->_buttons;
		}
		
		static public function add( $type = '', $value = false, $form_id = 'fle-admin-form', $form_action = false ){
			$buttons = self::getInstance();
			$buttons->add_button( $type, $value, $form_id, $form_action );
		}
		
		static public function & get_button($type, $value = false, $form_id = 'fle-admin-form', $form_action = false){
			$out = null;
			switch($type){
				case 'save':
					$value = $value == false ? t('BTN_SAVE') : $value;
					$onclick_js = 'jQuery(\'form#'. $form_id .'\').submit();';
					$out = HTML::e('button', 'save', $value, 'Save', array(
						'class' 	=> 'fle-button-green',
						'onclick'	=> $onclick_js
					));
				break;
				case 'apply':
					$value = $value == false ? t('BTN_APPLY') : $value;
					$onclick_js = 'jQuery(\'form#'. $form_id .'\').submit();';
					$out = HTML::e('button', 'apply', $value, 'Apply', array(
						'class' 	=> 'fle-button-blue',
						'onclick'	=> $onclick_js
					));
				break;
				case 'edit':
					$value = $value == false ? t('BTN_EDIT') : $value;
					$onclick_js = 'jQuery(\'form#'. $form_id .'\').submit();';
					$out = HTML::e('button', 'edit', $value, 'Edit', array(
						'class' 	=> 'fle-button-yellow',
						'onclick'	=> $onclick_js
					));
				break;
				case 'delete':
					$value = $value == false ? t('BTN_DEL') : $value;
					$onclick_js = 'jQuery(\'form#'. $form_id .'\').submit();';
					$out = HTML::e('button', 'save', $value, 'Delete', array(
						'class' 	=> 'fle-button-black',
						'onclick'	=> $onclick_js
					));
				break;
				case 'cancel':
					$value = $value == false ? t('BTN_CANCEL') : $value;
					$onclick_js = 'history.back();';
					$out = HTML::e('button', 'save', $value, 'Cancel', array(
						'class' 	=> 'fle-button-red',
						'onclick'	=> $onclick_js
					));
				break;
				default:
					$out = '<b>Unknown button \''. $type .'\'</b>';
				break;
			}
			return $out;
		}
		
		static public function render(){
			$out = '';
			foreach(self::getInstance()->get_buttons() as $btn){
				$out .= self::get_button($btn['type'], $btn['value'], $btn['form_id'], $btn['form_action']);
			}
			return $out;
		}
		
		static public function getInstance(){
			static $_instance;
			if(empty($_instance)){
				$_instance = new AdminFooButtons;
			}
			return $_instance;
		}
		
	}