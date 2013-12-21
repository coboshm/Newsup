<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'tipuDiari.php';

class tipusDiari extends Zend_Db_Table
{
        protected $_name = "tipusDiari";
        protected $_primary = "id";

        public function getTipus(){
            $diaris = array();
            $diaris = $this->fetchAll();
            $llistaTipus = array();
            foreach($diaris as $diari){
                $llistaTipus[] = new tipuDiari($diari->id, $diari->tipus);
            }
            return $llistaTipus;
        }

        public function getTipu($id){
            $select = $this->select();
            $select->where("id = ?", $id);
            $categorias = $this->fetchAll($select);
            foreach($categorias as $categoria){
                return new tipuDiari($diari->id, $diari->tipus);
            }
            
        }
}


?>