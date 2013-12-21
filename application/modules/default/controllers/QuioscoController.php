<?php
require_once 'BD/UserModel.php';
require_once 'BD/VotsTaula.php';
require_once 'Vot.php';
require_once 'BD/NoticiaTaula.php';
require_once 'BD/Users.php';
require_once 'Noticia.php';
require_once 'BD/Categories.php';
require_once 'BD/Seguidors.php';
require_once 'BD/Contactar.php';
require_once 'Categoria.php';
require_once 'Comentari.php';
require_once 'BD/ComentarisTaula.php';
require_once 'BD/NoticiesFavoritas.php';
require_once 'Notificacion.php';
require_once 'BD/Notificaciones.php';
require_once 'BD/TipoNotificacion.php';
require_once 'BD/diarisRss.php';
require_once 'BD/tipusDiari.php';
require_once 'BD/tipusMedis.php';
require_once 'Diari.php';
require_once 'NoticiaDiari.php';

class QuioscoController extends Zend_Controller_Action
{

    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_NoticiesFavoritas;
    private $_Seguidors;
    private $_Contactar;
    private $_Notificaciones;
    private $_diarisRss;
    private $_tipusDiari;
    private $_tipusMedis;

    public function init()
    {
        /* Initialize action controller here */
         $this->_Users = new Users();
         $this->_VotsTaula = new VotsTaula();
         $this->_Categories = new Categories();
         $this->_NoticiaTaula = new NoticiaTaula();
         $this->_ComentarisTaula = new ComentarisTaula();
         $this->_NoticiesFavoritas = new NoticiesFavoritas();
         $this->_Seguidors = new Seguidors();
         $this->_Contactar = new Contactar();
         $this->_Notificaciones = new Notificaciones();
         $this->_diarisRss = new diarisRss();
         $this->_tipusDiari = new tipusDiari();
         $this->_tipusMedis = new tipusMedis();


    }
    
    public function preDispatch(){
        if (UserModel::isLoggedIn()) {
                $this->view->loggedIn = true;
                $this->view->user = UserModel::getIdentity();
                $user = UserModel::getIdentity();
                $usuari = $this->_Users->getUsuari($user->id_user);
                if(count($usuari)==1){
                    foreach($usuari as $user1){
                        $this->view->usuario = $user1;
                    }
                }
                //ALEX---------------------------------------NOTIFICACIONES
                $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                $this->view->notficaciones = $notificaciones;


                $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                //Zend_Debug::dump(UserModel::getIdentity());
        }else{
            $cookie = $this->getRequest()->getCookie('newsup');
            if($cookie!=null){
                $user = $this->_Users->getDadesByCookie($cookie);
                @$usuari = explode("-",$user);

                $si = true;
                $responseLogin=null;


                $user2 = $this->_Users->getDadesByUsername($usuari[0]);

                if($user2->getActivat()==0){
                    $responseLogin = "Este Usuario no ha sido Activado Correctamente.";
                    $this->_redirect("index/index/responseLogin/$responseLogin");
                }
                //Zend_Debug::dump($email);
                //Zend_Debug::dump($password);
                //$encript_password = sha1($password);
                try{
                    $user = new UserModel();
                    $user->setMessage('El nombre de usuario introducido es incorrecto.', UserModel::NOT_IDENTITY);
                    $user->setMessage('La contraseña ingresada es incorrecta. Inténtelo de nuevo.', UserModel::INVALID_CREDENTIAL);
                    $user->setMessage('Los campos de Usuario y Password no pueden dejarse en blanco.', UserModel::INVALID_LOGIN);
                    $user->login($usuari[0],$usuari[1]);
                } catch(Exception $e){
                    $responseLogin = $e->getMessage();
                    //$this->view->responseLogin = $responseLogin;
                    //Zend_Debug::dump($responseLogin);

                }
                if($responseLogin==null){
                    $sessio = $this->_request->getParam("mantenirOpen");
                    if($sessio==true){
                        $contingutCookie = sha1($user2->getEmail().$user2->getId().$user2->getUsername());
                        //Zend_Debug::dump($contingutCookie);
                        //Zend_Debug::dump(sha1($user2->getEmail().$user2->getId().$user2->getUsername()));
                        $this->_Users->setCookie($contingutCookie,$user2->getId());
                        setcookie("newsup", $contingutCookie,time()+60*60*24*31,"/");
                        //Zend_Debug::dump($cookie);
                        //die();
                    }
                }

                if (UserModel::isLoggedIn()) {
                    $this->view->loggedIn = true;
                    $this->view->user = UserModel::getIdentity();
                    $user = UserModel::getIdentity();
                    $usuari = $this->_Users->getUsuari($user->id_user);
                    if(count($usuari)==1){
                        foreach($usuari as $user1){
                            $this->view->usuario = $user1;
                        }
                    }
                    //ALEX---------------------------------------NOTIFICACIONES
                    $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                    $this->view->notficaciones = $notificaciones;


                    $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                    //Zend_Debug::dump(UserModel::getIdentity());
                }
            }
        }
        $this->view->mostra = 5;
        $this->view->feed = 1;
        $this->view->metanom = "Quiosco";
        $this->view->llistaCategories = $this->_Categories->getCategories();
        //$this->view->ranking = $this->_NoticiaTaula->allNoticiasRanking();
    }

