<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'BD/diarisRss.php';

class Diari
{

        private $_id;
        private $_nom;
        private $_url;
        private $_favicon;
        private $_idioma;
        private $_noticies = array();
        private $_tipu;

        public function __construct($id, $nom, $favicon, $url, $idioma)
        {

            $this->_id = $id;
            $this->_nom = $nom;
            $this->_url = $url;
            $this->_favicon = $favicon;
            $this->_idioma = $idioma;


        }

        public function getFavicon()
        {
            return $this->_favicon;
        }

        public function getUrl()
        {
            return $this->_url;
        }

        public function getIdioma()
        {
            return $this->_idioma;
        }

        public function getNom()
        {
            return $this->_nom;
        }

        public function getId()
        {
            return $this->_id;
        }

        public function addNoticies($ColNoticies){
            $this->_noticies = $ColNoticies;
        }


        public function getAllNoticies()
        {
            return $this->_noticies;
        }

        public function setTipu($tipus){
            $this->_tipu = $tipus;
        }

        public function getTipu(){
            return $this->_tipu;
        }
}