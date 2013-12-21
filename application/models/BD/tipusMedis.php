<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'tipuMedis.php';

class tipusMedis extends Zend_Db_Table
{
        protected $_name = "tipusMedi";
        protected $_primary = "id";

        public function getTipus(){
            $diaris = array();
            $diaris = $this->fetchAll();
            $llistaIdiomes = array();
            foreach($diaris as $diari){
                $llistaIdiomes[] = new idiomaDiari($diari->id, $diari->tipus);
            }
            return $llistaIdiomes;
        }

        public function getMedi($id){
            $select = $this->select();
            $select->where("id = ?", $id);
            $categorias = $this->fetchAll($select);
            foreach($categorias as $categoria){
                return new idiomaDiari($diari->id, $diari->tipus);
            }
            
        }
}


?>