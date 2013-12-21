<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Noticia.php';
require_once 'BD/VotsTaula.php';
require_once 'Usuari.php';

class Vot
{

        private $_id;
        private $_idNoticia;
        private $_idUsuari;
        private $_data;

        public function __construct($id, $idNoticia, $idUsuari, $data)
        {
            $this->_id = $id;
            $this->_idNoticia = $idNoticia;
            $this->_idUsuari = $idUsuari;
            $this->_data = $data;

        }

        public function getidNoticia()
        {
            return $this->_idNoticia;
        }

        public function getidUsuari()
        {
            return $this->_idUsuari;
        }

        public function getData()
        {
            return $this->_data;
        }

        public function getId()
        {
            return $this->_id;
        }
}