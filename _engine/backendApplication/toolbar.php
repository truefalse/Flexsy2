<?php

	class ToolBar extends Base{
		
		private $_name 		= null,
				$_output 	= array();
		
		public function __construct( $name ){
			$this->_name 	= $name;
			$this->_output 	= Registry::getInstance()->get( 'output' );
		}
		
		public function add_submit( $action = null, $value = 'Submit', $color = 'black', $form_id = null ){
			$colors = array( 'red', 'green', 'blue', 'yellow', 'black' );
			
			$color = strtolower( $color );
			if( ! in_array( $color, $colors ) ){
				$color = 'black';
			}
			
			if( !! $form_id{0} ){
				$form_id = '{formId:\''. $form_id .'\'}';
			}
			
			$button = HTML::e( 'button', $action, t( $value ), null, array(
				'class' 	=> 'fle-button-'. $color,
				'onclick'	=> 'FleAdmin('. $form_id .').submit(\''. $action .'\');'
			) );
			
			$this->_output->assign_ref( $this->_name, $button );
		}
		
		public function add_link( $link = null, $value = 'Redirect', $color = 'black' ){
			$colors = array( 'red', 'green', 'blue', 'yellow', 'black' );
			
			$color = strtolower( $color );
			if( ! in_array( $color, $colors ) ){
				$color = 'black';
			}
			
			if( empty( $link ) ){
				$link = URI::current();
			}
			
			$button = HTML::e( 'button', null, t( $value ), null, array(
				'class' 	=> 'fle-button-'. $color,
				'onclick'	=> 'FleAdmin().link(\''. $link .'\');'
			) );
			
			$this->_output->assign_ref( $this->_name, $button );
		}
		
		static public function getInstance( $name = 'control_buttons' ){
			static $instances;
			
			if ( empty( $instances ) ) {
				$instances = array();
			}
			
			if ( empty( $instances[$name] ) ) {
				$instances[$name] = new ToolBar( $name );
			}
			return $instances[$name];
		}
		
	}