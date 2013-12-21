<?php

require_once 'Usuari.php';
require_once 'Categoria.php';
require_once 'Vot.php';

class Noticia
{

        private $_id;
        private $_Categoria;
        private $_titul;
        private $_descripcio;
        private $_Usuari;
        private $_datatime;
        private $_imatge;
        private $_url;
        private $_colVots = array();
        private $_colComentaris = array();
        private $_numComentaris;
        private $_latitude;
        private $_longitude;

        public function __construct($id, $categoria, $titul, $descripcio, $usuari, $data,$url,$imatge,$lat,$long)
        {

            $this->_id = $id;
            $this->_titul = $titul;
            $this->_datatime = $data;
            $this->_descripcio = $descripcio;
            $this->_Categoria = $categoria;
            $this->_Usuari = $usuari;
            $this->_url = $url;
            $this->_imatge = $imatge;
            $this->_latitude = $lat;
            $this->_longitude = $long;

        }

        public function getLatitude(){
            return $this->_latitude;
        }

        public function getLongitude(){
            return $this->_longitude;
        }

        public function getLatlong(){
            return "(".$this->_latitude.",".$this->_longitude.")";
        }

        public function getId()
        {
            return $this->_id;
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
                $num = strlen($this->_descripcio);
                $text = str_replace("\n","", $this->_descripcio);
                $text = str_replace("\r","", $text);
                return substr($text,0,$num-3)."...";
                //return $this->_descripcio;
            }else{
                $text = str_replace("\n","", $this->_descripcio);
                $text = str_replace("\r","", $text);
                return substr($text,0,280)."...";
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

        public function getCategoria()
        {
            return $this->_Categoria;
        }

        public function getData()
        {
            @$dataHora = explode(" ",$this->_datatime);
            @$dataTrosos = explode("-",$dataHora[0]);

            return $dataTrosos[2]."-".$dataTrosos[1]."-".$dataTrosos[0]." a las ".$dataHora[1];
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

        public function getUsuari()
        {
            return $this->_Usuari;
        }

        public function getUrl()
        {
            return $this->_url;
        }

        public function addVot($id, $idNoticia, $Usuari, $data)
        {
            $this->_colVots[] = new Vot($id ,$idNoticia,$Usuari,$data);
        }

        public function addVots($ColVots){
            $this->_colVots = $ColVots;
        }

        public function getAllVots()
        {
            return $this->_colVots;
        }

        public function getNumVots(){
            return count($this->_colVots);
        }

        /*public function addComent($id, $idNoticia, $Usuari, $data)
        {
            $this->_colComentariss[] = new Vot($id ,$idNoticia,$Usuari,$data);
        }*/

        public function addComents($ColComentaris){
            $this->_colComentaris = $ColComentaris;
        }

        public function setNumComentaris($numComentaris){
            $this->_numComentaris = $numComentaris;
        }

        public function getNumComentaris2(){
            return $this->_numComentaris;
        }
        public function getAllComentaris()
        {
            return $this->_colComentaris;
        }

        public function getNumComentaris(){
            $x=0;
            foreach($this->_colComentaris as $comment){
                $x++;
                foreach($comment->getAllFills() as $fill){
                    $x++;
                    foreach($fill->getAllFills() as $fill2){
                        $x++;
                        foreach($fill2->getAllFills() as $fill3){
                            $x++;
                        }
                    }
                }
            }
            return $x;
        }

        public function getUrlTitul(){
            $titul = substr($this->_titul,0,90);
            $titul2 = str_replace(" ","_", $titul);
            $titul3 = str_replace("-","_", $titul2);
            $titul4 = str_replace("?","", $titul3);
            $titul = str_replace("¿","", $titul4);
            $titul = str_replace("\"","_", $titul);
            $titul = str_replace("\'","", $titul);
            $titul = str_replace("/","", $titul);
            $titul = str_replace("'","", $titul);
            $titul = str_replace(".","", $titul);
            $titul = addslashes($titul."-".$this->_id);
            $titul = str_replace("%","",$titul);
            return $titul;
        }
}
