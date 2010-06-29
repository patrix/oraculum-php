<?php
/*Oraculum::Load('Models');
Oraculum::Load('DBO');*/
class ActiveRecord extends DBO{
    private $_fields=array();
    /*protected $_fields = array();
    protected $_keyField = null;*/
    protected $_className=NULL;
    protected $_tableName=NULL;
    protected $_key=array('id');
    protected $_keyvalue=array();

    public function __construct($class=NULL) {
        if (!is_null($class)) {
            $this->_className=$class;
            $this->_tableName=strtolower($class);
        }
        return $this;
    }

    public function getAll($limit=NULL) {
        $limit=(int)$limit;
        $query='SELECT * FROM '.$this->_tableName;
        if ($limit>0) {
            $query.=' LIMIT '.$limit;
        }
        $rows=$this->execSQL($query);
        return $this->fetch($rows);
    }
    public function getByTableField($field, $value, $type='%s') {
        $query=sprintf('SELECT * FROM '.$this->_tableName.' WHERE '.$field.'="'.$type.'" LIMIT 1',$this->secsql($value));
        $rows=$this->execSQL($query);
        $rows=$this->fetch($rows);
        if (sizeof($rows)>0) {
            return $rows[0];
        } else {
            return NULL;
        }
    }
    public function getAllByTableField($field, $value, $type='%s') {
        $query=sprintf('SELECT * FROM '.$this->_tableName.' WHERE '.$field.'="'.$type.'"',$this->secsql($value));
        $rows=$this->execSQL($query);
        $rows=$this->fetch($rows);
        return $rows;
    }
    public function __set($name, $value) {
        if (!is_null($value)) {
            $this->_fields[$name]=$value;
        } else {
            $this->_fields[$name]=NULL;
        }
    }
    public function __get($name) {
        if (array_key_exists($name, $this->_fields)) {
            return $this->_fields[$name];
        } else {
            throw new Exception('[Erro CGAR48] Campo \''.$name.'\' inexistente');
        }
    }
    public function __call($name, $values) {
        if (stripos($name, 'getBy')!==FALSE) {
            $field=strtolower(str_replace('getBy', '',$name));
            $value=(isset($values[0]))?$values[0]:NULL;
            $type=(isset($values[1]))?$values[1]:'%s';
            return $this->getByTableField($field, $value, $type);
        } elseif (stripos($name, 'getAllBy')!==false) {
            $field=strtolower(str_replace('getAllBy', '',$name));
            $value=(isset($values[0]))?$values[0]:NULL;
            $type=(isset($values[1]))?$values[1]:'%s';
            return $this->getAllByTableField($field, $value, $type);
        }
    }

    public function fetch($rows) {
        $return=array();
        foreach($rows as $row) {
            $obj=new self($this->_className);
            if (!empty($this->_key)) {
                $obj->setKey($this->_key);
            }
            foreach ($row as $field=>$value) {

                if (!is_integer($field)) {
                    $obj->$field=$value;
                }
            }
            $return[]=clone $obj;
        }
        return $return;
    }

