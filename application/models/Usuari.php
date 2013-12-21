<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Noticia.php';

class Usuari
{

        private $_id;
        private $_nom;
        private $_email;
        private $_username;
        private $_image;
        private $_poblacio;
        private $_tuvida;
        private $_aficiones;
        private $_dataNeixament;
        private $_colNoticies;
        private $_colSeguidors;
        private $_colSegueixen;
        private $_activat;
        private $_codi;

        public function __construct($id,$nom,$email,$username,$image,$localidad=null,$tuvida=null,$aficiones=null,$dataNeixament=null,$codi=null,$activat=null)
        {

            $this->_id = $id;
            $this->_email = $email;
            $this->_nom = $nom;
            $this->_username = $username;
            $this->_image = $image;
            $this->_poblacio = $localidad;
            $this->_tuvida = $tuvida;
            $this->_aficiones = $aficiones;
            $this->_dataNeixament = $dataNeixament;
            $this->_activat = $activat;
            $this->_codi = $codi;

        }

        public function getCodi(){
            return $this->_codi;
        }
        public function getActivat(){
            return $this->_activat;
        }

        public function getDataNeixament(){
            return $this->_dataNeixament;
        }

        public function getTuVida(){
            return $this->_tuvida;
        }

        public function getAficiones(){
            return $this->_aficiones;
        }

        public function getPoblacio(){
            return $this->_poblacio;
        }

        public function getUsername()
        {
            return $this->_username;
        }


        public function getId()
        {
            return $this->_id;
        }

        public function getNom()
        {
            return $this->_nom;
        }

        public function getEmail()
        {
            return $this->_email;
        }

        public function getImage()
        {
            return $this->_image;
        }
        
        public function addNoticies($ColNoticia){
            $this->_colNoticies = $ColNoticia;
        }

        public function getNumNoticies()
        {
            return $this->_colNoticies;
        }

        public function addSegueixen($ColSeguidors){
            $this->_colSeguidors = $ColSeguidors;
        }

        public function getNumSeguidors()
        {
            return $this->_colSeguidors;
        }

        public function addSegueix($ColSegueixen){
            $this->_colSegueixen = $ColSegueixen;
        }

        public function getNumSegueix()
        {
            return $this->_colSegueixen;
        }

       public function getNomCognoms(){
            return $this->getNom();
        }

        public function getNomCognom(){
            return $this->getNom();
        }



}
