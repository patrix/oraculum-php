<?php
/******************************
 * TO-DO: (criado por Patrick)
 * - Incluir logica que caso nao seja informado o segundo
 *   parametro do metodo AddClasse, busque em todos os campos
 * - Incluir opcao de usar o plugin de paginacao automaticamente
 * - Incluir logica de retornar mais coisas, como URL personalizada
 *   por pagina, etc
 * - Incluir opcao de destacar a palavra buscada nos resultados de
 *   forma personalizavel (cor, negrito, etc)
 * - Incluir atributo informando o numero de resultados encontrados
 ******************************/
Oraculum::Load('DBO');
class Oraculum_Cart extends DBO {

    private $_config = NULL;
    private $_param = '';
    private $_class = null;
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
    public function newPed($campos){
        while (list($key, $val) = each($campos)) {
            $this->_class->$key = $val;
        }
       $this->_class->insert();
       return $this->_class->getInsertId();
    }
    public function verifyPed($campo,$valor){
        $data = $this->_class->__call('getBy'.$campo,array($valor));
       return $data;
    }
    public function search() {
    
      foreach ($this->_classes as $k=>$classe):
        if (sizeof($this->_fields[$k])>0):
          foreach($this->_fields[$k] as $field):
            $data=$classe->__call('getAllBy'.$field, array('%'.$this->_param.'%'));
            $result['field']=$field;
            $result['key']=$k;
            $result['data']=$data;
            $result['class']=get_class($classe);
            $this->_results[]=$result;
          endforeach;
        endif;
        
      endforeach;
      return $this->_results;
    }
    
    /*public function search($param) {
        $i = 0;
        $config = $this->config;
        while (list($key, $val) = each($this->config)) {
            $aux = "";
            $campos = explode(',', $config[$key]['campos']);
            for ($j = 0; $j < sizeof($campos); $j++) {
                $aux .= $campos[$j] . " like '%$param%'";
                if (($j + 1) < sizeof($campos)) {
                    $aux .= " OR ";
                }
            }
            echo $sql[$i] = "select * from $key where $aux" . '<br/>';
            $i++;
        }
    }*/
    
    public function AddClass($obj=ActiveRecord, $fields=array()) {
      $this->_class=$obj;
      $this->_fields[]=$fields;
    }
    
    public function SetSearch($param) {
      $this->_param=$param;
    }

}

/*
EXEMPLO DE USO
<?php
    Oraculum::Load('DBO');
    Oraculum_Plugins::Load('search');
    
    $db=new Oraculum_Models(MODEL_NAME);
    $db->LoadModelClass('paginas');
    $paginas=new Paginas;
    
    $config['paginas']['titulo']='';
    $search=new Oraculum_Search();
    $search->AddClass($paginas, array('titulo'));
    $search->SetSearch(getvar('search'));
    $results=$search->search();
    foreach($results as $k=>$result):
        $registros=$result['data'];
        foreach($registros as $registro):
            echo $registro->titulo;
            echo '<hr />';
        endforeach;
    endforeach;*/