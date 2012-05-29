<?php
    include('./bootstrap.php'); // Carrega arquivo de inicializacao
    Oraculum::LoadContainer('WebApp');
    $app=new Oraculum_WebApp();
    $app->FrontController()
            ->setBaseUrl('/oraculum/exemplo/') // Define qual a URL base
            ->setDefaultPage('home') // Define qual a pagina padrao
            ->setErrorPage('404') // Define qual a pagina de erro
            ->start();
