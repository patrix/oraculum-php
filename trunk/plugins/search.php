<?php
/******************************
 * TO-DO: (criado por Patrick)
 * - Incluir logica que caso nao seja informado o segundo
 *   parametro do metodo AddClasse, busque em todos os campos
 * - Incluir opcao de usar o plugin de paginacao automaticamente
 * - Incluir opcao de destacar a palavra buscada nos resultados de
 *   forma personalizavel (cor, negrito, etc)
 * - Incluir atributo informando o numero de resultados encontrados
 ******************************/
class Oraculum_Search extends DBO {

    private $_config = NULL;
    private $_param = '';
    private $_classes = array();
    private $_urls = array();
    private $_fields = array();
    private $_results = array();

    public function __construct() {
        
    }

    public function config($conf) {
        if (is_array($conf)) {
            $this->_config = $conf;
        } else {
            throw new Exception('[Erro] A variável de configuração tem que ser um Array[tabela][campos]=campo1,campo2');
        }
    }

    public function search() {
    
      foreach ($this->_classes as $k=>$classe):
        if (sizeof($this->_fields[$k])>0):
          $url=$this->_urls[$k];
          foreach($this->_fields[$k] as $field):
            $data=$classe->{'getAllBy'.$field}('%'.$this->_param.'%');
            $result['field']=$field;
            $result['key']=$k;
            $result['data']=$data;
            $result['class']=get_class($classe);
            $result['url']=preg_replace("#(.*){%(.*?)%}(.*)#", '$2', $url);
            $result['url']=$data[0]->$result['url'];
            $this->_results[]=$result;
          endforeach;
        endif;
        
      endforeach;
      return $this->_results;
    }
    
    public function AddClass($obj=ActiveRecord, $fields=array(), $url=array()) {
      $this->_classes[]=$obj;
      $this->_urls[]=$url;
      $this->_fields[]=$fields;
    }
    
    public function SetSearch($param) {
      $this->_param=$param;
    }

}