<?php
/**
 * Captcha
 *
 *    @filesource     $HeadURL$
 *    @category       Components
 *    @package        oraculum
 *    @subpackage     oraculum.components.captcha
 *    @version        $Revision$
 *    @modifiedby     $LastChangedBy$
 *    @lastmodified   $Date$
 */
class Oraculum_Captcha
{
    public function __construct($type=1,$size=6) {
        Oraculum_Request::init_sess();
        if (is_null(Oraculum_Request::sess("captcha"))) {
            if ($type==3) {
                $letters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $max=61;
            } elseif ($type==2) {
                $letters='abcdefghijklmnopqrstuvwxyz0123456789';
                $max=35;
            } else {
                $letters='0123456789';
                $max=9;
            }
            $string=NULL;
            for($i=0;$i<$size;$i++) {
                $string.=$letters{mt_rand(0, $max)};
            }
            Oraculum_Request::setsess("captcha", $string);
        } else {
            $string=Oraculum_Request::sess("captcha");
        }
        $img=imagecreate(((10)*$size), 25);
        $backcolor=imagecolorallocate($img, 255, 255, 255);
        $textcolor=imagecolorallocate($img, 000, 000, 000);
        imagefill($img, 0, 0, $backcolor);
        imagestring($img, 10, 0, 5, $string, $textcolor);
        header("Content-type: image/jpeg");
        imagejpeg($img);
    }
}