<?php
class Oraculum_Pager {
    private $_total=NULL;
    private $_results=10;
    private $_url=NULL;
    private $_reverse=FALSE;
    private $_page=1;
    private $_pages=1;
    private $_template='<a href="{%url}">{%page}</a>';
    private $_selectedtemplate='<span>{%page}</span>';
    private $_range=5;
    private $_firstpage=1;
    private $_lastpage=NULL;
    private $_previouspage=NULL;
    private $_nextpage=NULL;
    private $_needpagination=TRUE;

    private $_previousbtn='<a href="{%url}">&lt;</a>';
    private $_nextbtn='<a href="{%url}">&gt;</a>';
    private $_lastbtn='<a href="{%url}">&gt;&gt;</a>';
    private $_firstbtn='<a href="{%url}">&lt;&lt;</a>';
    private $_previousbtnoff='<span class="disabled">&lt;</span>';
    private $_nextbtnoff='<span class="disabled">&gt;</span>';
    private $_lastbtnoff='<span class="disabled">&gt;&gt;</span>';
    private $_firstbtnoff='<span class="disabled">&lt;&lt;</span>';

    public function __construct() {
    }
    public function setPage($page=1) {
        $this->_page=(int)$page;
        return $this;
    }
    public function getPage() {
        return $this->_page;
    }
    public function getPages() {
        $this->_pages=ceil((int)$this->_total/(int)$this->_results);
    }
    public function getPreviousPage() {
        if (is_null($this->_previouspage)) {
            $this->_previouspage=$this->_page>1?($this->_page-1):NULL;
        }
        return $this->_previouspage;
    }
    public function getNextPage() {
        if (is_null($this->_nextpage)) {
            $this->_nextpage=$this->_page<$this->_pages?($this->_page+1):NULL;
        }
        return $this->_nextpage;
    }
    public function getLastPage() {
        $this->_lastpage=$this->_pages;
        return (int)$this->_lastpage;
    }
    public function getFirstPage() {
        $this->_firstpage=1;
        return (int)$this->_firstpage;
    }
    public function ifNeedPagination() {
        if ((!is_null($this->_total))&&(!is_null($this->_results))) {
            $this->_needpagination=(bool)((int)$this->_total>(int)$this->_results);
        } else {
            $this->_needpagination=FALSE;
        }
        return $this;
    }
    public function setTotal($total=0) {
        $this->_total=(int)$total;
        return $this;
    }
    public function setResults($results=10) {
        $this->_results=(int)$results;
        return $this;
    }
    public function setRange($range=5) {
        $this->_range=(int)$range;
        return $this;
    }
    public function setTemplate($template='<a href="{%url}">{%page}</a>') {
        $this->_template=$template;
    }

    public function setSelectedTemplate($template='<span>{%page}</span>') {
        $this->_selectedtemplate=$template;
    }
    public function setPreviousButton($template='<a href="{%url}">&lt;</a>') {
        $this->_previousbtn=$template;
    }
    public function setNextButton($template='<a href="{%url}">&gt;</a>') {
        $this->_nextbtn=$template;
    }
    public function setFirstButton($template='<a href="{%url}">&lt;&lt;</a>') {
        $this->_firstbtn=$template;
    }
    public function setLastButton($template='<a href="{%url}">&gt;&gt;</a>') {
        $this->_lastbtn=$template;
    }

    public function setPreviousButtonOff($template='<a href="{%url}">&lt;</a>') {
        $this->_previousbtnoff=$template;
    }
    public function setNextButtonOff($template='<a href="{%url}">&gt;</a>') {
        $this->_nextbtnoff=$template;
    }
    public function setFirstButtonOff($template='<a href="{%url}">&lt;&lt;</a>') {
        $this->_firstbtnoff=$template;
    }
    public function setLastButtonOff($template='<a href="{%url}">&gt;&gt;</a>') {
        $this->_lastbtnoff=$template;
    }
    public function setUrl($url) {
        $this->_url=$url;
    }
    public function pager() {
        if ($this->_needpagination) {
            $this->getPages(); // Set $this->_pages to the number of pages
            $pager=array();
            $t=array('{%url}' => $this->_url.$this->getFirstPage());
            if ($this->_page!=$this->_firstpage) {
                $pager[]=strtr($this->_firstbtn, $t);
            } else {
                $pager[]=strtr($this->_firstbtnoff, $t);
            }

            $t=array('{%url}' => $this->_url.$this->getPreviousPage());
            if (($this->_page!=$this->_previouspage)&&(!is_null($this->_previouspage))) {
                $pager[]=strtr($this->_previousbtn, $t);
            } else {
                $pager[]=strtr($this->_previousbtnoff, $t);
            }
            for($page=1;$page<=($this->_pages);$page++) {
                $trans=array('{%page}' => $page, '{%url}' => $this->_url.$page);
                if (($this->_pages>=$this->_range)&&($page>=($this->_page-$this->_range))&&($page<=($this->_page+$this->_range))) {
                    if ($page==$this->_page)
                        $pager[]=strtr($this->_selectedtemplate, $trans);
                    else
                        $pager[]=strtr($this->_template, $trans);
                }
            }


            $t=array('{%url}' => $this->_url.$this->getNextPage());
            if (($this->_page!=$this->_nextpage)&&(!is_null($this->_nextpage))) {
                $pager[]=strtr($this->_nextbtn, $t);
            } else {
                $pager[]=strtr($this->_nextbtnoff, $t);
            }

            $t=array('{%url}' => $this->_url.$this->getLastPage());
            if (($this->_page!=$this->_lastpage)) {
                $pager[]=strtr($this->_lastbtn, $t);
            } else {
                $pager[]=strtr($this->_lastbtnoff, $t);
            }

            if ($this->_reverse) {
                $pager=array_reverse($pager);
            }
            $pager=implode('', $pager);
            return $pager;
        } else {
            return NULL;
        }

    }
    public function reverse() {
        $this->_reverse=!(bool)$this->_reverse;
        return $this;
    }
}