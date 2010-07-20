<?php
Oraculum::Load('Request');
class Oraculum_FrontController
{
	private $_defaulturl=NULL;
	private $_errorpage=NULL;

	public function setBaseUrl($url) {
		if (!defined('URL')) {
			define('URL', $url);
			$gets=Oraculum_Request::gets();
			$base=(count(explode("/", URL))-2);
			$base=strpos($gets[$base], '.php')?$base+2:$base;
			define("BASE", $base);
		}
		return $this;
	}

	public function setDefaultPage($url) {
		$this->_defaulturl=$url;
		return $this;
	}

	public function setErrorPage($url) {
		if (!defined('ERRORPAGE')) {
			define('ERRORPAGE', $url);
		}
		return $this;
	}

	public function start() {
		Oraculum::Load('Request');
		$request=Oraculum_Request::request();
		$url=str_ireplace(URL, '', $request);
		$gets=Oraculum_Request::gets();
		if (isset($gets[(BASE)+1])) {
			$page=$gets[(BASE)+1];
		} else {
			$page=$this->_defaulturl;
			//throw new Exception('[Erro CGFC36] Nao foi possivel determinar a pagina atraves da URL');
		}
		if ($url=='') {
			$url=$this->_defaulturl;
		}
		Oraculum_App::LoadControl()->LoadPage($page, $url);
	}
}