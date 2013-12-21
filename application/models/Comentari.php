<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Noticia.php';
require_once 'BD/ComentarisTaula.php';
require_once 'BD/VotsComentarisPos.php';
require_once 'BD/VotsComentarisNeg.php';
require_once 'Usuari.php';

class Comentari
{

        private $_id;
        private $_idNoticia;
        private $_Usuari;
        private $_data;
        private $_text;
        private $_colVotsPos = array();
        private $_colVotsNeg = array();
        private $_colFills = array();

        public function __construct($id, $idNoticia, $Usuari, $data,$text)
        {
            $this->_id = $id;
            $this->_idNoticia = $idNoticia;
            $this->_Usuari = $Usuari;
            $this->_data = $data;
            $this->_text = $text;

        }

        public function getidNoticia()
        {
            return $this->_idNoticia;
        }

        public function getUsuari()
        {
            return $this->_Usuari;
        }

        public function getData()
        {
            return $this->_data;
        }

        public function getId()
        {
            return $this->_id;
        }

        public function getText()
        {
            return $this->_text;
        }

        //Metodes per vots positius
        public function addVotPos($id, $idComentari, $Usuari, $data)
        {
            $this->_colVotsPos[] = new VotComentari($id ,$idComentari,$Usuari,$data);
        }

        public function addVotsPos($ColVots){
            $this->_colVotsPos = $ColVots;
        }

        public function getAllVotsPos()
        {
            return $this->_colVotsPos;
        }


        //Metodes per vots negatius

        public function addVotNeg($id, $idComentari, $Usuari, $data)
        {
            $this->_colVotsNeg[] = new Vot($id ,$idComentari,$Usuari,$data);
        }

        public function addVotsNeg($ColVots){
            $this->_colVotsNeg = $ColVots;
        }

        public function getAllVotsNeg()
        {
            return $this->_colVotsNeg;
        }

        //Metode per retorna els vots totals

        public function getAllVots(){
            return count($this->_colVotsPos)-count($this->_colVotsNeg);

        }

        public function addFills($ColFills){
            $this->_colFills = $ColFills;
        }

        public function getAllFills()
        {
            return $this->_colFills;
        }

}