<?php

class Notificacion
{
    private $_id;
    private $_idUsuariEnv;
    private $_idUsuariRec;
    private $_tipus;
    private $_idTipus;
    private $_idNoticia;
    private $_idComentari;
    private $_data;
    private $_visto;
    
    function __construct( $id , $idUsuariEnv, $idUsuariRec, $tipus, $idTipus,$data ,$visto=null,$idNoticia = null, $idComentari = null)
    {
        
        $this->_id = $id;
        $this->_idUsuariEnv = $idUsuariEnv;
        $this->_idUsuariRec = $idUsuariRec;
        $this->_tipus = $tipus;
        $this->_idTipus = $idTipus;
        $this->_idNoticia = $idNoticia;
        $this->_idComentari = $idComentari;
        $this->_data = new Zend_Date($data);
        /*Zend_debug::Dump($this->_data);
        die;*/
        $this->_visto = $visto;
        
    }

    
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getIdUsuariEnv() {
        return $this->_idUsuariEnv;
    }

    public function setIdUsuariEnv($idUsuariEnv) {
        $this->_idUsuariEnv = $idUsuariEnv;
    }

    public function getIdUsuariRec() {
        return $this->_idUsuariRec;
    }

    public function setIdUsuariRec($idUsuariRec) {
        $this->_idUsuariRec = $idUsuariRec;
    }

    public function getTipus() {
        return $this->_tipus;
    }
    public function getIdTipus() {
        return $this->_idTipus;
    }

    public function setIdtipus($idtipus) {
        $this->_idtipus = $idtipus;
    }

    public function getIdNoticia() {
        return $this->_idNoticia;
    }

    public function setIdNoticia($idNoticia) {
        $this->_idNoticia = $idNoticia;
    }

    public function getIdComentari() {
        return $this->_idComentari;
    }

    public function setIdComentari($idComentari) {
        $this->_idComentari = $idComentari;
    }

    public function getData() {
        return $this->_data;
    }
    
    public function getData2() {
        $dataHoi = new Zend_Date();
        $diasAno = 0;
        $diasActual = 0;
        if($this->_data->get('YYYY') != $dataHoi->get('YYYY')){
            $contador =  $dataHoi->get('YYYY') - $this->_data->get('YYYY');
            for($x = 0; $x < $contador; $x++){
                if($contador == 1){
                    if($this->_data->get(Zend_Date::MONTH) > $dataHoi->get(Zend_Date::MONTH)){
                        $diasAno += 365 + ($this->_data->get(DAY_OF_YEAR) - $dataHoi->get(DAY_OF_YEAR));
                    } elseif($this->_data->get(Zend_Date::MONTH) < $dataHoi->get(Zend_Date::MONTH)) {
                        $diasAno += 365 - $dataHoi->get(Zend_Date::DAY_OF_YEAR);
                    }else{
                        if($this->_data->get(Zend_Date::DAY) < $dataHoi->get(Zend_Date::DAY)){
                            
                            $diasAno += 365 - ($dataHoi->get(Zend_Date::DAY_OF_YEAR) - $this->_data->get(Zend_Date::DAY_OF_YEAR));
                            /*echo $dataHoi->get(Zend_Date::DAY_OF_YEAR);
                            echo "<br>";
                            echo $this->_data->get(Zend_Date::DAY_OF_YEAR);
                            echo "<br>";*/
                            
                            //Zend_debug::Dump($dataHoi->get(Zend_Date::DAY_OF_YEAR));
                        }elseif($this->_data->get(Zend_Date::DAY) > $dataHoi->get(Zend_Date::DAY)){
                            $diasAno += 365 + ($this->_data->get(Zend_Date::DAY) - $dataHoi->get(Zend_Date::DAY));
                        }else{
                            $diasAno += 365;
                        }
                    }
                    return abs($diasAno);
                }else{
                    $diasAno += 365;
                }
            }
        }else{
            $diasAno += $this->_data->get(Zend_Date::DAY_OF_YEAR);
            $diasActual = $dataHoi->get(Zend_Date::DAY_OF_YEAR);
            $diferenciaDias = $diasAno - $diasActual;
            return abs($diferenciaDias);
        }
        //die;
        
    }
    /*public function setData($data) {
        $this->_data = $data;
    }*/


}