<?php
class Oraculum_Models
{
    private $_dsn=NULL;
    private $_dsntype=1;
    private $_user=NULL;
    private $_pass=NULL;
    private $_host=NULL;
    private $_driver=NULL;
    private $_database=NULL;
    private $_driveroptions=array();
    private $_model=NULL;
    public static $connection=NULL;

    public function __construct($model=NULL) {
        if (!defined('MODEL_DIR')) {
            define('MODEL_DIR', 'models');
        }
        Oraculum::Load('DBO');
        Oraculum::Load('ActiveRecord');
        return $this->LoadModel($model);
    }

    public function LoadModel($model=NULL) {
        if (!is_null($model)) {
            $modelfile=MODEL_DIR.'/'.$model.'.php';
            if (file_exists($modelfile)) {
                include($modelfile);
            } else {
                throw new Exception('[Erro CGM17] Modelo nao encontrado ('.$modelfile.') ');
            }
            if ($this->_dsntype==2) {
                $dsn=preg_split('[://|:|@|/]', $this->_dsn);
                $this->_driver=strtolower($dsn[0]);
                $this->_user=$dsn[1];
                $this->_pass=$dsn[2];
                $this->_host=$dsn[3];
                $this->_database=$dsn[4];
                $this->_driveroptions=isset($dsn[5])?$dsn[5]:NULL;
                $this->_dsn=$this->_driver.':host='.$this->_host.';dbname='.$this->_database;
            }
            $this->_model=$model;
        }
        if (!isset(self::$connection)) {
            $this->PDO();
        }
        return $this;
    }

    public function SaveModel($table='all', $debug=TRUE) {
        $table=strtolower($table);
        if ($table=='all') {
            $tables=self::$connection->query('SHOW TABLES')->fetchAll();
            foreach ($tables as $table) {
                $dtodir=MODEL_DIR.'/dto/';
                $daodir=MODEL_DIR.'/dao/';
                if (!file_exists($dtodir)) {
                    if (is_writable(MODEL_DIR)) {
                        mkdir($dtodir);
                    } else {
                        throw new Exception('[Erro CGM53] Sem permissao para criar diretorio DTO');
                    }
                }
                if (!file_exists($daodir)) {
                    if (is_writable(MODEL_DIR)) {
                        mkdir($daodir);
                    } else {
                        throw new Exception('[Erro CGM61] Sem permissao para criar diretorio DAO');
                    }
                }

                $modelfile=$dtodir.$table[0].'.php';
                if (is_writable($dtodir)) {
                    $model=$this->GenerateDTO($table[0], FALSE);
                    $mf=fopen($modelfile, 'w');
                    fwrite($mf, $model);
                    fclose($mf);
                } else {
                    throw new Exception('[Erro CGM71] O arquivo nao pode ser gravado ('.$modelfile.')');
                }

                $modelfile=$daodir.$table[0].'.php';
                if (is_writable($daodir)) {
                    $model=$this->GenerateDAO($table[0], FALSE);
                    $mf=fopen($modelfile, 'w');
                    fwrite($mf, $model);
                    fclose($mf);
                } else {
                    throw new Exception('[Erro CGM81] O arquivo nao pode ser gravado ('.$modelfile.')');
                }
            }
        }
        if ($debug) {
            echo 'Classes geradas com sucesso!<br />';
            echo 'Para carregar as classes geradas em alguma &aacute;rea do site utilize o seguinte c&oacute;digo:<br />';
            echo '<pre>';
            highlight_string("<?php\n\tOraculum::Load('Models');\n\t\$db=new Oraculum_Models('".$this->_model."');\n\t\$db->LoadModelClass();");
            echo '</pre>';
        }
    }