    public function indexAction()
    {
        //$feed = new Zend_Feed_Rss('http://feeds.feedburner.com/mundodeportivo-futbol-primera-titulares');
        //$this->view->feed = $feed;
        $feedDiaris = $this->_diarisRss->getDiaris();
        $this->view->feedDiaris = $feedDiaris;
        $this->view->tipus = $this->_tipusDiari->getTipus();
        $this->view->medis = $this->_tipusMedis->getTipus();

        $this->render();
              
    }

    public function getdiarisAction(){

        $dia = $this->_request->getParam("dia");
        $diarisRebuts = explode(",",$dia);
        
        $feedDiaris = $this->_diarisRss->getDiarisDins($diarisRebuts);
        $this->view->feedDiaris = $feedDiaris;
        $contador2 = 40 / count($feedDiaris);
        
        $noticiesFinals = array();
        $contador1 =45;
        $cont=0;

        foreach($feedDiaris as $diaris){
            //Zend_Debug::dump($diaris);
            //Zend_Debug::dump($diaris->getUrl());
            //die();
            $feed2 = new Zend_Feed_Rss($diaris->getUrl());
            
            if($contador1==0){
                break;
            }
            foreach($feed2 as  $feed){
                if($cont>$contador2 || $contador1==0){
                    break;
                }


                if($feed->title() != null && !is_array($feed->title())){
                    //Zend_Debug::dump($feed->title());
                   
                      // Zend_Debug::dump($feed->description());
                                @$resultImg = explode(" src=\"",$feed->description());
                                $piece3 = array();
                                //Zend_Debug::dump($resultImg);
                                if(@$resultImg[1]!=null){

                                    @$imatge = explode("\"",$resultImg[1]);
                                    if(substr($imatge[0],-3)=="jpg"){
                                    $imatgeFinal = $imatge[0];

                                    //Zend_Debug::dump($imatgeFinal);
                                    }
                                }
                                $urlinici = "http://";
                                $resultImg=null;
                                if(@$imatgeFinal==null){
                                    @$resultImg = explode(" src='",$feed->description());
                                    $piece3 = array();

                                    //Zend_Debug::dump($resultImg);
                                    //Zend_Debug::dump($resultImg);
                                    if(@$resultImg[1]!=null){

                                        @$imatge = explode("'",$resultImg[1]);
                                        if(substr($imatge[0],-3)=="jpg"){

                                        $imatgeFinal = $imatge[0];

                                        //Zend_Debug::dump($imatgeFinal);
                                        }
                                    }
                                }


                                @$descripcionSenseTags = explode ("<img ",$feed->description());
                                $descripcio2 = $descripcionSenseTags[0];
                                //Zend_Debug::dump(count($descripcionSenseTags));
                                for($x=1;$x<count($descripcionSenseTags);$x++){
                                    @$descripcionSenseTags2 = explode ("\>",$descripcionSenseTags[$x]);
                                    @$descripcio2.= $descripcionSenseTags2[1];
                                }


                                @$descripcioTagsA = explode ("<a",$descripcio2);
                                $descripcio3 = $descripcioTagsA[0];
                                for($x=1;$x<count($descripcioTagsA);$x++){
                                    @$descripcionSenseTagsA2 = explode ("\>",$descripcioTagsA[$x]);
                                    @$descripcio3.= $descripcionSenseTagsA2[1];
                                }

                                $utf = $this->isUTF8($feed->description());
                                if($utf==false){
                                    $descripcionFinalOk = utf8_encode(strip_tags($feed->description()));
                                }else{
                                    $descripcionFinalOk = strip_tags($feed->description());
                                }
                                //Zend_Debug::dump($descripcionFinalOk);



                                if($feed->link()!=null){
                                    
                                        

                                        $dataNoticia = new Zend_Date($feed->pubDate());

                                        if(@$imatgeFinal!=null){
                                        $noticiesFinals[] = new NoticiaDiari($feed->title(),$descripcionFinalOk,$feed->pubDate(),$feed->link(),$imatgeFinal);
                                        }else{
                                        $noticiesFinals[] = new NoticiaDiari($feed->title(),$descripcionFinalOk,$feed->pubDate(),$feed->link());
                                        }

                                        //$cont++;
                                        $imatgeFinal=null;
                                        $descripcionFinalOk = null;
                                        $contador1--;
                                        $cont++;
                                        
                                    

                                }
                    
                }
            }
            $cont=0;
            $diaris->addNoticies($noticiesFinals);
            
            $noticiesFinals = null;
        }
        
        $this->view->noticiesFinals = $feedDiaris;
        $this->_helper->layout()->disableLayout();
    }

