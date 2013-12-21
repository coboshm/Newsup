<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'idiomaDiari.php';

class idiomesDiari extends Zend_Db_Table
{
        protected $_name = "idiomes";
        protected $_primary = "id";

        public function getIdiomes(){
            $diaris = array();
            $diaris = $this->fetchAll();
            $llistaIdiomes = array();
            foreach($diaris as $diari){
                $llistaIdiomes[] = new idiomaDiari($diari->id, $diari->idioma);
            }
            return $llistaIdiomes;
        }

        public function getIdioma($id){
            $select = $this->select();
            $select->where("id = ?", $id);
            $categorias = $this->fetchAll($select);
            foreach($categorias as $categoria){
                return new idiomaDiari($diari->id, $diari->idioma);
            }
            
        }
}


?>