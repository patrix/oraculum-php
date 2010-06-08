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
	}

  public function LoadPage($page=NULL, $usetemplate=false)
  {
  	if (is_null($page)) {
  		throw new Exception ('[Erro CGC30] Pagina nao informada');
  	} else {
  		$pagefile=CONTROL_DIR.'/pages/'.$page.'.php';
  		$errorpage=CONTROL_DIR.'/pages/'.ERRORPAGE.'.php';
				if (file_exists($pagefile)) {
					include_once($pagefile);
				} elseif(file_exists($errorpage)) {
                                        header("HTTP/1.1 404 Not Found");
					include_once($errorpage);
				} else {
					throw new Exception('[Erro CGC37] Pagina nao encontrada ('.$pagefile.') ');
				}
  	}
  	return $this;
  }
}
