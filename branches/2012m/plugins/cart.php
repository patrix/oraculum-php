<?php
Oraculum::Load('DBO');

class Oraculum_Cart extends DBO {
    private $_class = null;
    private $_fields = array();
    private $_results = array();

    public function newPed($campos) {
        while (list($key, $val) = each($campos)) {
            $this->_class->$key = $val;
        }
        $this->_class->insert();
        return $this->_class->getInsertId();
    }

    public function newItem($campos) {
        while (list($key, $val) = each($campos)) {
            $this->_class->$key = $val;
        }
        $this->_class->insert();
        return $this->_class->getInsertId();
    }

    public function delItem($tabela,$campo,$valor) {
        $sql = "delete from $tabela where $campo = $valor";
        return $this->_class->execSQL($sql);
    }
    public function updQtd($tabela,$campo,$valor,$codigo,$valcod) {
        $sql = "update $tabela set $campo = $valor where $codigo = $valcod";
        return $this->_class->execSQL($sql);
    }

    public function verifyPed($campo, $valor) {
        $data = $this->_class->__call('getBy' . $campo, array($valor));
        return $data;
    }

    public function getItens($campo, $id) {
        $data = $this->_class->__call('getAllBy' . $campo, array($id));
        return $data;
    }

    public function AddClass($obj=ActiveRecord, $fields=array()) {
        $this->_class = $obj;
        $this->_fields[] = $fields;
    }
