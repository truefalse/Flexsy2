<?php

	class Access extends Base{
		
		private $_access = array();
		
		public function __construct(){
			parent::__construct();
			$this->_init();
		}
		
		private function _init(){
		
			$db = Registry::getInstance()->db;
			
			$query = 'SELECT 
						g.name AS title, 
						g.alias AS name, 
						g.id,  
						IFNULL(
							(SELECT GROUP_CONCAT( CONCAT(id,\',\',system_name) SEPARATOR  \'|\' ) 
								FROM  `#P_access_actions` 
								WHERE id
								IN (
									SELECT action_id
									FROM  `#P_access_xref` 
									WHERE group_id = g.id
								)
							)
						,0) AS actions
						
						FROM  `#P_access_groups` AS g
						WHERE g.enable = 1';
						
			$rows = $db->objectList($query);
			
			for($i=0,$c=count($rows); $i<$c; $i++){
			
				$this->_access[$rows[$i]->id] = array(
					'action' 	=> array(),
					'name' 		=> $rows[$i]->name,
					'title' 	=> $rows[$i]->title
				);
				
				$actions = explode('|', $rows[$i]->actions);
				
				for($j=0, $a = count($actions); $j<$a; $j++){
					list($id, $system_name) = explode(',', $actions[$j]);
					$this->_access[$rows[$i]->id]['action'][$id] = $system_name;
				}
				
			}
			
		}
		
		public function check( $gid = 0, $action = null ){
			$access = self::getInstance();
			return $access->canDoIt($gid, $action);
		}
		
		static public function getInstance(){ 
			static $instance = null;
			
			if(empty($instance)){
				$instance = new Access;
			}
			
			return $instance;
		}
		
		public function canDoIt( $groupId = 0, $action = null){
			
			if( ! (boolean) $action || ! (boolean) $groupId ){
				return false;
			}
			
			// Check whether the action of this group
			if( is_int( $action ) || is_numeric( $action ) ){
				// Return result by id of action
				return ! empty( $this->_access[$groupId]['action'][$action] );
			}else if( is_string( $action ) ){
				// Return result by alias of action
				return in_array( $action, $this->_access[$groupId]['action'] );
			}
		}
		
	}
