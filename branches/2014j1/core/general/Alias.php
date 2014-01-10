<?php
  /*
   * Arquivo com funcoes globais que na verdade chamam funcoes de
   * suas respectivas classes
   */
	class Oraculum_Alias
	{
		public static function AddAlias($newfunction, $originalfunction) {
			if(function_exists($newfunction)):
				throw new Exception('[Erro CGA10] A funcao \''.$newfunction.'\' ja existe');
			elseif (!is_callable($originalfunction)):
				throw new Exception('[Erro CGA12] A funcao \''.$originalfunction.'\' nao pode ser chamada');
			else:
				eval('function '.$newfunction.'() {
					$args=func_get_args();
					return call_user_func_array(\''.$originalfunction.'\',$args);
				       }');
			endif;
		}
		
		public static function LoadAlias($class) {
			if (($class=='Request')||($class=='All')):
				Oraculum::Load('Request');
				/**********************************
				 * Tratamento de requisicoes
				 **********************************/
				 Oraculum_Request::getpagina();
				  Oraculum_Alias::AddAlias('post', 'Oraculum_Request::post');
				  Oraculum_Alias::AddAlias('get', 'Oraculum_Request::get');
				  Oraculum_Alias::AddAlias('sess', 'Oraculum_Request::sess');
				  Oraculum_Alias::AddAlias('setsess', 'Oraculum_Request::setsess');
				  Oraculum_Alias::AddAlias('unsetsess', 'Oraculum_Request::unsetsess');
				  Oraculum_Alias::AddAlias('init_sess', 'Oraculum_Request::init_sess');
				  Oraculum_Alias::AddAlias('set_cookie', 'Oraculum_Request::set_cookie');
				  Oraculum_Alias::AddAlias('cookie', 'Oraculum_Request::set_cookie');
				  Oraculum_Alias::AddAlias('getpagina', 'Oraculum_Request::getpagina');
				  Oraculum_Alias::AddAlias('getid', 'Oraculum_Request::getid');
				  Oraculum_Alias::AddAlias('getlast', 'Oraculum_Request::getlast');
				  Oraculum_Alias::AddAlias('getvar', 'Oraculum_Request::getvar');
				  Oraculum_Alias::AddAlias('gets', 'Oraculum_Request::gets');
			endif;
			if (($class=='Crypt')||($class=='All')):
				Oraculum::Load('Crypt');
				/**********************************
				 * Tratamento de criptografia
				 **********************************/
				  Oraculum_Alias::AddAlias('strcrypt', 'Oraculum_Crypt::strcrypt');
				  Oraculum_Alias::AddAlias('strdcrypt', 'Oraculum_Crypt::strdcrypt');
				  Oraculum_Alias::AddAlias('blowfish', 'Oraculum_Crypt::blowfish');
				  Oraculum_Alias::AddAlias('blowfishcheck', 'Oraculum_Crypt::blowfishcheck');
			endif;
			if (($class=='HTTP')||($class=='All')):
				Oraculum::Load('HTTP');
				/**********************************
				 * Tratamento de parametros HTTP
				 **********************************/
				  Oraculum_Alias::AddAlias('redirect', 'Oraculum_HTTP::redirect');
				  Oraculum_Alias::AddAlias('ip', 'Oraculum_HTTP::ip');
				  Oraculum_Alias::AddAlias('host', 'Oraculum_HTTP::host');
			endif;
			if (($class=='Validate')||($class=='All')):
				Oraculum::Load('Validate');
				/**********************************
				 * Tratamento de formularios
				 **********************************/
				  Oraculum_Alias::AddAlias('validar', 'Oraculum_Validate::validar');
				  Oraculum_Alias::AddAlias('verificaCPF', 'Oraculum_Validate::verificaCPF');
				  Oraculum_Alias::AddAlias('verificaEmail', 'Oraculum_Validate::verificaEmail');
			endif;
			if (($class=='Views')||($class=='All')):
				Oraculum::Load('Views');
				/**********************************
				 * Tratamento de Views
				 **********************************/

			endif;
			if (($class=='Text')||($class=='All')):
				Oraculum::Load('Text');
				/**********************************
				 * Tratamento de informacoes textuais
				 **********************************/
				  Oraculum_Alias::AddAlias('moeda', 'Oraculum_Text::moeda');
				  Oraculum_Alias::AddAlias('moedasql', 'Oraculum_Text::moedasql');
				  Oraculum_Alias::AddAlias('data', 'Oraculum_Text::data');
				  Oraculum_Alias::AddAlias('data_mysql', 'Oraculum_Text::data_mysql');
				  Oraculum_Alias::AddAlias('hora', 'Oraculum_Text::hora');
				  Oraculum_Alias::AddAlias('saudacao', 'Oraculum_Text::saudacao');
				  Oraculum_Alias::AddAlias('getpwd', 'Oraculum_Text::getpwd');
				  Oraculum_Alias::AddAlias('inflector', 'Oraculum_Text::inflector');
				  Oraculum_Alias::AddAlias('plural', 'Oraculum_Text::plural');
			endif;
			if (($class=='Files')||($class=='All')):
				Oraculum::Load('Files');
				/**********************************
				 * Tratamento de inclusao de arquvos
				 **********************************/
				  Oraculum_Alias::AddAlias('inc', 'Oraculum_Files::inc');

			endif;
			if(($class=='Logs')||($class=='All')):
				Oraculum::Load('Logs');
				/**********************************
				 * Tratamento de erros e logs
				 **********************************/
				  Oraculum_Alias::AddAlias('alert', 'Oraculum_Logs::alert');
			endif;
		}
	}
