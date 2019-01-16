<?php
	Class DB{
		private $_query, $_results, $_count, $_pdo;
		private static $_instance;
		public function __construct(){
			$this->_pdo = new PDO('mysql:host=127.0.0.1;dbname=gymplan', 'hogrider','PBF5szWTUcznwbXz');
			//By providing a password and username this adds security to database
		}
		public static function getInstance(){
			if(!isset(self::$_instance)){
				self::$_instance = new DB();
			}
			return self::$_instance;
		}
		
		public function prep($query) {
			
			return $this -> _pdo -> prepare($query);
		}
		
		public function query($sql, $params = array()){
			$this->_query = $this->_pdo->prepare($sql);
			if(count($params)){
				$i = 1;
				foreach($params as $param){
					$this->_query->bindValue($i, $param);
					$i++;
				}
			}
			$this->_query->execute();
			$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
			$this->_count = $this->_query->rowCount();
			return $this;
		}
		public function results(){
			return $this->_results;
		}
		public function count(){
			return $this->_count;
		}
	}
?>