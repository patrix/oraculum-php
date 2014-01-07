<?php
	Oraculum_Register::set('titulo', 'Primeiros Passos');
	Oraculum_WebApp::LoadView()
 	    ->AddTemplate('geral')
 	    ->LoadPage('primeiros-passos');