    public function LoadModelClass($model='all', $type='AR', $key='id') {
        if (!is_null($model)) {
            if ($model=='all') {
                if ($type=='DO') {
                    foreach (glob(MODEL_DIR.'/dto/*.php') as $filename) {
                        include_once($filename);
                    }
                    foreach (glob(MODEL_DIR.'/dao/*.php') as $filename) {
                        include_once($filename);
                    }
                } else {
                    foreach (glob(MODEL_DIR.'/ar/*.php') as $filename) {
                        include_once($filename);
                    }
                }
            } else {
                $model=strtolower($model);
                if ($type=='DO') {
                    $modelfile=MODEL_DIR.'/dto/'.$model.'.php';
                    if (file_exists($modelfile)) {
                        include($modelfile);
                    } else {
                        throw new Exception('[Erro CGM93] Modelo nao encontrado ('.$modelfile.') ');
                    }
                    $modelfile=MODEL_DIR.'/dao/'.$model.'.php';
                    if (file_exists($modelfile)) {
                        include($modelfile);
                    } else {
                        throw new Exception('[Erro CGM93] Modelo nao encontrado ('.$modelfile.') ');
                    }
                } else {
                    $modelfile=MODEL_DIR.'/ar/'.$model.'.php';
                    if (file_exists($modelfile)) {
                        include_once($modelfile);
                    } else {
                        if (!$this->LoadDinamicModelClass($model, $key)) {
                            throw new Exception('[Erro CGM93] Modelo nao encontrado ('.$modelfile.') ');
                        }
                    }
                }
            }
        }
        return $this;
    }
    public function LoadDinamicModelClass($model=NULL, $key='id') {
        if (!is_null($model)) {
            $class=ucwords($model);
            if (!class_exists($class)) {
                $eval='class '.$class.' extends ActiveRecord{';
                $eval.=' public function __construct(){';
                $eval.='     parent::__construct(get_class($this))';
                $eval.='     ->setKey(array(\''.$key.'\'));';
                $eval.=' }';
                $eval.='}';
                eval($eval);
            }
            return true;
        } else {
            throw new Exception('[Erro CGM160] Modelo nao informado ('.$model.') ');
        }
        return $this;
    }
    public function PDO()
    {
        if (extension_loaded('pdo')) {
            if (in_array($this->_driver, PDO::getAvailableDrivers())) {
                try {
                    self::$connection=new PDO($this->_dsn, $this->_user, (!$this->_pass?'':$this->_pass), $this->_driveroptions);
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    //echo 'CONECTADO!!!';
                } catch (PDOException $e) {
                    throw new Exception('PDO Connection Error: '.$e->getMessage());
                }
                return self::$connection;
            } else {
                throw new Exception('[Error CGM54] Nao ha driver disponivel para \''.$this->_driver.'\'');
            }
        } else {
            throw new Exception('[Error CGM57] Extensao PDO nao carregada');
        }
    }

    public function GenerateClass($table='all', $create=TRUE) {
        $table=strtolower($table);
        if ($table=='all') {
            $tables=self::$connection->query('SHOW TABLES')->fetchAll();
            foreach ($tables as $table) {
                $this->GenerateDTO($table[0], $create);
                $this->GenerateDAO($table[0], $create);
            }
        } else {
            try {
                $desc=self::$connection->query('DESC '.$table)->fetchAll();
                $classedto=ucwords($table)."DTO";
                $class='  class '.$classedto.' extends DBO'."\n";
                $class.="  {\n";
                $contador=0;
                foreach ($desc as $d) {
                    $campo[$contador]=$d['Field'];
                    $tipo[$contador]=$d['Type'];
                    $null[$contador]=$d['Null'];
                    $key[$contador]=$d['Key'];
                    $default[$contador]=$d['Default'];
                    $extra[$contador]=$d['Extra'];
                    if (is_null($default[$contador])) {
                        $default[$contador]="NULL";
                    } else {
                        $default[$contador]="'".$default[$contador]."'";
                    }
                    if (strpos($tipo[$contador],"int")===false) {
                        $tiposql[$contador]="%s";
                    } else {
                        $tiposql[$contador]="%u";
                    }
                        $contador++;
                    }
                    for ($c=0;$c<$contador;$c++){
                        $class.="    public \$".$campo[$c]."=".$default[$c].";\n";
                    }
                    $class.="\n    public function ".$classedto."(){}\n";

                    for ($c=0;$c<$contador;$c++) {
                        $name=ucwords($campo[$c]);
                        if ($key[$c]!="") {
                            $class.="\n    // ".$name." (".$key[$c]." ".$tipo[$c].")\n";
                        } else {
                            $class.="\n    // ".$name." (".$tipo[$c].")\n";
                        }
                        $class.="    public function get".$name."()\n";
                        $class.="    {\n";
                        $class.="      return \$this->".$campo[$c].";\n";
                        $class.="    }\n";
                        $class.="    public function set".$name."($".$campo[$c].")\n";
                        $class.="    {\n";
                        $class.="      \$this->".$campo[$c]."=\$".$campo[$c].";\n";
                        $class.="    }\n";
                    }
                    $class.="  }\n";
                    if ($create) {
                        eval($class);
                    } else {
                        return "<?php \n".$class;
                    }

            } catch (PDOException $e) {
                throw new Exception('PDO Connection Error: '.$e->getMessage());
            }
        }
    }


