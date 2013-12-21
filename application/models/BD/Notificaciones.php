<?php
require_once 'Notificacion.php';
require_once 'BD/TipoNotificacion.php';
require_once 'BD/Users.php';
require_once 'Usuari.php';

class Notificaciones extends Zend_Db_Table
{
    protected $_name = "notificaciones";
    protected $_primary = "id";
    private $_TipoNotificacion;
    
    public function insertNotificacion($notificacio)
    {
        $this->insert($notificacio);
    }

    public function setSugerencia($idNoticia, $idUsuariRep, $idUsuariFa){
        $projecte = $this->createRow();
        $projecte->idTipus = 2;
        $projecte->idUsuariEnv = $idUsuariFa;
        $projecte->idUsuariRec = $idUsuariRep;
        $projecte->idNoticia = $idNoticia;
        $projecte->data = @date('Y-m-d H:i:s');
        $projecte->save();
    }
    
    public function getSugerencias($idNoticia,$idUsuariFa){
            $consulta = $this->select();
            $consulta->where("idNoticia = '".addslashes($idNoticia)."'");
            $consulta->where("idTipus = 2");
            $consulta->where("idUsuariEnv = '".addslashes($idUsuariFa)."'");
            $resultat = $this->fetchAll($consulta);
            return $resultat->count();
    }

    public function getSeguidor($idUsuariFa,$idUsuariRep,$datatime){
            
            $consulta = $this->select();
            $consulta->where("idUsuariRec = '".addslashes($idUsuariRep)."'");
            $consulta->where("idTipus = 3");
            $consulta->where("data >= ?", $datatime);
            $consulta->where("idUsuariEnv = '".addslashes($idUsuariFa)."'");
            $resultat = $this->fetchAll($consulta);
            return $resultat->count();
    }
    
    public function getNotificacionByUser($id)
    {
        $consulta = $this->select();
        $consulta->where("idUsuariRec = $id");
        $notificacionesRow = $this->fetchAll($consulta);
        $notificaciones = array();
        foreach ($notificacionesRow as $notificacionRow) {
            $notificaciones[] = new Notificacion($notificacionRow->id, $notificacionRow->idUsuariEnv, $notificacionRow->idUsuariRec, $tipoRow->Nom, $tipoRow->Id,$notificacionRow->visto, $notificacionRow->idNoticia, $notificacionRow->idComentari);
        }
        return $notificaciones;
    }

    public function getNotificacionByUserNoVisto($id)
    {
        $consulta = $this->select();
        $consulta->where("idUsuariRec = $id");
        $consulta->where("visto = 0");
        $consulta->order("data DESC");

        $notificacionesRow = $this->fetchAll($consulta);
        $this->_TipoNotificacion = new TipoNotificacion();
        $this->_Users = new Users();
        $tipoNotificacion = $this->_TipoNotificacion->allTipoNotificacion();

        $notificaciones = array();
        foreach ($notificacionesRow as $notificacionRow) {
            //Zend_Debug::dump($notificacionRow->idUsuariEnv);
            $usuariEnv = $this->_Users->getUsuariTot($notificacionRow->idUsuariEnv);
            $usuariRec = $this->_Users->getUsuariTot($notificacionRow->idUsuariRec);

            $tipus = $this->_TipoNotificacion->getTipoById($notificacionRow->idTipus);
            //Zend_Debug::dump($usuariEnv);

            $notificaciones[] = new Notificacion($notificacionRow->id, $usuariEnv, $usuariRec, $tipus->Nom, $notificacionRow->idTipus,$notificacionRow->data,  $notificacionRow->visto, $notificacionRow->idNoticia, $notificacionRow->idComentari);
            $usuariEnv = null;
            $usuariRec = null;
            $tipus = null;

        }
   
        /*Zend_debug::Dump($notificaciones);
        die;*/
        return $notificaciones;
    }

    public function updateVisto($id)
    {
        $visto = array (
            'visto' => true
        );
        $this->update($visto,"Id = $id");
    }

    public function cantitatNotificaciones($id){
        $consulta = $this->select();
        $consulta->where("idUsuariRec = $id");
        $notificacionesRow = $this->fetchAll($consulta);
        return count($notificacionesRow);
    }

    public function allNotificaciones($id,$y)
    {
        $consulta = $this->select();
        $consulta->where("idUsuariRec = $id");
        $consulta->order("data DESC");
        //$select->order("data DESC");
        $consulta->limit($y,0);
        
        $notificacionesRow = $this->fetchAll($consulta);
        $this->_TipoNotificacion = new TipoNotificacion();
        $this->_Users = new Users();


        $notificaciones = array();
        foreach ($notificacionesRow as $notificacionRow) {
            $usuariEnv = $this->_Users->getUsuariTot($notificacionRow->idUsuariEnv);
            $usuariRec = $this->_Users->getUsuariTot($notificacionRow->idUsuariRec);

            $tipus = $this->_TipoNotificacion->getTipoById($notificacionRow->idTipus);

            $notificaciones[] = new Notificacion($notificacionRow->id, $usuariEnv, $usuariRec, $tipus->Nom, $notificacionRow->idTipus,$notificacionRow->data , $notificacionRow->visto,$notificacionRow->idNoticia, $notificacionRow->idComentari);


        }
        /*Zend_debug::Dump($notificaciones);
        die;*/
        return $notificaciones;
    }

    public function clearNotificacions($id)
    {
        $visto = array (
            'visto' => true
        );
        $this->update($visto,"idUsuariRec = $id");
    }



}
