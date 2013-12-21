<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'BD/ContactarTaula.php';

class Contactar
{

        private $_id;
        private $_username;
        private $_nom;
        private $_email;
        private $_opcio;
        private $_missatge;

        public function __construct($id, $username, $nom, $email, $opcio, $missatge)
        {
            $this->_id = $id;
            $this->_username = $username;
            $this->_nom = $nom;
            $this->_email = $email;
            $this->_opcio = $opcio;
            $this->_missatge = $missatge;
        }

        public function getId()
        {
            return $this->_id;
        }

        public function getUsername()
        {
            return $this->_username;
        }

        public function getNom()
        {
            return $this->_nom;
        }

        public function getEmail()
        {
            return $this->_email;
        }

        public function getOpcio()
        {
            return $this->_opcio;
        }

        public function getMissatge()
        {
            return $this->_missatge;
        }

        public function getMissatge60(){
            if(strlen($this->_missatge)<=60){
                return $this->_missatge;
            }else{
                return substr($this->_missatge,0,60)."...";
            }
        }
        
}