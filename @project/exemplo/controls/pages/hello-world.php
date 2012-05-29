<?php
  Oraculum_Register::set('titulo', 'Hello World');
	Oraculum_WebApp::LoadView()
 	    ->AddTemplate('geral')
 	    ->LoadPage('hello-world');