    public function getdiarisdataAction(){
        $dia = $this->_request->getParam("dia");
        $diarisRebuts = explode(",",$dia);

        $feedDiaris = $this->_diarisRss->getDiarisDins($diarisRebuts);
        $this->view->feedDiaris = $feedDiaris;
        $contador2 = 40 / count($feedDiaris);

        $noticiesFinals = array();
        $contador1 =45;
        $cont=0;
        $contadorNoticies = 0;

        foreach($feedDiaris as $diaris){
            $feed2 = new Zend_Feed_Rss($diaris->getUrl());

            if($contador1==0){
                break;
            }
            foreach($feed2 as  $feed){
                if($cont>$contador2 || $contador1==0){
                    break;
                }


                if($feed->title() != null && !is_array($feed->title())){
                    //Zend_Debug::dump($feed->title());

                      // Zend_Debug::dump($feed->description());
                                @$resultImg = explode(" src=\"",$feed->description());
                                $piece3 = array();
                                //Zend_Debug::dump($resultImg);
                                if(@$resultImg[1]!=null){

                                    @$imatge = explode("\"",$resultImg[1]);
                                    if(substr($imatge[0],-3)=="jpg"){
                                    $imatgeFinal = $imatge[0];

                                    //Zend_Debug::dump($imatgeFinal);
                                    }
                                }

                                $resultImg=null;
                                if(@$imatgeFinal==null){
                                    @$resultImg = explode(" src='",$feed->description());
                                    $piece3 = array();

                                    //Zend_Debug::dump($resultImg);
                                    //Zend_Debug::dump($resultImg);
                                    if(@$resultImg[1]!=null){

                                        @$imatge = explode("'",$resultImg[1]);
                                        if(substr($imatge[0],-3)=="jpg"){
                                        $imatgeFinal = $imatge[0];

                                        //Zend_Debug::dump($imatgeFinal);
                                        }
                                    }
                                }


                                @$descripcionSenseTags = explode ("<img ",$feed->description());
                                $descripcio2 = $descripcionSenseTags[0];
                                //Zend_Debug::dump(count($descripcionSenseTags));
                                for($x=1;$x<count($descripcionSenseTags);$x++){
                                    @$descripcionSenseTags2 = explode ("\>",$descripcionSenseTags[$x]);
                                    @$descripcio2.= $descripcionSenseTags2[1];
                                }


                                @$descripcioTagsA = explode ("<a",$descripcio2);
                                $descripcio3 = $descripcioTagsA[0];
                                for($x=1;$x<count($descripcioTagsA);$x++){
                                    @$descripcionSenseTagsA2 = explode ("\>",$descripcioTagsA[$x]);
                                    @$descripcio3.= $descripcionSenseTagsA2[1];
                                }


                                $utf = $this->isUTF8($feed->description());
                                if($utf==false){
                                    $descripcionFinalOk = utf8_encode(strip_tags($feed->description()));
                                }else{
                                    $descripcionFinalOk = strip_tags($feed->description());
                                }
              
                                if($feed->link()!=null){
                        
                                        $dataNoticia = new Zend_Date($feed->pubDate());

                                        if(@$imatgeFinal!=null){
                                            $noticiesFinals[$contadorNoticies] = new NoticiaDiari($feed->title(),$descripcionFinalOk,$feed->pubDate(),$feed->link(),$imatgeFinal);
                                        }else{
                                            $noticiesFinals[$contadorNoticies] = new NoticiaDiari($feed->title(),$descripcionFinalOk,$feed->pubDate(),$feed->link());
                                        }

                                        $noticiesFinals[$contadorNoticies]->setNomDiari($diaris->getNom());

                                        //$cont++;
                                        $imatgeFinal=null;
                                        $descripcionFinalOk = null;
                                        $contador1--;
                                        $contadorNoticies++;
                                        $cont++;
                                        
                              
                                }

                }
            }
            $cont=0;

        }
        
        $n = count($noticiesFinals);
        //Zend_Debug::dump($n);
        for ($i = 1; $i<$n; $i++) {
            for ($j = $n-1; $j >= $i; $j--) {
                    //Zend_Debug::dump($noticiesFinals[$j-1]->getData());
                    //Zend_Debug::dump($noticiesFinals[$j]->getData());
                if ($noticiesFinals[$j-1]->getData() < $noticiesFinals[$j]->getData()) {
                    //Zend_Debug::dump($noticiesFinals[$j-1]->getData());
                    //Zend_Debug::dump($noticiesFinals[$j]->getData());
                    $aux = $noticiesFinals[$j];
                    $noticiesFinals[$j] = $noticiesFinals[$j-1];
                    $noticiesFinals[$j-1] = $aux;
                }
            }
        }
        //Zend_Debug::dump($noticiesFinals);
        //die();
        $this->view->noticiesFinals = $noticiesFinals;
        $this->_helper->layout()->disableLayout();

    }

