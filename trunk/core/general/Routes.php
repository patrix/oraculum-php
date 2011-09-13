<?php
/**
 * Tratamento de rotas
 *
 *
 *    @filesource     $HeadURL$
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.routes
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision$
 *    @modifiedby     $LastChangedBy$
 *    @lastmodified   $Date$
 *
 */

class Oraculum_Routes
{
  private function __construct() {
  }
  public static function add($origem, $destino)
  {
    $request=Oraculum_Request::request();
    $_SERVER['REQUEST_URI']=str_replace($origem, $destino, $request);
  }
  public static function check()
  {
    $rotas='./'.CONTROL_DIR.'/routes.php';
    if (file_exists($rotas)) {
        include($rotas);
    }
    return null;
  }
}
