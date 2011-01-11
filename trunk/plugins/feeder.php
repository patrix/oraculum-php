<?php
class Oraculum_Feeder{
    private $_title=NULL;
    private $_description=NULL;
    private $_link=NULL;
    private $_language=NULL;
    private $_charset='utf-8';
    private $_version='2.0';
    private $_itens=array();

    public function __construct($title=NULL, $link=NULL, $description=NULL, $language=NULL, $charset=NULL, $version=NULL) {
        if (!is_null($title))
            $this->_title=$title;
        if (!is_null($link))
            $this->_link=$link;
        if (!is_null($description))
            $this->_description=$description;
        if (!is_null($charset))
            $this->_charset=$charset;
        if (!is_null($language))
            $this->_language=$language;
        if (!is_null($version))
            $this->_version=$version;
    }

    public function generate() {
        $rss='<'.'?xml version="1.0" encoding="'.$this->_charset.'" ?'.'>'."\n";
        $rss.='<rss version="'.$this->_version.'" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
        $rss.='<channel>'."\n";
        $rss.='<atom:link href="'.$this->_link.'" rel="self" type="application/rss+xml" />'."\n";
        $rss.='<title>'.$this->_title.'</title>'."\n";
        $rss.='<description>'.$this->_description.'</description>'."\n";
        $rss.='<link>'.$this->_link.'</link>'."\n";
        $rss.='<language>'.$this->_language.'</language>'."\n";
        foreach ($this->_itens as $item) {
            //$dtrfc=$dia.' ' .$mes2.' '.$ano.' '.$hora.' GMT';
            $rss.='<item>'."\n";
            $rss.='<title>'.$item[1].'</title>'."\n";
            $rss.='<description>'.$item[2].'</description>'."\n";
            $rss.='<published>'.date('r', strtotime($item[3])).'</published>'."\n";
            $rss.='<link>'.$item[0].'</link>'."\n";
            $rss.='<guid>'.$item[0].'</guid>'."\n";
            $rss.='</item>'."\n";
        }
        $rss.='</channel>'."\n";
        $rss.='</rss>'."\n";
        return $rss;
    }

    public function addItem($link=NULL, $title=NULL, $description=NULL, $date=NULL){
        $values=array($link, htmlspecialchars(html_entity_decode(strip_tags($title))), htmlspecialchars(html_entity_decode(strip_tags($description))), $date);
        $this->_itens[]=$values;
    }
}