<?php
/**
 * Tratamento de Views
 *
 *
 *    @filesource     $HeadURL: $
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.controls
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision:  $
 *    @modifiedby     $LastChangedBy: Patrick $
 *    @lastmodified   $Date:  $
 *
 */

class Oraculum_Controls
{
	public function __construct() {
		if (!defined('CONTROL_DIR')) {
			define('CONTROL_DIR', 'controls');
		}
		if (!defined('ERRORPAGE')) {
			define('ERRORPAGE', '404');
		}

	}

  public function LoadPage($page=NULL, $url=NULL, $usetemplate=false)
  {
  	if (is_null($page)) {
  		throw new Exception ('[Erro CGC33] Pagina nao informada');
  	} else {
  		$pagefile=CONTROL_DIR.'/pages/'.$page.'.php';
  		$urlfile=CONTROL_DIR.'/pages/'.$url.'.php';
  		$errorpage=CONTROL_DIR.'/pages/'.ERRORPAGE.'.php';
                if ($page=='') {
                    $class=ucwords($url).'Controller';
                } else {
                    $class=ucwords($page).'Controller';
                }
                if (file_exists($urlfile)) {
                        include_once($urlfile);
                } elseif (file_exists($pagefile)) {
                        include_once($pagefile);
                } elseif(file_exists($errorpage)) {
                        //header('HTTP/1.1 404 Not Found');
                        include_once($errorpage);
                } else {
                        header('HTTP/1.1 404 Not Found');
                        throw new Exception('[Erro CGC48] Pagina nao encontrada ('.$pagefile.') ');
                }
                if (class_exists($class)) {
                    new $class;
                }
  	}
  	return $this;
  }
}
