<?php
    class Oraculum_PasswordGenerator{
        private $_password=NULL;

        public function __construct($type=1, $size=6) {
            if ($type==3) {
                $letters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $max=61;
            } elseif ($type==2) {
                $letters='abcdefghijklmnopqrstuvwxyz0123456789';
                $max=35;
            } else {
                $letters='0123456789';
                $max=9;
            }
            $string=NULL;
            for($i=0;$i<$size;$i++) {
                $string.=$letters{mt_rand(0, $max)};
            }
            $this->_password=$string;
            return $this->_password;
        }
        public function __toString() {
            return $this->_password;
        }
    }