    public function GenerateDTO ($table='all', $create=TRUE) {
        $table=strtolower($table);
        if ($table=='all') {
            $tables=self::$connection->query('SHOW TABLES')->fetchAll();
            foreach ($tables as $table) {
                $this->GenerateDTO($table[0], $create);
            }
        } else {
            try {
                $desc=self::$connection->query('DESC '.$table)->fetchAll();
                $classedto=ucwords($table)."DTO";
                $class="  class ".$classedto."\n";
                $class.="  {\n";
                $contador=0;
                foreach ($desc as $d) {
                    $campo[$contador]=$d['Field'];
                    $tipo[$contador]=$d['Type'];
                    $null[$contador]=$d['Null'];
                    $key[$contador]=$d['Key'];
                    $default[$contador]=$d['Default'];
                    $extra[$contador]=$d['Extra'];
                    if (is_null($default[$contador])) {
                        $default[$contador]="NULL";
                    } else {
                        $default[$contador]="'".$default[$contador]."'";
                    }
                    if (strpos($tipo[$contador],"int")===false) {
                        $tiposql[$contador]="%s";
                    } else {
                        $tiposql[$contador]="%u";
                    }
                        $contador++;
                    }
                    for ($c=0;$c<$contador;$c++){
                        $class.="    private \$".$campo[$c]."=".$default[$c].";\n";
                    }
                    $class.="\n    public function ".$classedto."(){}\n";

                    for ($c=0;$c<$contador;$c++) {
                        $name=ucwords($campo[$c]);
                        if ($key[$c]!="") {
                            $class.="\n    // ".$name." (".$key[$c]." ".$tipo[$c].")\n";
                        } else {
                            $class.="\n    // ".$name." (".$tipo[$c].")\n";
                        }
                        $class.="    public function get".$name."()\n";
                        $class.="    {\n";
                        $class.="      return \$this->".$campo[$c].";\n";
                        $class.="    }\n";
                        $class.="    public function set".$name."($".$campo[$c].")\n";
                        $class.="    {\n";
                        $class.="      \$this->".$campo[$c]."=\$".$campo[$c].";\n";
                        $class.="    }\n";
                    }
                    $class.="  }\n";
                    if ($create) {
                        eval($class);
                    } else {
                        return "<?php \n".$class;
                    }

            } catch (PDOException $e) {
                throw new Exception('PDO Connection Error: '.$e->getMessage());
            }
        }
    }

