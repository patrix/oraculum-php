<?php
    ini_set('display_errors',false);
    /*
      Utilizar as linhas comentadas apenas em ambiente de desenvolvimento
        error_reporting(E_ALL|E_STRICT);
        ini_set('display_errors',true);
    */
    define('DS', DIRECTORY_SEPARATOR);
    /*
        Definindo diretorio do framework ../libraries/OF/
    */
    define('PATH', getcwd().DS.'..'.DS.'libraries'.DS.'OF'.DS);
    /*
        A linha abaixo foi comentada por que foi identificado problemas em sistemas Windows
        ini_set('include_path', PATH.'::'.dirname(__FILE__));
    */
    ini_set('include_path', PATH);
    date_default_timezone_set('America/Sao_Paulo');
    include('Oraculum.php');
    Oraculum::Load('Register');
    /*
        Utilizar apenas em ambiente de desenvolvimento
        Oraculum::Load('Exceptions');
    */