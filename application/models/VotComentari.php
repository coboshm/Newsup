<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Comentari.php';
require_once 'Usuari.php';

class VotComentari
{

        private $_id;
        private $_idComentari;
        private $_idUsuari;
        private $_data;

        public function __construct($id, $idComentari, $idUsuari, $data)
        {
            $this->_id = $id;
            $this->_idComentari = $idComentari;
            $this->_idUsuari = $idUsuari;
            $this->_data = $data;

        }

        public function getidComentari()
        {
            return $this->_idComentari;
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