    public function GenerateDAO ($table='all', $create=TRUE) {
        $table=strtolower($table);
        if ($table=='all') {
            $tables=self::$connection->query('SHOW TABLES')->fetchAll();
            foreach ($tables as $table) {
                $this->GenerateDAO($table[0], $create);
            }
        } else {
            try {
                $desc=self::$connection->query('DESC '.$table)->fetchAll();
                $classedao=ucwords($table)."DAO";
                $classedto=ucwords($table)."DTO";
                $classdao="  class ".$classedao." extends DBO\n";
                $classdao.="  {\n\n";
                //foreach ($desc as $d) {

                $contador=sizeof($desc);
                if ($contador>0) {
                    $classdao.="    // Select All\n";
                    $classdao.="    public function getAll(\$limit=10)\n";
                    $classdao.="    {\n";
                    $classdao.="      \$objDto=new ".$classedto."();\n";
                    $classdao.="      \$return=array();\n";
                    $classdao.="      if(floor(\$limit)!=0)\n";
                    $classdao.="      {\n";
                    $classdao.="      	\$sqllimit=\"LIMIT \".floor(\$limit);\n";
                    $classdao.="      }\n";
                    $classdao.="      else\n";
                    $classdao.="      {\n";
                    $classdao.="      	\$sqllimit=\"\";\n";
                    $classdao.="      }\n";
                    $classdao.="      \$sql=\"SELECT * FROM ".$table." \".\$sqllimit;\n";
                    $classdao.="      \$resultado=\$this->execSQL(\$sql);\n";
                    $classdao.="      \$dados=\$this->dados(\$resultado);\n";
                    $classdao.="      foreach(\$dados as \$d) {\n";
                    //$classdao.="      while(\$dados=\$this->dados(\$resultado))\n";
                    //$classdao.="      {\n";
                    foreach ($desc as $d) {
                        $name=ucwords($d[0]);
                        //$classdao.="var_dump(\$dados);";
                        $classdao.="        \$objDto->set".$name."(stripslashes(\$d['".$d[0]."']));\n";
                        //$classdao.="        \$objDto->set".$name."(stripslashes(\$dados['".$d[0]."']));\n";
                    }
                    $classdao.="        \$return[]=clone \$objDto;\n";
                    $classdao.="      }\n";
                    $classdao.="      return \$return;\n";
                    $classdao.="    }\n\n";

                    $classdao.="    // Select by Id\n";
                    $classdao.="    public function getById(\$id)\n";
                    $classdao.="    {\n";
                    $classdao.="      \$objDto=new ".$classedto."();\n";
                    $classdao.="      \$sql=sprintf('SELECT * FROM ".$table." WHERE codigo=\"%u\"',\$this->secsql(\$id));\n";
                    $classdao.="      \$resultado=\$this->execSQL(\$sql);\n";
                    $classdao.="      if(\$this->linhas(\$resultado)==1)\n";
                    $classdao.="      {\n";
                    $classdao.="      \$dados=\$this->dados(\$resultado);\n";
                    $classdao.="      foreach(\$dados as \$d) {\n";
                    foreach ($desc as $d) {
                        $name=ucwords($d[0]);
                        $classdao.="        \$objDto->set".$name."(stripslashes(\$d['".$d[0]."']));\n";
                    }
                    $classdao.="        \$return=clone \$objDto;\n";
                    $classdao.="      }\n";
                    $classdao.="      }\n";
                    $classdao.="      else\n";
                    $classdao.="      {\n";
                    $classdao.="        \$return=NULL;\n";
                    $classdao.="      }\n";
                    $classdao.="      return \$return;\n";
                    $classdao.="    }\n\n";

                    $classdao.="    // Insert\n";
                    $classdao.="    public function insert(".$classedto." \$objDto)\n";
                    $classdao.="    {\n";
                    $classdao.="      \$sql=sprintf('INSERT INTO ".$table." (";
                    $c=0;
                    foreach ($desc as $d) {
                        $classdao.=$d[0];
                        if ($c<$contador-1) {
                            $classdao.=",";
                        }
                        $c++;
                    }
                    $classdao.=") VALUES (";
                    $c=0;
                    foreach ($desc as $d) {
                        if (strpos($d[1],"int")===false) {
                            $classdao.="\"%s\"";
                        } else {
                            $classdao.="\"%u\"";
                        }
                        if ($c<$contador-1) {
                            $classdao.=",";
                        }
                        $c++;
                    }
                    $classdao.=")',\n";
                    $c=0;
                    foreach ($desc as $d) {
                        $name=ucwords($d[0]);
                        $classdao.="               \$this->secsql(\$objDto->get".$name."())";
                        if ($c<$contador-1) {
                            $classdao.=",\n";
                        }
                        $c++;
                    }
                    $classdao.="              );\n";
                    $classdao.="      \$this->execSQL(\$sql);\n";
                    $classdao.="      \$objDto->setCodigo(mysql_insert_id());\n";
                    $classdao.="      return \$objDto;\n";
                    $classdao.="    }\n\n";
                    $classdao.="    // Update\n";
                    $classdao.="    public function update(".$classedto." \$objDto)\n";
                    $classdao.="    {\n";
                    $classdao.="      if(!\$objDto->getCodigo())\n";
                    $classdao.="        throw new Exception('Valor da chave primaria invalido');\n";
                    $classdao.="      \$sql=sprintf('UPDATE ".$table." SET ";
                    $c=0;
                    foreach ($desc as $d) {
                        if (strpos($d[1],"int")===false) {
                            $classdao.=$d[0]."=\"%s\"";
                        } else {
                            $classdao.=$d[0]."=\"%u\"";
                        }
                        if ($c<$contador-1) {
                            $classdao.=", ";
                        }
                        $c++;
                    }
                    $classdao.=" WHERE codigo=\"%u\"',\n";
                    $c=0;
                    foreach ($desc as $d) {
                        $name=ucwords($d[0]);
                        $classdao.="               \$this->secsql(\$objDto->get".$name."())";
                        if ($c<$contador-1) {
                            $classdao.=",\n";
                        }
                        $c++;
                    }
                    $classdao.="              );\n";
                    $classdao.="      \$this->execSQL(\$sql);\n";
                    $classdao.="    }\n\n";
                    $classdao.="    // Delete\n";
                    $classdao.="    public function delete(".$classedto." \$objDto)\n";
                    $classdao.="    {\n";
                    $classdao.="      if(\$objDto->getCodigo()==NULL)\n";
                    $classdao.="          throw new Exception('Valor da chave primaria invalido.');\n";
                    $classdao.="      \$sql=sprintf('DELETE FROM ".$table." WHERE codigo=\"%u\"',\$this->secsql(\$objDto->getCodigo()));\n";
                    $classdao.="      \$this->execSQL(\$sql);\n";
                    $classdao.="    }\n\n";
                    $classdao.="    // Save\n";
                    $classdao.="    public function save(".$classedto." &\$objDto)\n";
                    $classdao.="    {\n";
                    $classdao.="      if(\$objDto->getCodigo()!== null)\n";
                    $classdao.="      {\n";
                    $classdao.="        \$this->update(\$objDto);\n";
                    $classdao.="      }\n";
                    $classdao.="      else\n";
                    $classdao.="      {\n";
                    $classdao.="        \$this->insert(\$objDto);\n";
                    $classdao.="      }\n";
                    $classdao.="    }\n\n";

                    $classdao.="    // SecSQL\n";
                    $classdao.="    public function secsql(\$string)\n";
                    $classdao.="    {\n";
                    $classdao.="      \$string=mysql_real_escape_string(\$string);\n";
                    $classdao.="      return \$string;\n";
                    $classdao.="    }\n";
                    $classdao.="  }\n";
                    if ($create) {
                        eval($classdao);
                    } else {
                        return "<?php \n".$classdao;
                    }
                }
            } catch (PDOException $e) {
                throw new Exception('PDO Connection Error: '.$e->getMessage());
            }
        }


    }


