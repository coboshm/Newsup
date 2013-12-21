<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class diarisRss extends Zend_Db_Table
{
        protected $_name = "diarisRss";
        protected $_primary = "id";

        public function getDiaris(){
            $diaris = array();
	    $select = $this->select();
            $select->order('nom asc');
            $diaris = $this->fetchAll($select);
            $llistaDiaris = array();
            foreach($diaris as $diari){
                $llistaDiaris[] = new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            return $llistaDiaris;
        }

        public function getDiarisTipus($id){
            $diaris = array();
            $select = $this->select();
            $select->where("tipus = ?", $id);
	    $select->order('nom asc');
            $diaris = $this->fetchAll($select);
            $llistaDiaris = array();
            foreach($diaris as $diari){
                $llistaDiaris[] = new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            return $llistaDiaris;
        }

        public function getDiarisIdioma($idioma){
            $diaris = array();
            $select = $this->select();
            $select->where("tipusMedi = ?", $idioma);
	    $select->order('nom asc');
            $diaris = $this->fetchAll($select);
            $llistaDiaris = array();
            foreach($diaris as $diari){
                $llistaDiaris[] = new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            return $llistaDiaris;
        }

        public function getDiarisTipusIdioma($id,$idioma){
            $diaris = array();
            $select = $this->select();
            $select->where("tipus = ?", $id);
            $select->where("tipusMedi = ?", $idioma);
	    $select->order('nom asc');
            $diaris = $this->fetchAll($select);
            $llistaDiaris = array();
            foreach($diaris as $diari){
                $llistaDiaris[] = new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            return $llistaDiaris;
        }

        public function getDiari($id){
            $select = $this->select();
            $select->where("id = ?", $id);
            $categorias = $this->fetchAll($select);
            foreach($categorias as $categoria){
                return new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            
        }

        public function getDiarisDins($diaris2){
            $diaris = array();
            $consulta = $this->select();
            $consulta->where("id IN(?)", $diaris2);
 	    $consulta->order('nom asc');
            $diaris = $this->fetchAll($consulta);
            
            $llistaDiaris = array();
            foreach($diaris as $diari){
                $llistaDiaris[] = new Diari($diari->id, $diari->nom,$diari->favicon, $diari->urlrss, $diari->tipusMedi);
            }
            return $llistaDiaris;
        }
}


?>
