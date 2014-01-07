<?php
    ini_set('display_errors',true);
    /*
      Utilizar as linhas comentadas apenas em ambiente de desenvolvimento
        error_reporting(E_ALL|E_STRICT);
        ini_set('display_errors',true);
    */
	define('OF_DEBUG', true);
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
    Oraculum::Load('Models');
    Oraculum::Load('DBO');
    Oraculum::Load('ActiveRecord');
    Oraculum::Load('Alias');
    Oraculum::Load('Controls');
    Oraculum::Load('Crypt');
    Oraculum::Load('Exceptions');
    Oraculum::Load('Files');
    Oraculum::Load('FrontController');
    Oraculum::Load('HTTP');
    Oraculum::Load('Logs');
    Oraculum::Load('Plugins');
    Oraculum::Load('Register');
    Oraculum::Load('Request');
    Oraculum::Load('Routes');
    Oraculum::Load('Security');
    Oraculum::Load('Test');
    Oraculum::Load('Text');
    Oraculum::Load('Validate');
    Oraculum::Load('Views');
	Oraculum_Alias::LoadAlias('All');
    /*
        Utilizar apenas em ambiente de desenvolvimento
        Oraculum::Load('Exceptions');
    */