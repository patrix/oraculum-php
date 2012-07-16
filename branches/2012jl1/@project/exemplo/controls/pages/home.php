<?php
  Oraculum_Register::set('titulo', 'Home');
	Oraculum_WebApp::LoadView()
 	    ->AddTemplate('geral')
 	    ->LoadPage('home');
