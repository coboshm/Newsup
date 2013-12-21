<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'NoticiaTaula.php';
require_once 'VotComentari.php';
require_once 'ComentarisTaula.php';

class VotsComentarisPos extends Zend_Db_Table
{
        protected $_name = "votscomentarispositius";
        protected $_primary = "id";


        public function getVotsComentari($idComentari){
            $votsOk = array();
            $select = $this->select();
            $select->where("id_comentari = ?", $idComentari);
            $vots = $this->fetchAll($select);
            foreach($vots as $vot){
                $votsOk[] = new VotComentari($vot->id, $vot->id_comentari, $vot->id_usuari, $vot->data);
            }
            return $votsOk;
        }

        public function afegirVotPositiu($idComentari, $Usuari,$dataHora){
            
            $vot = $this->createRow();
            $vot->id_comentari = $idComentari;
            $vot->id_usuari = $Usuari->id_user;
            $vot->data = $dataHora;
            $vot->save();

            $_ComentarisTaula = new ComentarisTaula();
            $_ComentarisTaula->setPunt($idComentari);

        }
}


?>