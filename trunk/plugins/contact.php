<?php
/**
 * Componente de Contato
 *
 *
 *    @filesource
 *    @category       Components
 *    @package        oraculum
 *    @subpackage     oraculum.components.contato
 *    @required       PHPMailer 2.3 ou superior
 */

	class Oraculum_Contact {
		public $blankfields=FALSE;
		private $_required=array();
		private $_fields=array();
		private $_emailfield=NULL;
		private $_email=NULL;
		private $_errormsg=NULL;
		private $_to=NULL;
		private $_replyto=NULL;
		private $_subject=NULL;
		private $_message=NULL;
		private $_from=NULL;
		private $_fromname=NULL;
		private $_bcc=NULL;
		private $_smtphost=NULL;
		private $_smtpuser=NULL;
		private $_smtppassword=NULL;
		private $_smtpport=NULL;
		private $_ssl=FALSE;

		public function __construct($host=NULL, $user=NULL, $password=NULL, $smtpport=NULL, $ssl=FALSE) {
			$this->_smtphost=$host;
			$this->_smtpuser=$user;
			$this->_smtppassword=$password;
			$this->_ssl=(bool)$ssl;
			$this->_smtpport=(int)$smtpport;
		}

		public function addRequiredFields($obrigatorios=array()) {
			$this->_required=$obrigatorios;
		}
		public function addFields($fields=array()) {
			foreach($fields as $field=>$value):
				if((($value=='')||(is_null($value)))&&(in_array($field, $this->_required))):
					$this->blankfields=TRUE;
					$this->_errormsg.='O campo <strong>'.$field.'</strong> n&atilde;o foi informado!<br />';
				else:
					$this->addField($field, $value);
				endif;
			endforeach;
		}
		public function addField($campo, $valor) {
			$this->_fields[$campo]=$valor;
		}

		public function emailField($field='E-mail'){
			if (isset($this->_fields[$field])):
				$this->_emailfield=$field;
				$this->_email=$this->_fields[$field];
				//define('EMAIL_FIELD', $this->_fields[$field]);
			endif;
		}
		public function validyEmail() {
			$e=explode("@", $this->_email);
			if (count($e)==2):
				if (strpos($e[1], ".")):
					return checkdnsrr($e[1]);
				else:
					return FALSE;
				endif;
			else:
				return FALSE;
			endif;
		}
		public function __call($name, $values){
			if(stripos($name, 'set')!==false):
				$field='_'.strtolower(str_replace('set', '',$name));
				if(property_exists($this, $field)):
					$this->{$field}=$values[0];
				else:
					throw new Exception('Atributo invalido \''.$field.'\'');
				endif;
			endif;
		}
		public function send() {
			$from=$this->_from;
			$fromname=$this->_fromname;
			$to=is_array($this->_to)?$this->_to:array($this->_to);
			$subject=$this->_subject;
			//$message=$this->_message;
			$replyto=$this->_replyto;
			$bcc=$this->_bcc;
			$text=NULL;
			foreach ($this->_fields as $field=>$value):
				$text.='<strong>'.$field.':</strong> '.$value.'<br />'."\n";
			endforeach;
			include('plugins/mail.php');
			$mail=new Oraculum_Mail($this->_smtphost, $this->_smtpuser, $this->_smtppassword, $this->_smtpport, $this->_ssl);
			return $mail->sendmail($to,$subject,$text,$from,$fromname,$replyto,$bcc);
		}
		
		public function getErrors() {
			return $this->_errormsg;
		}
	}
	if (!function_exists('checkdnsrr')):
	  function checkdnsrr($host, $type='MX') {
		  $h=gethostbyname($h);
		  return gethostbyaddr($h);
	  }
	endif;