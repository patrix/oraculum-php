<?php
    /*
      Utilizar OF_DEBUG true apenas em ambiente de desenvolvimento
	  pois habilita a exibição de todos os erros e alertas (E_STRING)
    */
	define('OF_DEBUG', false);
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
    Oraculum::Load('Request');
    Oraculum::Load('Alias');
	Oraculum_Alias::LoadAlias('Request');
    /*
        Utilizar apenas em ambiente de desenvolvimento
        Oraculum::Load('Exceptions');
    */