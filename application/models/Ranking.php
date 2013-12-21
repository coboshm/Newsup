<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ranking
 *
 * @author Alex
 */
require_once 'BD/Users.php';

class Ranking {
    
    private $_user;
    private $_puntos;
    private $_contadorNoticias;
    
    
    function __construct($user, $puntos, $contadorNoticias) {
        $this->_Users = new Users();
        $this->_user = $this->_Users->getUsuariTot($user);
        $this->_puntos = $puntos;
        $this->_contadorNoticias = $contadorNoticias;
    }
    
    public function getUser() {
        return $this->_user;
    }

    public function setUser($user) {
        $this->_user = $user;
    }

    public function getPuntos() {
        return $this->_puntos;
    }

    public function setPuntos($puntos) {
        $this->_puntos = $puntos;
    }

    public function getContadorNoticias() {
        return $this->_contadorNoticias;
    }

    public function setContadorNoticias($contadorNoticias) {
        $this->_contadorNoticias = $contadorNoticias;
    }
    public function getMediaPuntos(){
        if($this->_puntos == 0){
            return 0;
        }else{
            return $this->_puntos / $this->_contadorNoticias;
        }
        
    }
    
    

}

?>
