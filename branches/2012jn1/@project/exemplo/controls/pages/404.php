<?php
    header('HTTP/1.1 404 Not Found');
	Oraculum_WebApp::LoadView()
 	    ->AddTemplate('geral')
 	    ->LoadPage('404');
