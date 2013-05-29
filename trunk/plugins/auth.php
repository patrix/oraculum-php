<?php
    class Oraculum_Auth {

        private $_sessname='ofauth';
        private $_logoffurl=NULL;
        private $_loginurl=NULL;
        private $_homeurl=NULL;
        private $_dbobj=NULL;
        private $_keyfield=NULL;
        private $_userfield=NULL;
        private $_passwordfield=NULL;
        private $_emailfield=NULL;
        private $_cryptkeyfield=NULL;
        private $_user=NULL;
        private $_password=NULL;
        private $_email=NULL;
        private $_cryptkey=NULL;
        private $_crypttype='md5';
        private $_crypttypes=array('md5', 'sha1', 'blowfish');
        private $_key=NULL;
        private $_register=NULL;
        private $_fields=array();

        public function __construct() {
            if (!defined('URL')):
                define('URL','');
            endif;
            Oraculum::Load('Crypt');
            Oraculum::Load('HTTP');
            Oraculum::Load('Request');
            $this->_logoffurl=URL.'logoff';
            $this->_loginurl=URL.'login';
        }
        public function setsess($sess) {
            $this->_sessname=$sess;
        }
        public function getsess() {
            return $this->_sessname;
        }
        public function setlogoffurl($url) {
            $this->_logoffurl=$url;
        }
        public function setloginurl($url) {
            $this->_loginurl=$url;
        }
        public function sethomeurl($url) {
            $this->_homeurl=$url;
        }
        public function verify($redirect=FALSE) {
            Oraculum_Request::init_sess();
            $user=Oraculum_Request::sess($this->_sessname);
            if(!is_null($user)):
                $ip=Oraculum_Crypt::strdcrypt($user['ip']);
                if($ip!=Oraculum_HTTP::ip()):
                    if ($redirect):
                        Oraculum_HTTP::redirect($this->_logoffurl);
                    endif;
                    return false;
                else:
                    return true;
                endif;
            else:
                if($redirect):
                    Oraculum_HTTP::redirect($this->_logoffurl);
                endif;
                return false;
            endif;
        }

        public static function logoff() {
            Oraculum_Request::init_sess();
            session_unset();
            $_SESSION=array();
            session_destroy();
            session_regenerate_id(true);
            Oraculum_HTTP::redirect(URL);
            exit;
        }

        /* DB Autentication */
        public function setDbAutentication($dbobj=NULL) {
            if(!is_null($dbobj)):
                $this->_dbobj=$dbobj;
            endif;
        }
        public function setDbKeyField($keyfield=NULL) {
            if(!is_null($keyfield)):
                $this->_keyfield=$keyfield;
            endif;
        }
        public function setDbUserField($userfield=NULL) {
            if(!is_null($userfield)):
                $this->_userfield=$userfield;
            endif;
        }
        public function setDbPasswordField($passwordfield=NULL) {
            if(!is_null($passwordfield)):
                $this->_passwordfield=$passwordfield;
            endif;
        }
        public function setDbEmailField($emailfield=NULL) {
            if(!is_null($emailfield)):
                $this->_emailfield=$emailfield;
            endif;
        }
        public function setDbCryptkeyField($cryptkeyfield=NULL) {
            if(!is_null($cryptkeyfield)):
                $this->_cryptkeyfield=$cryptkeyfield;
            endif;
        }
        public function setUser($user=NULL) {
            if(!is_null($user)):
                $this->_user=$user;
            endif;
        }
        public function setPassword($pass=NULL) {
            if(!is_null($pass)):
                $this->_password=$pass;
            endif;
        }
        public function setEmail($email=NULL) {
            if(!is_null($email)):
                $this->_email=$email;
            endif;
        }
        public function setCryptkey($cryptkey=NULL) {
            if(!is_null($cryptkey)):
                $this->_cryptkey=$cryptkey;
            endif;
        }
        public function DbAuth(){
            if(is_object($this->_dbobj)):
                $userfield='getBy'.ucwords($this->_userfield);
                $passwordfield=$this->_passwordfield;
                $keyfield=$this->_keyfield;
                $obj=$this->_dbobj;
                $register=$obj->$userfield($this->_user);
                if (sizeof($register)==1):
                    if ($this->_crypttype=='md5'):
                        if ($register->$passwordfield==md5($this->_password)):
                            $this->_key=$register->$keyfield;
                            $this->_register=$register;
                            return true;
                        else:
                            return false;
                        endif;
                    elseif ($this->_crypttype=='sha1'):
                        if($register->$passwordfield==sha1($this->_password)):
                            $this->_key=$register->$keyfield;
                            $this->_register=$register;
                            return true;
                        else:
                            return false;
                        endif;
                    elseif ($this->_crypttype=='blowfish'):
						Oraculum::Load('Crypt');
						if (Oraculum_Crypt::blowfishcheck($this->_password, $register->$passwordfield)):
                            $this->_key=$register->$keyfield;
                            $this->_register=$register;
                            return true;
                        else:
                            return false;
                        endif;
                    else:
                        if($register->$passwordfield==$this->_password):
                            $this->_key=$register->$keyfield;
                            return true;
                        else:
                            return false;
                        endif;
                    endif;
                endif;
            else:
                throw new Exception('Para autenticacao atraves de base de dados deve ser passada uma instancia relacionada a uma entidade do banco');
            endif;
        }
        public function PasswordlessAuth($clearkey=TRUE){
            if(is_object($this->_dbobj)):
                $cryptfield=$this->_cryptkeyfield;
                $getcryptfield='getBy'.ucwords($this->_cryptkeyfield);
                $keyfield=$this->_keyfield;
                $obj=$this->_dbobj;
                $this->_register=$obj->$getcryptfield($this->_cryptkey);
                if(sizeof($this->_register)==1):
                    $this->_key=$this->_register->$keyfield;
                    $key=Oraculum_Crypt::strdcrypt($this->_cryptkey);
                    $key=explode('::', $key);
                    $time=$key[0];
                    $timeout=$key[2];
                    $auth=(time()<$time+$timeout);
                    if(($auth)&&($clearkey)):
                        $this->_register->$cryptfield=NULL;
                        $this->_register->save();
                    endif;
                    return $auth;
                else:
                    return FALSE;
                endif;
            else:
                throw new Exception('Para autenticacao atraves de base de dados deve ser passada uma instancia relacionada a uma entidade do banco');
            endif;
        }
        public function RecordFields($fields=array()) {
            if(is_array($fields)):
                $this->_fields=$fields;
            else:
                throw new Exception ('Campos que serao gravados em sessao devem ser informados em um vetor');
            endif;
        }
        public function RecordSession($redirect=FALSE) {
            if(!is_null($this->_key)):
                $obj=$this->_register;
                foreach ($this->_fields as $field):
                    //if (property_exists(get_class($obj),$field)) {
                        $user[$field]=Oraculum_Crypt::strcrypt($obj->$field);
                    //}
                endforeach;
                $user['ip']=Oraculum_Crypt::strcrypt(Oraculum_HTTP::ip());
                $user['key']=Oraculum_Crypt::strcrypt($this->_key);
                $user['user']=Oraculum_Crypt::strcrypt($this->_user);
                Oraculum_Request::init_sess();
                Oraculum_Request::setsess($this->_sessname, $user);
                if($redirect):
                    Oraculum_HTTP::redirect($this->_homeurl);
                endif;
                return true;
            else:
                return false;
            endif;
        }
        public function setCrypttype($type) {
            if(in_array($type, $this->_crypttypes)):
                $this->_crypttype=$type;
            endif;
        }
    }