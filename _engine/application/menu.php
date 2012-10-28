<?php

	class Menu extends Base{
		
		private $_db 			= null;
		
		static private $_menus 	= array();
		
		function __construct(){
			parent::__construct();
			$this->_db = Registry::getInstance()->db;
		}
		
		public function getItems( $menu_name = '', $depth = -1 ){
		
			$menu_name = strtolower( $menu_name );
			
			if( ! empty( self::$_menus[$menu_name] ) ){
				return self::$_menus[$menu_name];
			}
			
			self::$_menus[$menu_name] = & $this->_loadItems( $menu_name, 0, $depth );
			
			Registry::getInstance()->get( 'event' )->trigger( 'beforeMenuReturnItems', array( & self::$_menus[$menu_name] ) );
			
			$this->addActiveItem( self::$_menus[$menu_name] );
			
			return self::$_menus[$menu_name];
		}
		
		public function get_item_by_link( $link = '' ){
			$query = 'SELECT * FROM #P_menu_items AS i WHERE i.link = \''. $link .'\' AND i.enable = 1';
			return $this->_db->objectItem( $query );
		}
		
		public function get_current_item( $link = '' ){			
			return $this->get_item_by_link( Registry::getInstance()->get( 'request' )->get( 'route' ) );
		}
		
		private function addActiveItem( & $items ){		
		
			foreach( $items as & $item ){
				
				if( ! empty( $item['child'] ) ){
					$this->addActiveItem( $item['child'] );
				}
				
				if( URI::current() == $item['link'] ){			
					while( array_key_exists( 'parent', $item ) ){					
						$item['active'] 	= true;
						$item 				= & $item['parent'];		
					}
					break;
				}
				
			}
			
		}
		
		private function & _loadItems( $menu_name = '', $start = 0, $depth = 0, & $parent = array() ){
			$items = array();
			$where = array( 'm.name = \'' . (string) $menu_name . '\'' );
			
			if( Application::isBackend() ){
				$where = array( 'm.backend = 1', 'm.system = 1' );
			}else if( Application::isFrontend() ){
				$where = array( 'm.frontend = 1', 'm.system = 0' );
			}
			
			$where[] = 'm.enable = 1';
			$where[] = 'i.enable = 1';
			$where[] = 'i.parent_id = ' . (int) $start;
			
			$query = 'SELECT m.mod_rewrire_enable AS sef, i.id, i.parent_id, i.title, i.link, i.params
						FROM #P_menu AS m
						INNER JOIN #P_menu_items AS i ON i.menu_id = m.id
						WHERE ' . join( ' AND ', $where ) . ' ORDER BY i.order';
			
			$rows = $this->_db->objectList($query);
			
			if( ! empty( $rows ) ){
			
				foreach($rows as $i => $row){
				
					$link = Router::link( 
								explode( '/', $row->link ), 
								array_merge( (array) json_decode( $row->params ), array() ), 
								$row->sef 
							);
					
					$items[$i] = array(
						'id' 			=> $row->id,
						'title' 		=> $row->title,
						'alias' 		=> $row->alias,
						'link' 			=> $link,
						'active'		=> false,
						'menu_id'		=> 'menu_item_' . $row->id,
						'parent'		=> & $parent,
					);
					
					if( (int) $depth == -1 ){
						$items[$i]['child'] = & $this->_loadItems( $menu_name, $row->id, -1, $items[$i] );
					}else{
						if($depth == 0){
							$items[$i]['child'] = array();
							break;
						}else{
							$items[$i]['child'] = & $this->_loadItems( $menu_name, $row->id, --$depth, $items[$i] );
						}
					}
					
				}
			}
			
			return $items;
		}
		
	}