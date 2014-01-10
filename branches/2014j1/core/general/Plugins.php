<?php
	class Oraculum_Plugins {
		public static function Load($plugin) {
			$plugin=strtolower($plugin);
			$arquivo=PATH.'plugins/'.$plugin.'.php';
			if(file_exists($arquivo)):
			  include_once($arquivo);
			else:
				throw new Exception('[Error CGP9] Plugin nao encontrado');
			endif;
		}
	}