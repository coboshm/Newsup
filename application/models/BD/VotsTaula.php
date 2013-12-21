<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'NoticiaTaula.php';

class VotsTaula extends Zend_Db_Table
{
        protected $_name = "puntuacions";
        protected $_primary = "id_puntuacio";

        public function getVotsNoticia($idNoticia){
            $votsOk = array();
            $select = $this->select();
            $select->where("id_noticia = ?", $idNoticia);
            $vots = $this->fetchAll($select);
            foreach($vots as $vot){
                $votsOk[] = new Vot($vot->id_puntuacio, $vot->id_noticia, $vot->id_user, $vot->data);
            }
            return $votsOk;
        }

        public function afegirVot($idNoticia, $Usuari,$dataHora){
            
            $vot = $this->createRow();
            $vot->id_noticia = $idNoticia;
            $vot->id_user = $Usuari->id_user;
            $vot->data = $dataHora;
            $vot->save();

            $_NoticiaTaula = new NoticiaTaula();
            $_NoticiaTaula->setPunt($idNoticia);
        }
}


?>