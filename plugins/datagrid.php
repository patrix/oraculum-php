<?php
  class Oraculum_Datagrid {
    private $_table=array();
    private $_grid=NULL;
    private $_showactions=true;
    private $_actions=array('delete', 'update');
    private $_actionstitle='Actions';
    private $_deleteurl='delete/%id%';
    private $_updateurl='update/%id%';
    private $_aditionaldeletelink='';
    private $_aditionalupdatelink='';
    private $_tableclass='';
    private $_updateclass='';
    private $_deleteclass='';
    private $_updatelabel='Update';
    private $_deletelabel='Delete';
    private $_adicionalactionhtml='';
    private $_norecordsfound='No Records Found';
    private $_fields=array();
    private $_headers=array();
    
    public function __construct($table=array()) {
      $this->_table=$table;
    }
    public function generate() {
      if (sizeof($this->_table)>0):
        foreach ($this->_table as $reg):
          $id=NULL;
          if (is_object($reg))
            $reg=$reg->getFieldList();
          if (is_array($reg)):
            if (is_null($this->_grid)):
              $this->_grid.='<table class="'.$this->_tableclass.'">';
              $this->_grid.='<thead>';
              $this->_grid.='<tr>';
              if (sizeof($this->_headers)>0):
                  foreach ($this->_headers as $header):          
                    $this->_grid.='<th>';
                    $this->_grid.=$header;
                    $this->_grid.='</th>';
                  endforeach;
              else:
                  foreach ($reg as $field=>$value):          
                    $this->_grid.='<th>';
                    $this->_grid.=ucwords($field);
                    $this->_grid.='</th>';
                  endforeach;
              endif;
              if ($this->_showactions):
                $this->_grid.='<th>';
                $this->_grid.=$this->_actionstitle;
                $this->_grid.='</th>';
              endif;
              $this->_grid.='</tr>';
              $this->_grid.='</thead>';
              $this->_grid.='<tbody>';
            endif;
            $this->_grid.='<tr>';
            
            if (sizeof($this->_fields)>0):
                foreach ($this->_fields as $field):
                    $this->_grid.='<td>';
                    $this->_grid.=$reg[$field];
                    $this->_grid.='</td>';
                endforeach;
            else:
                foreach ($reg as $field=>$value):
                    $id=(is_null($id))?$value:$id;
                    $this->_grid.='<td>';
                    $this->_grid.=$value;
                    $this->_grid.='</td>';
                endforeach;
            endif;
            if ($this->_showactions):
              $this->_grid.='<td>';
              if (in_array('update', $this->_actions))
                $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_updateurl).'" class="'.$this->_updateclass.'" '.$this->_aditionalupdatelink.'>'.$this->_updatelabel.'</a> ';
              if (in_array('delete', $this->_actions))
                $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_deleteurl).'" class="'.$this->_deleteclass.'" '.$this->_aditionaldeletelink.'>'.$this->_deletelabel.'</a>';
              $this->_grid.=str_replace('%id%', $id, $this->_adicionalactionhtml);
              $this->_grid.='</td>';
            endif;
            $this->_grid.='</tr>';
          endif;
        endforeach;
        $this->_grid.='</tbody>';
        $this->_grid.='</table>';
      else:
        $this->_grid=$this->_norecordsfound;
      endif;
      return $this->_grid;        
    }
    public function setTableClass($class) {
      $this->_tableclass=$class;
    }
    public function setUpdateClass($class) {
      $this->_updateclass=$class;
    }
    public function setDeleteClass($class) {
      $this->_deleteclass=$class;
    }
    public function setDeleteUrl($url) {
      $this->_deleteurl=$url;
    }
    public function setUpdateUrl($url) {
      $this->_updateurl=$url;
    }
    public function setAditionalHTMLDeleteLink($html) {
      $this->_aditionaldeletelink=$html;
    }
    public function setAditionalHTMLUpdateLink($html) {
      $this->_aditionalupdatelink=$html;
    }
    public function setDeleteLabel($label) {
      $this->_deletelabel=$label;
    }
    public function setUpdateLabel($label) {
      $this->_updatelabel=$label;
    }
    public function setNoRecordsFound($text) {
      $this->_norecordsfound=$text;
    }
    public function setAdictionalActionHTML($html) {
      $this->_adicionalactionhtml=$html;
    }
    public function setShowActions($showactions) {
      $this->_showactions=(bool)$showactions;
    }
    public function setHeaders($headers) {
      $this->_headers=$headers;
    }
    public function setFields($fields) {
      $this->_fields=$fields;
    }
  }