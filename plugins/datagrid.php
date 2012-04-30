<?php
  class Oraculum_Datagrid {
    
    private $_table=array();
    private $_grid=NULL;
    private $_showactions=true;
    private $_actions=array('delete', 'update');
    private $_actionstitle='A&ccedil;&otilde;es';
    private $_deleteurl='delete/%id%';
    private $_updateurl='update/%id%';
    private $_tableclass='';
    private $_updateclass='';
    private $_deleteclass='';
    private $_updatelabel='Update';
    private $_deletelabel='Delete';
    private $_adicionalactionhtml='';
    private $_norecordsfound='No Records Found';
    
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
              foreach ($reg as $field=>$value):          
                $this->_grid.='<th>';
                $this->_grid.=ucwords($field);
                $this->_grid.='</th>';
              endforeach;
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
            foreach ($reg as $field=>$value):
                $id=(is_null($id))?$value:$id;
                $this->_grid.='<td>';
                $this->_grid.=$value;
                $this->_grid.='</td>';
            endforeach;
            if ($this->_showactions):
              $this->_grid.='<td>';
              if (in_array('update', $this->_actions))
                $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_updateurl).'" class="'.$this->_updateclass.'">'.$this->_updatelabel.'</a> ';
              if (in_array('delete', $this->_actions))
                $this->_grid.='<a href="'.str_replace('%id%', $id, $this->_deleteurl).'" class="'.$this->_deleteclass.'" data-toggle="modal">'.$this->_deletelabel.'</a>';
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
    
    public function setTableClasse($class) {
      $this->_tableclass=$class;
    }
    public function setUpdateClasse($class) {
      $this->_updateclass=$class;
    }
    public function setDeleteClasse($class) {
      $this->_deleteclass=$class;
    }
    public function setDeleteUrl($url) {
      $this->_deleteurl=$url;
    }
    public function setUpdateUrl($url) {
      $this->_updateurl=$url;
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
  }
      
  /*    
      
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>
          C&oacute;digo
        </th>
        <th>
          Usu&aacute;rio
        </th>
        <th>
          E-mail
        </th>
        <th>
          Permiss&otilde;es
        </th>
        <th style="width:180px; text-align:center;">A&ccedil;&otilde;es</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->_table as $reg): ?>
        <tr>
          <td>
            <code><?php echo $reg->idusuario; ?></code>
          </td>
          <td>
            <?php echo $reg->login; ?>
          </td>
          <td>
            <?php echo $reg->email; ?>
          </td>
          <td>
            <?php echo $reg->permissao; ?>
          </td>
          <td style="width:180px; text-align:center;">
            <a href="" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Alterar</a>
            <a href="#myModal" class="btn btn-danger" data-toggle="modal"><i class="icon-remove icon-white"></i> Excluir</a>

          <div id="myModal" class="modal hide fade">
            <div class="modal-header">
              <button class="close" data-dismiss="modal">&times;</button>
              <h3>Confirma&ccedil;&atilde;o</h3>
            </div>
            <div class="modal-body">
              <p>Voc&ecirc; tem certeza que quer remover este registro?</p>
            </div>
            <div class="modal-footer">
              <a href="<?php echo URL; ?>usuarios/excluir/<?php echo $reg->idusuario; ?>" class="btn btn-primary">OK</a>
              <a href="#" class="btn" data-dismiss="modal" >Cancelar</a>
            </div>
          </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
    }
    
  }
    
    
    */
    
    
    
    
    
    
