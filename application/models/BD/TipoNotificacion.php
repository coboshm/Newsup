<?php

class TipoNotificacion extends Zend_Db_Table
{
    protected $_name = "tiponotificacion";
    protected $_primary = "Id";
    
    public function allTipoNotificacion()
    {
        return $this->fetchAll();
    }

    public function getTipoById($id)
    {
        return $this->find($id)->current();
    }
}
