<?php
// In development
//debug_print_backtrace();

class Oraculum_Exception extends Exception
{
    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return "Code: " . $this->getCode() . "<br />Message: " . htmlentities($this->getMessage());
    }

    public function getException()
    {
        print $this; // This will print the return from the above method __toString()
    }

    public static function getStaticException($exception)
    {
         $exception->getException(); // $exception is an instance of this class
    }

    public static function showException($e)
    {
        $filecode=NULL;
        $message=$e->getMessage();
        $code=$e->getCode();
        $file=$e->getFile();
        $line=$e->getLine();
        $trace=$e->getTrace();
        $traceasstring=$e->getTraceAsString();
        $report='<h1>'.$message.'</h1>';
        //$report='<h2>Report for PHP Error #'.$code.'</h2>';
        $report.='<strong>Error Code:</strong> #'.$code.'<br />';
        $report.='<strong>Source File:</strong> '.$file.' <strong>line '.$line.'</strong><br />';
        $report.='<h3>Backtrace</h3>';
        foreach ($trace as $error) {
            $filecode=$error['file'];
            $report.='<hr />';
            $report.='<strong>File:</strong> '.$error['file'].' <strong>line '.$error['line'].'</strong><br />';


            $args=array();
            if ($error['args']) {
                foreach ($error['args'] as $arg) {
                    if (is_object($arg)) {
                        $args[] = get_class($arg). ' object';
                    } else if (is_array($arg)) {
                        $args[] = implode(',', $arg);
                    } else {
                        $args[] = (string) $arg;
                    }
                }
            }

            if (isset($error['class'])) {
                $report.='<i><font color=green>'.$error['class'].'</font>';
            }
            if (isset($error['type'])) {
                $report.='<font color=olive>'.$error['type'].'</font>';
            }
            $report.='<font color=darkblue>'.$error['function'].'</font>';
            $report.='(<font color=maroon>'.implode(',', $args).'</font>);</i>';
            
            $cod='sourcecodedebug'.time().rand();
            $report.='<br /><a href="#alert"'.$cod.'" onclick="document.getElementById(\''.$cod.'\').style.display=\'block\';" style="color:#00a;">';
            $report.='Show Source Code</a>';
            $report.='<div id="'.$cod.'" style="display:none;border:1px solid #444;background-color:#fff; word-wrap:break-word;">'.highlight_file($filecode, true).'</div><br />';
        }
        $report='<div style=\'float:left;text-align:left;\'>'.$report.'</div>';
        alert($report);
    }
    public static function showError($code=NULL,$message=NULL,$file=NULL,$line=NULL,$context=null)
    {
        $filecode=NULL;
        $report='<h1>'.$message.'</h1>';
        $report.='<strong>Error Code:</strong> #'.$code.'<br />';
        $report.='<strong>Source File:</strong> '.$file.' <strong>line '.$line.'</strong><br />';
        $report.='<h3>Backtrace</h3>';
        //var_dump($context);
        /*foreach ($context as $error) {
            $filecode=$error['file'];
            $report.='<hr />';
            $report.='<strong>File:</strong> '.$error['file'].' <strong>line '.$error['line'].'</strong><br />';
            $args=array();
            if ($error['args']) {
                foreach ($error['args'] as $arg) {
                    if (is_object($arg)) {
                        $args[] = get_class($arg). ' object';
                    } else if (is_array($arg)) {
                        $args[] = implode(',', $arg);
                    } else {
                        $args[] = (string) $arg;
                    }
                }
            }

            if (isset($error['class'])) {
                $report.='<i><font color=green>'.$error['class'].'</font>';
            }
            if (isset($error['type'])) {
                $report.='<font color=olive>'.$error['type'].'</font>';
            }
            $report.='<font color=darkblue>'.$error['function'].'</font>';
            $report.='(<font color=maroon>'.implode(',', $args).'</font>);</i>';

            $cod='sourcecodedebug'.time().rand();
            $report.='<br /><a href="#alert"'.$cod.'" onclick="document.getElementById(\''.$cod.'\').style.display=\'block\';" style="color:#00a;">';
            $report.='Show Source Code</a>';
            $report.='<div id="'.$cod.'" style="display:none;border:1px solid #444;background-color:#fff; word-wrap:break-word;">'.highlight_file($filecode, true).'</div><br />';
        }*/
        $report='<div style=\'float:left;text-align:left;\'>'.$report.'</div>';
        alert($report);
    }
}
set_exception_handler(array('Oraculum_Exception', 'showException'));

set_error_handler(array('Oraculum_Exception', 'showError'));