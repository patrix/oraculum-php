<?php
/**
 * Tratamento de parametros HTTP
 *
 *
 *    @filesource     $HeadURL$
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.http
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision$
 *    @modifiedby     $LastChangedBy$
 *    @lastmodified   $Date$
 *
 */

	class Oraculum_HTTP {
		// Redirecionar
		public static function redirect($url) {
			if (isset($url)):
				header('Location: '.$url);
				// Usa javascript caso falhe o redirecionamento por cabecalho
				echo '<script type="text/javascript">';
				echo '  document.location.href=\''.$url.'\';';
				echo '</script>';
				exit;
			endif;
		}

		// Capturar endereco IP
		public static function ip() {
			$ip=$_SERVER['REMOTE_ADDR'];
			return $ip;
		}

		// Capturar HOST
		public static function host() {
			$host=isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:null;
			if ((is_null($host))||($host=='')) {
				$host=Oraculum_HTTP::ip();
			}
			return $host;
		}

		// Capturar Request URL
		public static function referer() {
			$referer=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:null;
			return $referer;
		}
	}
