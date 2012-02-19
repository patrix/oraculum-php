<?php
	class Oraculum_Register
	{
		private $_vars=array();
		private static $_instance=NULL;

		public static function getInstance()
		{
			if (is_null(self::$_instance)) {
				self::setInstance(new Oraculum_Register);
			}
			return self::$_instance;
		}

		public static function setInstance(Oraculum_Register $instance)
		{
			self::$_instance=$instance;
		}
    
		public static function set($id, $value) {
			$instance=self::getInstance();
			$instance->_vars[$id]=$value;
		}

		public static function get($id) {
        		$instance=self::getInstance();
        		if (isset($instance->_vars[$id])) {
				return $instance->_vars[$id];
        		} else {
	        		return NULL;
        		}
		
		}
		public static function getVars() {
        		$instance=self::getInstance();
        		if (isset($instance->_vars)) {
				return $instance->_vars;
        		} else {
	        		return NULL;
        		}
		}
	}
