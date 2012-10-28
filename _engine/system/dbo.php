<?php
	
    
	class DBO extends PDO{
		
		private $PDOStatement = null;
		
		static private $queryCounter = 0;
		
        private $_cfg = null;
        
		function __construct(){
            $cfg = $this->_cfg = Registry::getInstance()->get('config');
			$connect_string = 'mysql:host=' . $cfg->get( 'db_host' ) . ';dbname=' . $cfg->get( 'db_name' );
			$init_cmd = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
			try{
				parent::__construct( $connect_string, $cfg->get( 'db_username' ), $cfg->get( 'db_passwd' ), $init_cmd );
			}catch(Exception $e){
				Registry::getInstance()->get('error')->raise($e->getMessage(), 'fatal');
			}
			parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
        private function bindValues( & $pdo_statement, $params = array() ){
            
            if( is_object( $pdo_statement ) && !! ( $pdo_statement instanceof PDOStatement ) && ! empty( $params ) ){
                for( $i=0,$c=count($params);$i<$c;$i++ ){
                    
                    if( ! is_array( $params[$i] ) )
                        continue;
                    
                    $field = is_numeric($params[$i][0])?(int)$params[$i][0]:':'.(string)$params[$i][0];
                        
                    $value = $params[$i][1];
                    
                    switch( strtoupper( $params[$i][2] ) ){
                        case 'INT':
                            $filter = parent::PARAM_INT;
                        break;
                        case 'NULL':
                            $filter = parent::PARAM_NULL;
                        break;
                        case 'STRING':
                        case 'STR':
                            $filter = parent::PARAM_STR;
                        break;
                        case 'BOOL':
                        case 'BOOLEAN':
                            $filter = parent::PARAM_BOOL;
                        break;
                        default:
                            $filter = false;
                        break;
                    }
                    
                    $lenght = $params[$i][3] ? intval( $params[$i][3] ) : false;
					
                    $pdo_statement->bindValue( $field, $value, $filter );
                }
            }
        }
        
		public function paramsDump(){
			return $this->PDOStatement->debugDumpParams();
		}
		
        private function _replacePrefix( & $query ){
            $query = preg_replace( '/#P/', $this->_cfg->get( 'db_prefix' ), $query );
        }
        
        private function & _prepare( $query, $params = array() ){
            if( ! $query{0} )
                return false;
            $this->_replacePrefix( $query );
            $this->PDOStatement = parent::prepare( $query );
            self::bindValues( $this->PDOStatement, $params );
        }
        
        private function _execute( $query, $params ){
			
			self::$queryCounter++;
			
            $this->_prepare( $query, $params );
            $this->PDOStatement->execute();
        }
        
        public function execute( $query = '', $params = array() ){
            $this->_execute( $query, $params );
            return $this->PDOStatement->rowCount();
        }
        
		public function & objectList( $query, $params = array() ){
			$this->_execute( $query, $params );
            return $this->PDOStatement->fetchAll( parent::FETCH_OBJ );
		}
		
		public function & arrayList( $query, $params = array() ){
			$this->_execute( $query, $params );
            return $this->PDOStatement->fetchAll( parent::FETCH_ASSOC );
		}
		
		public function & numList( $query, $params = array() ){
			$this->_execute( $query, $params );
            return $this->PDOStatement->fetchAll( parent::FETCH_NUM );
		}
		
		public function & objectItem( $query, $params = array() ){
			$rows =& $this->objectList( $query, $params );
            return $rows[0];
		}
		
		public function & arrayItem( $query, $params = array() ){
			$rows =& $this->arrayList( $query, $params );
            return $rows[0];
		}
		
		public function & numItem( $query, $params = array() ){
			$rows =& $this->numList( $query, $params );
            return $rows[0];
		}
		
		static public function getQueryCounter(){
			return (int) self::$queryCounter;
		}
        
	}
	
?>