     public function isUTF8($string){

        for ($idx = 0, $strlen = strlen($string); $idx < $strlen; $idx++)
        {
          $byte = ord($string[$idx]);

          if ($byte & 0x80)
          {
            if (($byte & 0xE0) == 0xC0)
            {
              // 2 byte char
              $bytes_remaining = 1;
            }
            else if (($byte & 0xF0) == 0xE0)
            {
              // 3 byte char
              $bytes_remaining = 2;
            }
            else if (($byte & 0xF8) == 0xF0)
            {
              // 4 byte char
              $bytes_remaining = 3;
            }
            else
            {
              return false;
            }

            if ($idx + $bytes_remaining >= $strlen)
            {
              return false;
            }

            while ($bytes_remaining--)
            {
              if ((ord($string[++$idx]) & 0xC0) != 0x80)
              {
                return false;
              }
            }
          }
        }

        return true;
  }

 public function getdiaristipusAction(){
    $tipus = $this->_request->getParam("tipus");
    $medi = $this->_request->getParam("medi");
    if($tipus!=6){
        if($medi!=4){
            $feedDiaris = $this->_diarisRss->getDiarisTipusIdioma($tipus , $medi);
        }else{
            $feedDiaris = $this->_diarisRss->getDiarisTipus($tipus);
        }
    }else{
        if($medi!=4){
            $feedDiaris = $this->_diarisRss->getDiarisIdioma($medi);
        }else{
            $feedDiaris = $this->_diarisRss->getDiaris();
        }
    }
    $this->view->feedDiaris = $feedDiaris;
    $this->_helper->layout()->disableLayout();
  }

  public function provaAction(){
      $feed2 = new Zend_Feed_Rss("http://rss.computerworld.com/computerworld/news/feed?format=xml");
      $this->view->feed2 = $feed2;
  }


}

