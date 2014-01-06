<?php
  /************************************
   * Arquivo:
   *   Classe de Conexao com o Banco
   * Ultima Alteracao:
   *   Data: 31.12.2013
   *   Programador: Patrick
   ************************************/
  class DBO extends Oraculum_Models
  {
    private $_host=NULL;
    private $_username=NULL;
    private $_password=NULL;
    private $_squema=NULL;

    // Contrutor
    public function __construct() {
    }
    public static function execSQL($sql, $showsql=false) {
        if($showsql):
            echo '<br />SQL: <pre>'.$sql.'</pre>';
        endif;
        try {
            return self::$connection->query($sql);
        } catch (PDOException $e) {
            throw new Exception('PDO Connection Error: '.$e->getMessage());
        }
    }

    public function getInsertId() {
        try {
            return self::$connection->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('PDO Connection Error: '.$e->getMessage());
        }
    }
	
    public function start() {
		$this->execSQL('begin');
    }

    public function commit() {
		$this->execSQL('commit');
    }

    public function rollback() {
		$this->execSQL('rollback');
    }

    public static function dados($query) {
		return $query->fetchAll();
    }

    public function linhas($query)
    {
        return $query->rowCount();
        //return $query->query('SELECT FOUND_ROWS()')->fetchColumn();
    }
  }
