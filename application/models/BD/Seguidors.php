<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Seguidors extends Zend_Db_Table
{
        protected $_name = "seguidors";
        protected $_primary = "id";

        public function setNewSeguidor($idSeguit,$idSegueix){
            $client = $this->createRow();
            $client->id_seguidor = $idSegueix;
            $client->id_seguit = $idSeguit;
            $client->save();
        }

        public function getIdSeguiment($idSeguit,$idSegueix){
            $consulta = $this->select();
            $consulta->where("id_seguidor = '".$idSegueix."'");
            $consulta->where("id_seguit = '".$idSeguit."'");
            $seguiments = $this->fetchAll($consulta);
            foreach($seguiments as $seguiment){
                return $seguiment->id;
            }
        }

        public function removeSeguidor($id){
            $consulta = $this->find($id)->current();
            $consulta->delete();
        }

        public function getSeguidorsUser($id){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguidor = ?", $id);
            $seguiments = $this->fetchAll($select);
            foreach($seguiments as $seguiment){
                $seguimentsOk[] = $seguiment->id_seguit;
            }
            return $seguimentsOk;
        }
        
        public function getSegueix($id,$y){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguidor = ?", $id);
            $select->limit(15,$y);
            $seguiments = $this->fetchAll($select);
            foreach($seguiments as $seguiment){
                $seguimentsOk[] = $seguiment->id_seguit;
            }
            return $seguimentsOk;
        }
        
        public function cantitatSegueix($id){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguidor = ?", $id);
            $seguiments = $this->fetchAll($select);
            $x = count($seguiments);
            return $x;
        }



        public function getSegueixen($id,$y){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguit = ?", $id);
            $select->limit(15,$y);
            $seguiments = $this->fetchAll($select);
            foreach($seguiments as $seguiment){
                $seguimentsOk[] = $seguiment->id_seguidor;
            }
            return $seguimentsOk;
        }

        public function cantitatSegueixen($id){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguit = ?", $id);
            $seguiments = $this->fetchAll($select);
            $x = count($seguiments);
            return $x;
        }

        public function getSegueixenid($id){
            $seguimentsOk = array();
            $select = $this->select();
            $select->where("id_seguidor = ?", $id);
            $seguiments = $this->fetchAll($select);
            foreach($seguiments as $seguiment){
                $seguimentsOk[] = $seguiment->id_seguit;
            }
            return $seguimentsOk;
        }


}


?>