    public function GenerateAR ($table='all', $create=TRUE) {
        if ($table=='all') {
            $tables=self::$connection->query('SHOW TABLES')->fetchAll();
            foreach ($tables as $table) {
                $this->GenerateAR($table[0], $create);
            }
        } else {
            $table=strtolower($table);
            $classear=ucwords($table);
            $class="class ".$classear." extends ActiveRecord{\n";
            $class.="\tpublic function __construct(){\n";
            $class.="\t\tparent::__construct(get_class(\$this));\n";
            $class.="\t}\n";
            $class.="}\n";
            if ($create) {
                eval($class);
            } else {
                return "<?php \n".$class;
            }
        }
    }

  public function LoadTable($table=NULL)
  {
  	if (is_null($table)) {
  		throw new Exception ('[Erro CGM66] Tabela nao informada');
  	} else {
                if ($adapter == null) {
                    return Oraculum_Models::getInstance()->getCurrentConnection();
                } else {
                    return Oraculum_Models::getInstance()->openConnection($adapter, $name);
                }
  	}
  	return $this;
  }
    public static function connection($adapter = null, $name = null)
    {
        if ($adapter == null) {
            return Doctrine_Manager::getInstance()->getCurrentConnection();
        } else {
            return Doctrine_Manager::getInstance()->openConnection($adapter, $name);
        }
    }

    public function setDsn($dsn=NULL) {
  	if (is_null($dsn)) {
  		throw new Exception ('[Erro CGM414] DSN nao informado');
  	} else {
            $this->_dsn=$dsn;
        }
    }
    public function getModelName() {
        return $this->_model;
    }
}