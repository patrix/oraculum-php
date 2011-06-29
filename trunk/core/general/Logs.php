<?php
/**
 * Tratamento de parametros HTTP
 *
 *
 *    @filesource     $HeadURL:  $
 *    @category       Framework
 *    @package        oraculum
 *    @subpackage     oraculum.core.http
 *    @license        http://www.opensource.org/licenses/lgpl-3.0.html (LGPLv3)
 *    @version        $Revision: $
 *    @modifiedby     $LastChangedBy: Patrick $
 *    @lastmodified   $Date: 2011-06-21 16:11:56 -0300 (Ter, 21 Jun 2011) $
 *
 */

class Oraculum_Logs
{
  // Funcao de exibicao de mensagens de erro
  public static function alert($mensagem, $log=false)
  {
	    $cod=time().rand();
	    echo "<div id=\"alert".$cod."\" style=\"";
	    echo "border:2px solid #456abc; background-color:#ffffe7; color:#000000; ";
	    echo "margin:auto; width:75%; margin-top:10px; text-align:center; ";
	    echo "padding:10px; padding-top:0px; font-family: 'Times New Roman', ";
	    echo "serif; font-style:italic;\">\n";
	    echo "<div style=\"float:right; width:100%; text-align:right; ";
	    echo "clear:both;\">\n";
	    echo "<a href=\"#alert".$cod."\" onclick=\"";
	    echo "document.getElementById('alert".$cod."').style.display='none';\" ";
	    echo "style=\"color: #aa0000; font-size: 1em; text-decoration: none;\" ";
	    echo "title=\"Fechar\">x</a></div>";
	    echo "\n".utf8_decode($mensagem)."\n";
	    echo "</div>\n";
  }

	/*
	 * função showException
	 * exibe a pilha de error
	 * de uma exceção
	 */
    public static function showException($e)
    {
        $errorarray=$e->getTrace();
        echo '<table border=1>';
        echo '<tr>';
        echo '<td><b>General Error: '.$e->getMessage().'<br></td>';
        echo '</tr>';
        foreach ($errorarray as $error) {
            echo '<tr>';
            echo '<td>';
            echo 'File: '.$error['file'].' : '.$error['line'];
            echo '</td>';
            $args = array();
            if ($error['args']) {
                foreach ($error['args'] as $arg) {
                    if (is_object($arg)) {
                        $args[]=get_class($arg).' object';
                    } else if (is_array($arg)) {
                        $args[]=implode(',', $arg);
                    } else {
                        $args[]=(string)$arg;
                    }
                }
            }
            echo '<tr>';
            echo '<td>';
            echo '&nbsp;&nbsp;<i>'.'<font color=green>'.$error['class'].'</font>'.
                 '<font color=olive>'.$error['type'].'</font>'.
                 '<font color=darkblue>'.$error['function'].'</font>'.
                 '('.'<font color=maroon>'.implode(',', $args).'</font>'.')</i>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
