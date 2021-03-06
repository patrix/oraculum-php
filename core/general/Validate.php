<?php
/**
 * Validacoes
 *
 *
 *    @filesource     $HeadURL: https://oraculum-php.googlecode.com/svn/trunk/core/general/Validate.php $
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.validate
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision: 90 $
 *    @modifiedby     $LastChangedBy: Patrixsbs $
 *    @lastmodified   $Date: 2012-05-24 13:46:17 -0300 (qui, 24 mai 2012) $
 *
 */

	class Oraculum_Validate {
		public static function validar($valor=null, $tipo='s', $notnull=false) {
			/*
			* Tipos:
			*   s: string
			*   n: numeric
			*   i: inteiro
			*   c: cpf
			*   e: email
			*   E: email validando o dominio
			*   d: data
			*   N: null
			*/
			if((!$notnull)&&(is_null($valor))):
				$retorno=true;
			else:
				switch($tipo):
				case 's':
					$retorno=is_string($valor);
				break;
				case 'n':
					$retorno=is_numeric($valor);
				break;
				case 'i':
					$retorno=is_int($valor);
				break;
				case 'c':
					$retorno=Oraculum_Forms::verificaCPF($valor);
				break;
				case 'e':
					$retorno=filter_var($valor, FILTER_VALIDATE_EMAIL);
					$retorno=($retorno===false)?false:true;
				break;
				case 'E':
					$retorno=filter_var($valor, FILTER_VALIDATE_EMAIL);
					if($retorno):
						$retorno=Oraculum_Forms::verificaEmail($valor);
					endif;
				break;
				case 'N':
					$retorno=is_null($valor);
				break;
				case 'd':
					$valor=trim($valor);
					if (strpos($valor, ' ')):
						$valor=explode(' ', $valor);
						$valor=$valor[0];
					endif;
					if (strpos($valor, '/')):
						$data=explode('/', $valor);
					elseif(strpos($valor, '-')):
						$data=explode('-', $valor);
					elseif(strpos($valor, '.')):
						$data=explode('.', $valor);
					else:
						return false;
					endif;
					/*$data=trim($data);*/
					if(sizeof($data)==3):
						if($data['2']>$data['0']):
							if($data['0']>12):
								$ano=$data['2'];
								$mes=$data['1'];
								$dia=$data['0'];
							else:
								$ano=$data['2'];
								$mes=$data['0'];
								$dia=$data['1'];
							endif;
						else:
							$ano=$data['0'];
							$mes=$data['1'];
							$dia=$data['2'];
						endif;
						$retorno=checkdate((int)$mes, (int)$dia, (int)$ano);
					else:
						$retorno=false;
					endif;
					return $retorno;
				break;
				default:
				$retorno=is_string($valor);
				break;
				endswitch;
			endif;
			return $retorno;
		}

		public static function verificaCPF($cpf) {
			$recebecpf=$cpf;
			//Retirar todos os caracteres que nao sejam 0-9
			$s=null;
			for ($x=1;$x<=strlen($recebecpf);$x=$x+1):
				$ch=substr($recebecpf, $x-1, 1);
				if(ord($ch)>=48 && ord($ch)<=57):
					$s=$s.$ch;
				endif;
			endfor;
			if((strlen($s)==11)&&($cpf!='00000000000')&&($cpf!='99999999999')):
				$somaA=(int)($s[0]*10);
				$somaA=$somaA+($s[1]*9);
				$somaA=$somaA+($s[2]*8);
				$somaA=$somaA+($s[3]*7);
				$somaA=$somaA+($s[4]*6);
				$somaA=$somaA+($s[5]*5);
				$somaA=$somaA+($s[6]*4);
				$somaA=$somaA+($s[7]*3);
				$somaA=$somaA+($s[8]*2);
				$resto=$somaA%11;
				$digitoA=$resto<2?0:11-$resto;
				$somaB=($s[0]*11);
				$somaB=$somaB+($s[1]*10);
				$somaB=$somaB+($s[2]*9);
				$somaB=$somaB+($s[3]*8);
				$somaB=$somaB+($s[4]*7);
				$somaB=$somaB+($s[5]*6);
				$somaB=$somaB+($s[6]*5);
				$somaB=$somaB+($s[7]*4);
				$somaB=$somaB+($s[8]*3);
				$somaB=$somaB+($s[9]*2);
				$resto=$somaB%11;
				$digitoB=$resto<2?0:11-$resto;
				if(($s[9]==$digitoA)&&($s[10]==$digitoB)):
					return true;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		}

		public static function verificaEmail($email) {
			// Verifica se o e-mail e valido
			if (strpos($email, '@')):
				$e=explode('@', $email); // Transforma o email em array.
				if(count($e)==2):
					if(strpos($e[1], '.')):
						$check=checkdnsrr($e[1]);
					else:
						$check=false;
					endif;
				else:
					$check=false;
				endif;
			else:
				$check=false;
			endif;
			return $check;
		}
	}
