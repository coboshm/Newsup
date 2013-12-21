<?php


class NoticiaDiari
{

        private $_titul;
        private $_descripcio;
        private $_datatime;
        private $_imatge;
        private $_url;
        private $_nomDiari;

        public function __construct($titul, $descripcio,$data,$url,$imatge=null)
        {

            $this->_titul = $titul;
            $this->_datatime = $data;
            $this->_descripcio = $descripcio;
            $this->_url = $url;
            $this->_imatge = $imatge;


        }

        public function setNomDiari($nomDiari){
            $this->_nomDiari = $nomDiari;
        }

        public function getNomDiari(){
            return $this->_nomDiari;
        }


        public function getImatge()
        {
            return $this->_imatge;
        }

        public function getDescripcio()
        {
            return $this->_descripcio;
        }

        public function getDescripcio180(){
            if(strlen($this->_descripcio)<=280){
                return $this->_descripcio;
            }else{
                return substr($this->_descripcio,0,280)."...";
            }
        }

        public function getTitul()
        {
            return $this->_titul;
        }

        public function getTitul200(){
            if(strlen($this->_titul)<=130){
                return $this->_titul;
            }else{
                return substr($this->_titul,0,130)."...";
            }
        }

        public function getData()
        {
            
            $data = new Zend_Date($this->_datatime);
            //Zend_Debug::dump($data);
            
            return $data->toString("dd-MM-YYYY")." a las ".$data->toString("HH:mm:ss");
        }

        public function unDia(){
            @$data1dia= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
            @$start = strtotime($this->_datatime);
            @$end = strtotime($data1dia);
            if ($start-$end >0){
              return 1;
            }else{
              return 0;
            }
        }

        public function unaSetmana(){
            @$data1setmana= date('Y/m/d/ H:i:s',strtotime("-1 week")) ;
            @$start = strtotime($this->_datatime);
            @$end = strtotime($data1setmana);
            if ($start-$end > 0){
              return 1;
            }else{
              return 0;
            }
        }

        public function unMes(){
            @$data1mes= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
            @$start = strtotime($this->_datatime);
            @$end = strtotime($data1mes);
            if ($start-$end > 0){
              return 1;
            }else{
              return 0;
            }
        }


        public function getUrl()
        {
            return $this->_url;
        }

}
