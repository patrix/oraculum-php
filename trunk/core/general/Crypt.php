<?php
/**
 * Tratamento de criptografia
 *
 *
 *    @filesource     $HeadURL$
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.crypt
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision$
 *    @modifiedby     $LastChangedBy$
 *    @lastmodified   $Date$
 *
 */

	class Oraculum_Crypt {
		// Criptografar string
		public static function strcrypt($str) {
			$str=base64_encode($str);
			$str=base64_decode($str);
			$str=str_rot13($str);
			$str=base64_encode($str);
			$str=str_rot13($str);
			return $str;
		}

		// Descriptografar string
		public static function strdcrypt($str) {
			$str=str_rot13($str);
			$str=base64_decode($str);
			$str=str_rot13($str);
			$str=base64_encode($str);
			$str=base64_decode($str);
			return $str;
		}
		
		public static function blowfish($string, $custo=10) {
			$seed=uniqid(rand(), true);
			$salt=base64_encode($seed);
			$salt=str_replace('+', '.', $salt);
			$salt=substr($salt, 0, 22);
			$crypt=crypt($string, '$2a$'.$custo.'$'.$salt.'$');
			return $crypt;
		}

		public static function blowfishcheck($string, $hash) {
			return (crypt($string, $hash) === $hash);
		}
	}
