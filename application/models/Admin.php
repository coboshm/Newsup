<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'BD/AdminsTaula.php';

class Admin
{

        private $_id;
        private $_username;
        private $_password;
        private $_nom;
        private $_email;

        public function __construct($id, $username, $password, $nom, $email)
        {
            $this->_id = $id;
            $this->_username = $username;
            $this->_password = $password;
            $this->_nom = $nom;
            $this->_email = $email;
        }

        public function getId()
        {
            return $this->_id;
        }

        public function getUsername()
        {
            return $this->_username;
        }

        public function getPassword()
        {
            return $this->_password;
        }

        public function getNom()
        {
            return $this->_nom;
        }

        public function getEmail()
        {
            return $this->_email;
        }
}