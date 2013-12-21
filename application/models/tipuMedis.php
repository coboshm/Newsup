<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'BD/tipusDiari.php';

class idiomaDiari
{

        private $_id;
        private $_nom;

        public function __construct($id, $nom)
        {

            $this->_id = $id;
            $this->_nom = $nom;

        }

        public function getNom()
        {
            return $this->_nom;
        }

        public function getId()
        {
            return $this->_id;
        }
}