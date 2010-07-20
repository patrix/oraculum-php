<?php
/**
 * Componente de Envio de E-mails
 *
 *
 *    @filesource
 *    @category       Components
 *    @package        oraculum
 *    @subpackage     oraculum.components.sendmail
 *    @required       PHPMailer 2.3 ou superior
 */
class Oraculum_Mail
{
    private $_host=NULL;
    private $_user=NULL;
    private $_password=NULL;

    public function __construct($host=NULL, $user=NULL, $password=NULL) {
        $this->_host=$host;
        $this->_user=$user;
        $this->_password=$password;
    }

    public function sendmail($tos=array(),$subject=null,$msg=null,$from=null,$fromname=null,$replyto=null,$bcc=null)
    {
        if (!is_null($msg)) {
            $text="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" ";
            $text.="\"http://www.w3.org/TR/html4/loose.dtd\">\n";
            $text.="<html>\n";
            $text.="  <head>\n";
            $text.="    <title>\n";
            $text.="      ".$subject."\n";
            $text.="    </title>\n";
            $text.="    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
            $text.="  </head>\n";
            $text.="  <body>\n";
            $text.="  <div style=\"margin: auto; text-align: left; width: 75%; background-color: #fff; ";
            $text.="border: 0px solid #777; margin-top: 5px; padding: 15px; ";
            $text.="font: 15px Verdana, Arial, Helvetica, sans-serif; color: #444;\">\n";
            $text.="    <h3 style=\"border-bottom: 1px solid #000;\">";
            $text.="      ".$subject.":";
            $text.="    </h3>";
            $text.="    <div style=\"text-align: justify; line-height:150%; margin: 25px; ";
            $text.="margin-top: 5px; margin-bottom: 5px;\">";
            $text.=$msg;
            $text.="      <br />";
            $text.="    </div>";
            $text.="  </body>\n";
            $text.="</html>";
            require_once("plugins/phpmailer/class.phpmailer.php");
            $mail=new PHPMailer();
            $mail->SetLanguage("br");
            $mail->CharSet="utf-8";
            $mail->WordWrap=50; // Definicao de quebra de linha
            $mail->IsSMTP(); // send via SMTP
            $mail->SMTPAuth=true; // Habilitando a autenticacao
            //$mail->SMTPSecure="ssl"; // Definindo modo SSL
            $mail->Host=$this->_host; //seu servidor SMTP
            $mail->Username=$this->_user; // Usuario Autenticador
            $mail->Password=$this->_password; // Senha do usuario (nao usar ou divulgar).
            //$mail->IsMail();
            $mail->SMTPDebug=0;
            //$mail->Port=587;
            $mail->IsHTML(true);
            // Destino
            foreach ($tos as $to) {
              $mail->AddAddress($to);
            }
            $mail->From=$from;
            $mail->FromName=$fromname;
            $mail->AddReplyTo($replyto); // Responder para
            $mail->Subject=$subject;
            $mail->Body=$text;
            $mail->AltBody=strip_tags($text);
            if (!is_null($bcc)) {
                $bcc=str_replace(";",",",$bcc);
                $bcc=explode(",", $bcc);
                foreach ($bcc as $copy) {
                   $copy=trim($copy);
                   $mail->AddBCC($copy);
                }
            }
            return $mail->Send();
        } else {
            return FALSE;
        }
    }
}