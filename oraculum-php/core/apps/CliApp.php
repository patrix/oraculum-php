<?php
	Oraculum::LoadContainer('App');
	class Oraculum_CliApp extends Oraculum_App
	{
            public function __construct(){
                if(!defined('STDIN')) {
                    //throw new Exception('Aplicacao deve ser executada apenas por linha de comando');
                }
            }

	}
