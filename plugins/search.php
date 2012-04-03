<?php

class Oraculum_Search extends DBO {

    private $config = NULL;

    public function __construct() {
        
    }

    public function config($conf) {
        if (is_array($conf)) {
            $this->config = $conf;
        } else {
            throw new Exception('[Erro] A variável de configuração tem que ser um Array[tabela][campos]=campo1,campo2');
        }
    }

    public function search($param) {
        $i = 0;
        $config = $this->config;
        while (list($key, $val) = each($this->config)) {
            $aux = "";
            $campos = explode(',', $config[$key]['campos']);
            for ($j = 0; $j < sizeof($campos); $j++) {
                $aux .= $campos[$j] . " like '%$param%'";
                if (($j + 1) < sizeof($campos)) {
                    $aux .= " OR ";
                }
            }
            echo $sql[$i] = "select * from $key where $aux" . '<br/>';
            $i++;
        }
    }

}