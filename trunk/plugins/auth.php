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
        private $_crypttypes=array('md5','sha1');
        private $_key=NULL;
        private $_register=NULL;
        private $_fields=array();

        public function __construct() {
            if (!defined('URL')) {
                define('URL','');
            }
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
            if (!is_null($user)) {
                $ip=Oraculum_Crypt::strdcrypt($user['ip']);
                if ($ip!=Oraculum_HTTP::ip()) {
                    if ($redirect) {
                        Oraculum_HTTP::redirect($this->_logoffurl);
                    }
                    return false;
                } else {
                    return true;
                }
            } else {
                if ($redirect) {
                    Oraculum_HTTP::redirect($this->_logoffurl);
                }
                return false;
            }
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
            if (!is_null($dbobj)) {
                $this->_dbobj=$dbobj;
            }
        }
        public function setDbKeyField($keyfield=NULL) {
            if (!is_null($keyfield)) {
                $this->_keyfield=$keyfield;
            }
        }
        public function setDbUserField($userfield=NULL) {
            if (!is_null($userfield)) {
                $this->_userfield=$userfield;
            }
        }
        public function setDbPasswordField($passwordfield=NULL) {
            if (!is_null($passwordfield)) {
                $this->_passwordfield=$passwordfield;
            }
        }
        public function setDbEmailField($emailfield=NULL) {
            if (!is_null($emailfield)) {
                $this->_emailfield=$emailfield;
            }
        }
        public function setDbCryptkeyField($cryptkeyfield=NULL) {
            if (!is_null($cryptkeyfield)) {
                $this->_cryptkeyfield=$cryptkeyfield;
            }
        }
        public function setUser($user=NULL) {
            if (!is_null($user)) {
                $this->_user=$user;
            }
        }
        public function setPassword($pass=NULL) {
            if (!is_null($pass)) {
                $this->_password=$pass;
            }
        }
        public function setEmail($email=NULL) {
            if (!is_null($email)) {
                $this->_email=$email;
            }
        }
        public function setCryptkey($cryptkey=NULL) {
            if (!is_null($cryptkey)) {
                $this->_cryptkey=$cryptkey;
            }
        }
        public function DbAuth(){
            if (is_object($this->_dbobj)) {
                $userfield='getBy'.ucwords($this->_userfield);
                $passwordfield=$this->_passwordfield;
                $keyfield=$this->_keyfield;
                $obj=$this->_dbobj;
                $register=$obj->$userfield($this->_user);
                if (sizeof($register)==1) {
                    if ($this->_crypttype=='md5') {
                        if ($register->$passwordfield==md5($this->_password)) {
                            $this->_key=$register->$keyfield;
                            $this->_register=$register;
                            return true;
                        } else {
                            return false;
                        }
                    } elseif ($this->_crypttype=='sha1') {
                        if ($register->$passwordfield==sha1($this->_password)) {
                            $this->_key=$register->$keyfield;
                            $this->_register=$register;
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        if ($register->$passwordfield==$this->_password) {
                            $this->_key=$register->$keyfield;
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                throw new Exception('Para autenticacao atraves de base de dados deve ser passada uma instancia relacionada a uma entidade do banco');
            }
        }
        public function PasswordlessAuth($clearkey=TRUE){
            if (is_object($this->_dbobj)) {
                $cryptfield=$this->_cryptkeyfield;
                $getcryptfield='getBy'.ucwords($this->_cryptkeyfield);
                $keyfield=$this->_keyfield;
                $obj=$this->_dbobj;
                $this->_register=$obj->$getcryptfield($this->_cryptkey);
                if (sizeof($this->_register)==1) {
				
                    $this->_key=$this->_register->$keyfield;
                    $key=Oraculum_Crypt::strdcrypt($this->_cryptkey);
                    $key=explode('::', $key);
                    $time=$key[0];
                    $timeout=$key[2];
                    $auth=(time()<$time+$timeout);
                    if (($auth)&&($clearkey)){
                        $this->_register->$cryptfield=NULL;
                        $this->_register->save();
                    }
                    return $auth;
                } else {
                    return FALSE;
                }
            } else {
                throw new Exception('Para autenticacao atraves de base de dados deve ser passada uma instancia relacionada a uma entidade do banco');
            }
        }
        public function RecordFields($fields=array()) {
            if (is_array($fields)) {
                $this->_fields=$fields;
            } else {
                throw new Exception ('Campos que serao gravados em sessao devem ser informados em um vetor');
            }
        }
        public function RecordSession($redirect=FALSE) {
            if (!is_null($this->_key)) {
                $obj=$this->_register;
                foreach ($this->_fields as $field) {
                    //if (property_exists(get_class($obj),$field)) {
                        $user[$field]=Oraculum_Crypt::strcrypt($obj->$field);
                    //}
                }
                $user['ip']=Oraculum_Crypt::strcrypt(Oraculum_HTTP::ip());
                $user['key']=Oraculum_Crypt::strcrypt($this->_key);
                $user['user']=Oraculum_Crypt::strcrypt($this->_user);
                Oraculum_Request::init_sess();
                Oraculum_Request::setsess($this->_sessname, $user);
                if ($redirect) {
                    Oraculum_HTTP::redirect($this->_homeurl);
                }
                return true;
            } else {
                return false;
            }
        }
        public function setCrypttype($type) {
            if (in_array($type, $this->_crypttypes)) {
                $this->_crypttype=$type;
            }   
        }
    }