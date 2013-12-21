<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Categories extends Zend_Db_Table
{
        protected $_name = "categories";
        protected $_primary = "id_categoria";

        public function getCategories(){   
            $categories = array();
            $categories = $this->fetchAll();
            $llistaCategories = array();
            foreach($categories as $categoria){
                $llistaCategories[] = new Categoria($categoria->id_categoria, $categoria->nom);
            }
            return $llistaCategories;
        }

        public function getCategoria($id){
            $categoriaOk = array();
            $select = $this->select();
            $select->where("id_categoria = ?", $id);
            $categorias = $this->fetchAll($select);
            foreach($categorias as $categoria){
                return new Categoria($categoria->id_categoria, $categoria->nom);
            }
            
        }

        public function getCategoriaByName($name)
        {
            $select = $this->select();
            $select->where("nom = ?", $name);
            $categorias = $this->fetchAll($select)->current();
            return $categorias;
        }
}


?>