<?php
    class Oraculum_Calendar{
        private $_year;
        private $_month;
        private $_day;
        private $_hour;
        private $_minute;
        private $_second;
        private $_time;
        private $_daysinmonth;
        private $_weekstart;
        private $_weekend;
        private $_weekdaystart;
        private $_weekdayend;
        private $_today;
        private $_showweek=FALSE;
        private $_showweekend=TRUE;
        private $_showmonth=TRUE;
        private $_weekdays=array('D','S','T','Q','Q','S','S');
        private $_events=array();
        private $_listevents=array();
        private $_months=array('Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
        private $_divisor=' de ';
        private $_url=NULL;
        private $_domain=NULL;
        private $_timezone='America/Sao_Paulo';
        private $_timezoneetc=3;

        public function __construct() {
            $this->_year=date('Y');
            $this->_month=date('m');
            $this->_day=date('j');
            $this->_hour=date('H');
            $this->_minute=date('i');
            $this->_second=date('s');
            return $this;
        }

        public function showweek($show=TRUE) {
            $this->_showweek=$show;
        }

        public function showweekend($show=TRUE) {
            $this->_showweekend=$show;
        }

        public function showmonth($show=TRUE) {
            $this->_showmonth=$show;
        }

        public function setday($d) {
            $this->_day=$d;
        }

        public function setmonth($m) {
            $this->_month=$m;
        }

        public function setyear($y) {
            $this->_year=$y;
        }

        public function setdivisor($divisor) {
            $this->_divisor=$divisor;
        }

        public function setdomain($domain) {
            $this->_domain=$domain;
        }

        public function getdomain() {
            if (is_null($this->_domain)) {
                $this->_domain=$_SERVER['SERVER_NAME'];
            }
            return $this->_domain;
        }

        public function setmonths($months=array()) {
            $this->_months=$months;
        }

        public function setweeks($weeks=array()) {
            $this->_weeks=$weeks;
        }

        public function settimezone($timezone=array()) {
            $this->_timezone=$timezone;
        }

        public function getmonth($m=NULL) {
            if (is_null($m)) {
                return $this->_months[$this->_month-1];
            } else {
                return $this->_months[$m-1];
            }
        }

        public function getlistevents() {
            return $this->_listevents;
        }

        public function setweekdays($days=array()) {
            if (sizeof($days)==7) {
                $this->_weekdays=$days;
            }

        }
        public function seturl($url=NULL) {
            $this->_url=$url;
        }

        public function geturl($c) {
            $url=$this->_url;
            switch($c) {
                case 'py':
                    $m=$this->_month;
                    $y=$this->_year-1;
                    break;
                case 'p':
                    if ($this->_month==1) {
                        $m=12;
                        $y=$this->_year-1;
                    } else {
                        $m=$this->_month-1;
                        $y=$this->_year;
                    }
                    break;
                case 'n':
                    if ($this->_month==12) {
                        $m=1;
                        $y=$this->_year+1;
                    } else {
                        $m=$this->_month+1;
                        $y=$this->_year;
                    }
                    break;
                case 'ny':
                    $m=$this->_month;
                    $y=$this->_year+1;
                    break;
                default:
                    $m=$this->_month;
                    $y=$this->_year;

            }
            $url=str_replace('%m', $m, $url);
            $url=str_replace('%y', $y, $url);
            return $url;
        }

        public function getcontroller() {
            $this->_controller='<div class="ofcalendar-controller">';
            $this->_controller.='<ul>';
            $this->_controller.='   <li class="btn">';
            $this->_controller.='      <a href="'.$this->geturl('py').'">';
            $this->_controller.='          &lt;&lt;';
            $this->_controller.='      </a>';
            $this->_controller.='   </li>';
            $this->_controller.='   <li class="btn">';
            $this->_controller.='      <a href="'.$this->geturl('p').'">';
            $this->_controller.='          &lt;';
            $this->_controller.='      </a>';
            $this->_controller.='   </li>';
            $this->_controller.='   <li class="ofcalendar-title">';
            $this->_controller.='      '.$this->getmonth().$this->_divisor.$this->_year.'';
            $this->_controller.='   </li>';
            $this->_controller.='   <li class="btn">';
            $this->_controller.='      <a href="'.$this->geturl('n').'">';
            $this->_controller.='          &gt;';
            $this->_controller.='      </a>';
            $this->_controller.='   </li>';
            $this->_controller.='   <li class="btn">';
            $this->_controller.='      <a href="'.$this->geturl('ny').'">';
            $this->_controller.='          &gt;&gt;';
            $this->_controller.='      </a>';
            $this->_controller.='   </li>';
            $this->_controller.='</ul>';
            $this->_controller.='</div>';
            return $this->_controller;
        }

        public function addevent($desc=NULL, $startdate=array(), $enddate=array(), $code=NULL) {
            if (sizeof($startdate)==3)
                $starttime=mktime(0, 0, 0, (int)$startdate[1], (int)$startdate[2], (int)$startdate[0]);
            if (sizeof($enddate)==3) {
                $endtime=mktime(0, 0, 0, (int)$enddate[1], (int)$enddate[2], (int)$enddate[0]);
            }
            $event['desc']=$desc;
            $event['start']=isset($starttime)?$starttime:NULL;
            $event['end']=isset($endtime)?$endtime:($starttime);
            $event['code']=(!is_null($code))?$code:NULL;
            $this->_events[]=$event;
        }

        public function generate() {
            $this->_time=mktime($this->_hour, $this->_minute, $this->_second, $this->_month, $this->_day, $this->_year);
            $this->_daysinmonth=date('t',$this->_time);
            $this->_firstdaytime=mktime(0, 0, 0, $this->_month, 1, $this->_year);
            $this->_lastdaytime=mktime(24, 0, 0, $this->_month, $this->_daysinmonth, $this->_year);
            $this->_weekstart=date('W',$this->_firstdaytime);
            $this->_today=mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            if ($this->_weekstart>=52) {
                $this->_weekstart=0;
            }
            $this->_weekend=date('W',$this->_lastdaytime);
            $this->_weekdaystart=date('w',$this->_firstdaytime);
            $this->_weekdayend=date('w',$this->_lastdaytime);
            if ($this->_weekend==1) {
                $this->_weekend=53;
            }
            $calendar='';
            if ($this->_showmonth) {
                $calendar.='<h2 class="ofcalendar-title">'.$this->getmonth().$this->_divisor.$this->_year.'</h2>';
            }
            $calendar.='<table class="ofcalendar">';
            $calendar.='    <tr>';
            if ($this->_showweek) {
                $calendar.='        <th>';
                $calendar.='            *';
                $calendar.='        </th>';
            }
            foreach ($this->_weekdays as $k=>$weekday) {
                if ((($k!=0)&&($k!=6))||($this->_showweekend)) {
                    $calendar.='        <th>';
                    $calendar.='            '.$weekday;
                    $calendar.='        </th>';
                }
            }
            $calendar.='    </tr>';
            $weeks=(($this->_weekend)-($this->_weekstart));
            $day=NULL;
            $week=$this->_weekstart;
            $daytime=NULL;
            for($c=0;$c<=$weeks;$c++) {
                $calendar.='    <tr>';
                if ($this->_showweek) {
                    $calendar.='        <td>';
                    $calendar.='            '.$week;
                    $calendar.='        </td>';
                }
                foreach ($this->_weekdays as $k=>$weekday) {
                    if (($c==0)&&($k==$this->_weekdaystart)){
                        $day=1;
                        $daytime=mktime(0, 0, 0, $this->_month, $day, $this->_year);
                    }
                    if ((($k!=0)&&($k!=6))||($this->_showweekend)) {
                        if ($daytime==$this->_today) {
                            if (($k==0)||($k==6)) {
                                $calendar.='        <td class="weekend week'.$k.' today">';
                            } else {
                                $calendar.='        <td class="today">';
                            }
                        }else {
                            if (($k==0)||($k==6)) {
                                $calendar.='        <td class="weekend week'.$k.'">';
                            } else {
                                $calendar.='        <td>';
                            }
                        }
                        $calendar.='            <span class="day">';
                        $calendar.=((is_null($day))||($day>$this->_daysinmonth))?'&nbsp;':$day;
                        $calendar.='            </span>';
                        $calendar.='            <div>';
                        // Procura eventos no dia
                        foreach ($this->_events as $ke=>$event) {
                            if (($event['start']<=$daytime)&&($daytime<=$event['end'])){
                                $calendar.=$event['desc'].'<br class="events" />';
                                $this->_listevents[$event['code']]=$event;
                            }
                        }
                        $calendar.='            </div>';
                        if (in_array($daytime, $this->_events)) {
                            $calendar.='        </td>';
                        }
                        $calendar.='        </td>';
                    }
                    if (!is_null($day)) {
                        $day++;
                        $daytime=mktime(0, 0, 0, $this->_month, $day, $this->_year);
                    }
                }
                $calendar.='    </tr>';
                $week++;
            }
            $calendar.='</table>';
            return $calendar;
        }

        public function show() {
            echo $this->generate();
        }

        public function generateIcal($type='publish') {
            $search=array ('/"/',
                 '/,/',
                 '/\n/',
                 '/\r/',
                 '/:/',
                 '/;/',
                 '/\\//');
            $replace=array ('\"',
                 '\\,',
                 '\\n',
                 '',
                 '\:',
                 '\\;',
                 '\\\\');
            $ical='BEGIN:VCALENDAR'."\n";
            $ical.='X-WR-CALNAME:Agenda de Eventos (Oraculum Framework iCal Generator)'."\n";
            $ical.='PRODID:-//Oraculum//Framework iCal Generator//EN'."\n";
            $ical.='VERSION:2.0'."\n";
            $ical.='CALSCALE:GREGORIAN'."\n";
            if ($type=='request') {
                $ical.='METHOD:REQUEST';
            } else {
                $ical.='METHOD:PUBLISH'."\n";
            }
            foreach ($this->_events as $event){
                $desc=preg_replace($search, $replace, $event['desc']);
                $desc=wordwrap($desc);
                $desc=str_replace("\n","\n  ",$desc);
                $desc=strip_tags($desc);
                $ical.='BEGIN:VEVENT'."\n";
                $ical.='DTSTAMP;TZID=US-Eastern:'.date('Ymd').'T'.date('His')."\n";
                $ical.='DTSTAMP;TZID='.$this->_timezone.':'.date('Ymd').'T'.date('His')."\n";
                $ical.='DTSTART;TZID='.$this->_timezone.':'.date('Ymd',$event['start']).'T'.date('His',$event['start']+10800)."Z\n";
                $ical.='DTEND;TZID='.$this->_timezone.':'.date('Ymd',$event['end']).'T'.date('His',$event['end']+(($this->_timezoneetc)*3600))."Z\n";
                $ical.='SUMMARY:'.$desc."\n";
                $ical.='DESCRIPTION:'.$desc."\n";
                $ical.='UID:'.date('Ymd').'T'.date('His').'-'.rand().'-'.$this->getdomain()."\n";
                $ical.='CLASS:PUBLIC'."\n";
                $ical.='END:VEVENT'."\n";
            }
            $ical.='END:VCALENDAR'."\n";
            return $ical;
        }

        public function downloadIcal(){
            header('Content-Type: text/Calendar');
            header('Content-Disposition: inline; filename=calendar.ical');
            echo $this->generateIcal('request');
        }

        public static function IcalUrlEncode($code){
            $ical=base64_encode(str_rot13(str_rot13(base64_encode($code)).
                    ':'.(base64_encode(base64_encode($code)).
                    ':'.base64_encode(str_rot13($code)))));
            return $ical;
        }

        public static function IcalUrlDecode($code){
            $code=explode(':',str_rot13(base64_decode($code)));
            $c1=base64_decode(str_rot13($code[0]));
            $c2=base64_decode(base64_decode($code[1]));
            $c3=str_rot13(base64_decode($code[2]));
            if (($c1!=$c2)||($c2!=$c3)) {
                return false;
            } else {
                return $c1;
            }
        }
    }