    public function insert()
    {
        $values=NULL;
        $fields=NULL;
        $eval='$query=sprintf(\'INSERT INTO '.$this->_tableName.' ';
        if (sizeof($this->_fields)>0) {
            foreach ($this->_fields as $field=>$value) {
                if (is_null($values)) {
                    $eval.='(';
                } else {
                    $eval.=',';
                    $values.=',';
                    $fields.=',';
                }
                $eval.=$field;
                $values.='"%s"';
                $fields.='$this->secsql($this->'.$field.')';
            }
        }
        $eval.=')';
        $eval.=' VALUES ('.$values.')\','.$fields.');';
        //echo $eval;
        eval($eval);
        $this->execSQL($query);
        $this->_keyvalue=array($this->getInsertId());
        return $this;
    }
    public function update()
    {
        $fields=NULL;
        $eval='$query=sprintf(\'UPDATE '.$this->_tableName.' SET ';
        if (sizeof($this->_fields)>0) {
            foreach ($this->_fields as $field=>$value) {
                if (!is_null($fields)) {
                    $eval.=',';
                }
                $eval.=$field.'="%s" ';
                $fields.='$this->secsql($this->'.$field.'),';
            }
        }
        $eval.='WHERE '.$this->_key[0].'="%u"';
        $fields.='$this->secsql('.$this->_keyvalue[0].')';
        $eval.='\','.$fields.');';
        //echo $eval;
        eval($eval);
        $this->execSQL($query);
        $this->_keyvalue=array($this->getInsertId());
        return $this;
    }
    public function delete()
    {
        $this->updateKeyValue();
        $fields=NULL;
        $eval='$query=sprintf(\'DELETE FROM '.$this->_tableName.' ';
        $eval.='WHERE '.$this->_key[0].'="%u"';
        $fields.='$this->secsql('.$this->_keyvalue[0].')';
        $eval.='\','.$fields.');';
        eval($eval);
        $this->execSQL($query);
        return $this;
    }
    public function save()
    {
        $this->updateKeyValue();
        if (!empty($this->_keyvalue)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function setKey($key=array('id')) {
        $this->_key=$key;
        $this->updateKeyValue();
    }

    public function updateKeyValue() {
        $this->_keyvalue=array();
        foreach($this->_key as $key) {
            if (isset($this->_fields[$key])) {
                $this->_keyvalue[]=$this->_fields[$key];
            }
        }
    }
    public function getKeyValue($index=NULL) {
        if (!is_null($index)) {
            return $this->_keyvalue[$index];
        } else {
            return $this->_keyvalue;
        }
    }
    public function secsql($string)
    {
      //$string=mysql_real_escape_string($string);
      return $string;
    }

}
/*














  public function __construct($tableName, $key=null, PDO $dbConnection=null){
        $this->_tableName = $tableName;
        $this->_key = $key;

        $this->_dbConnection = (!$dbConnection && self::$_defaultDBConnection)?
                                    self::$_defaultDBConnection:
                                    $dbConnection;

        $this->_dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function getKey(){
        return $this->_key;
    }

    public function __get($field){
        return $this->getValue($field);
    }public function getValue($field){
        if(!$this->_fields){
            $this->describe();
        }

        if(isset($this->_fields[$field])){
            if(!$this->_fields[$field]['value']){
                $this->select();
            }

            return $this->_fields[$field]['value'];
        } else {
            throw new Exception('Unknown field `'.$field.'`');
        }
    }

    public function __set($field, $value){
        return $this->setValue($field, $value);
    }
    public function setValue($field, $value){
        if(!$this->_fields){
            $this->describe();
        }

        if(isset($this->_fields[$field])){
            $this->_fields[$field]['value']   = $value;
            $this->_fields[$field]['changed'] = true;

            return $this->_fields[$field]['value'];
        }else{
            throw new Exception('Unknown field `'.$field.'`');
        }
    }
    public function setDBConnection(PDO $db){
        return $this->_dbConnection = $db;
    }
    public function getDBConnection(){
        return $this->_dbConnection;
    }
    protected function select(){
        if(!$this->_fields){
            $this->describe();
        }

        if(!$this->_key || !$this->_keyField){
            throw new Exception("Key field ('{$this->_key}', `{$this->_keyField}`) are invalid");
        }

        $db = $this->getCheckedDBConnection();
        $sql = "SELECT * FROM `{$this->_tableName}` WHERE `{$this->_keyField}` = :key LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute(array(':key'=>$this->_key));

        foreach($stmt->fetch() as $field=>$value){
            //field must present
            assert(isset($this->_fields[$field]));
            $this->_fields[$field]['changed'] = false;
            $this->_fields[$field]['value'] = $value;
        }

    }


    /*public function __construct() {
    }*/
/*


    public function getAll($limit=10)
    {
        $limit=(int)$limit;
        $fields=self::$fields;
        foreach ($fields as $field) {

        }

        $objDto=new NoticiasDTO();
        $return=array();
        if($limit>0) {
            $sqllimit='LIMIT '.$limit;
        }
        else
        {
        $sqllimit="";
        }
        $sql="SELECT * FROM noticias ".$sqllimit;
        $resultado=$this->execSQL($sql);
        $dados=$this->dados($resultado);
        foreach($dados as $d) {
        $objDto->setCodigo(stripslashes($d['codigo']));
        $objDto->setTitulo(stripslashes($d['titulo']));
        $objDto->setTarja(stripslashes($d['tarja']));
        $objDto->setFoto(stripslashes($d['foto']));
        $objDto->setData(stripslashes($d['data']));
        $objDto->setTexto(stripslashes($d['texto']));
        $return[]=clone $objDto;
        }
      return $return;

      return $this;
    }

    // Select by Id
    public function getById($id)
    {
      $objDto=new NoticiasDTO();
      $sql=sprintf('SELECT * FROM noticias WHERE codigo="%u"',$this->secsql($id));
      $resultado=$this->execSQL($sql);
      if($this->linhas($resultado)==1)
      {
      $dados=$this->dados($resultado);
      foreach($dados as $d) {
        $objDto->setCodigo(stripslashes($d['codigo']));
        $objDto->setTitulo(stripslashes($d['titulo']));
        $objDto->setTarja(stripslashes($d['tarja']));
        $objDto->setFoto(stripslashes($d['foto']));
        $objDto->setData(stripslashes($d['data']));
        $objDto->setTexto(stripslashes($d['texto']));
        $return=clone $objDto;
      }
      }
      else
      {
        $return=NULL;
      }
      return $return;
    }

    // Insert
    public function insert(NoticiasDTO $objDto)
    {
      $sql=sprintf('INSERT INTO noticias (codigo,titulo,tarja,foto,data,texto) VALUES ("%u","%s","%s","%s","%s","%s")',
               $this->secsql($objDto->getCodigo()),
               $this->secsql($objDto->getTitulo()),
               $this->secsql($objDto->getTarja()),
               $this->secsql($objDto->getFoto()),
               $this->secsql($objDto->getData()),
               $this->secsql($objDto->getTexto())              );
      $this->execSQL($sql);
      $objDto->setCodigo(mysql_insert_id());
      return $objDto;
    }

    // Update
    public function update(NoticiasDTO $objDto)
    {
      if(!$objDto->getCodigo())
        throw new Exception('Valor da chave primaria invalido');
      $sql=sprintf('UPDATE noticias SET codigo="%u", titulo="%s", tarja="%s", foto="%s", data="%s", texto="%s" WHERE codigo="%u"',
               $this->secsql($objDto->getCodigo()),
               $this->secsql($objDto->getTitulo()),
               $this->secsql($objDto->getTarja()),
               $this->secsql($objDto->getFoto()),
               $this->secsql($objDto->getData()),
               $this->secsql($objDto->getTexto())              );
      $this->execSQL($sql);
    }

    // Delete
    public function delete(NoticiasDTO $objDto)
    {
      if($objDto->getCodigo()==NULL)
          throw new Exception('Valor da chave primaria invalido.');
      $sql=sprintf('DELETE FROM noticias WHERE codigo="%u"',$this->secsql($objDto->getCodigo()));
      $this->execSQL($sql);
    }

    // Save
    public function save(NoticiasDTO &$objDto)
    {
      if($objDto->getCodigo()!== null)
      {
        $this->update($objDto);
      }
      else
      {
        $this->insert($objDto);
      }
    }
}*/