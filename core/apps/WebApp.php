<?php
	Oraculum::LoadContainer('App');
	class Oraculum_WebApp extends Oraculum_App
	{
            public function __construct(){
                header('X-Powered-By: Oraculum PHP Framework');
				Oraculum_App::checkDebug();
            }
	}
