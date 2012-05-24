<?php
  class Oraculum_Forms {
    private $_method='post';
    private $_action=NULL;
    private $_legend=NULL;
    private $_formclass=NULL;
    private $_required=array();
    private $_fields=array();
    private $_labels=array();
    private $_values=array();
    private $_table=array();
    private $_form=NULL;
    private $_submitvalue=NULL;
    private $_cancelvalue=NULL;
    private $_showsubmit=TRUE;
    private $_showcancel=TRUE;
    private $_submitclass=NULL;
    private $_cancelclass=NULL;
    private $_submitadictionalhtml=NULL;
    private $_canceladictionalhtml=NULL;
    
    public function __construct($table=array()) {
      $this->_table=$table;
    }
    public function generate() {
      $this->_form.='<form method="'.$this->_method.'" action="'.$this->_action.'">';
      $this->_form.='<fieldset>';
      if (!is_null($this->_legend)):
        $this->_form.='<legend>'.$this->_legend.'</legend>';
      endif;
      foreach($this->_fields as $k=>$field):
          $this->_form.='<div class="control-group">';
          $this->_form.='  <label class="control-label" for="'.$this->_fields[$k].'">'.$this->_labels[$k].' <span class="required">*</span></label>';
          $this->_form.='  <div class="controls">';
          $this->_form.='    <input type="text" class="input-xlarge" id="'.$this->_fields[$k].'" name="'.$this->_fields[$k].'" value="">';
          $this->_form.='  </div>';
          $this->_form.='</div>';
      endforeach;
      $this->_form.='</fieldset>';
      $this->_form.='</form>';
      return $this->_form;        
    }
    public function setMethod($method) {
      $this->_method = $method;
    }

    public function setAction($action) {
      $this->_action = $action;
    }

    public function setLegend($legend) {
      $this->_legend = $legend;
    }

    public function setFormclass($formclass) {
      $this->_formclass = $formclass;
    }

    public function setRequired($required) {
      $this->_required = $required;
    }

    public function setFields($fields) {
      $this->_fields = $fields;
    }

    public function setLabels($labels) {
      $this->_labels = $labels;
    }

    public function setValues($values) {
      $this->_values = $values;
    }

    public function setTable($table) {
      $this->_table = $table;
    }

    public function setSubmitValue($submitvalue) {
      $this->_submitvalue = $submitvalue;
    }

    public function setCancelValue($cancelvalue) {
      $this->_cancelvalue = $cancelvalue;
    }

    public function setShowSubmit($showsubmit) {
      $this->_showsubmit = $showsubmit;
    }

    public function setShowCancel($showcancel) {
      $this->_showcancel = $showcancel;
    }

    public function setSubmitClass($submitclass) {
      $this->_submitclass = $submitclass;
    }

    public function setCancelClass($cancelclass) {
      $this->_cancelclass = $cancelclass;
    }

    public function setSubmitAdictionalHTML($submitadictionalhtml) {
      $this->_submitadictionalhtml = $submitadictionalhtml;
    }

    public function setCancelAdictionalHTML($canceladictionalhtml) {
      $this->_canceladictionalhtml = $canceladictionalhtml;
    }
}