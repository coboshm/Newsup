<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class NoticiesFavoritas extends Zend_Db_Table
{
        protected $_name = "newsguardadas";
        protected $_primary = "id";

        public function setNoticiaFavorita($idNoticia, $idUsuari)
        {
            $client = $this->createRow();
            $client->id_usuari = $idUsuari;
            $client->id_noticia = $idNoticia;
            $client->save();
        }

        public function removeNoticiaFavorita($idNoticia,$idUsuari)
        {
            $select = $this->select();
            $select->where("id_usuari = ?", $idUsuari);
            $select->where("id_noticia = ?", $idNoticia);
            $noticies = $this->fetchAll($select);
            foreach($noticies as $noticia){
                $projecte = $this->find($noticia->id)->current();
                $projecte->delete();
            }
        }

        public function getGuardadas($id)
        {
            $noticiesOk = array();
            $select = $this->select();
            $select->where("id_usuari = ?", $id);
            $noticies = $this->fetchAll($select);
            foreach($noticies as $noticia){
                $noticiesOk[] = $noticia->id_noticia;
            }
            return $noticiesOk;
        }
}


?>