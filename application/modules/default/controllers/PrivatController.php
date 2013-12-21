<?php
require_once 'BD/UserModel.php';
require_once 'BD/VotsTaula.php';
require_once 'Vot.php';
require_once 'BD/NoticiaTaula.php';
require_once 'BD/Users.php';
require_once 'Noticia.php';
require_once 'BD/Categories.php';
require_once 'BD/Seguidors.php';
require_once 'Categoria.php';
require_once 'Comentari.php';
require_once 'BD/ComentarisTaula.php';
require_once 'BD/VotsComentarisNeg.php';
require_once 'BD/VotsComentarisPos.php';
require_once 'BD/NoticiesFavoritas.php';
require_once 'Notificacion.php';
require_once 'BD/Notificaciones.php';
require_once 'BD/TipoNotificacion.php';

class PrivatController extends Zend_Controller_Action
{

    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_VotsComentarisNeg;
    private $_VotsComentarisPos;
    private $_NoticiesFavoritas;
    private $_Seguidors;
    private $_Notificaciones;

    public function init()
    {
        /* Initialize action controller here */
         $this->_Users = new Users();
         $this->_VotsTaula = new VotsTaula();
         $this->_Categories = new Categories();
         $this->_NoticiaTaula = new NoticiaTaula();
         $this->_ComentarisTaula = new ComentarisTaula();
         $this->_VotsComentarisNeg = new VotsComentarisNeg();
         $this->_VotsComentarisPos = new VotsComentarisPos();
         $this->_NoticiesFavoritas = new NoticiesFavoritas();
         $this->_Seguidors = new Seguidors();
         $this->_Notificaciones = new Notificaciones();

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
                        $this->view->dadesUser = $this->_Users->getDadesById($user->id_user);

                    }
                }

                //ALEX---------------------------------------NOTIFICACIONES
                $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                $this->view->notficaciones = $notificaciones;
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
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
                                $this->view->dadesUser = $this->_Users->getDadesById($user->id_user);

                            }
                        }

                        //ALEX---------------------------------------NOTIFICACIONES
                        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                        $this->view->notficaciones = $notificaciones;
                        $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
                        $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                        //Zend_Debug::dump(UserModel::getIdentity());
                }
            }
        }
        //$this->view->ranking = $this->_NoticiaTaula->allNoticiasRanking();
    }

    public function indexAction(){
        $this->view->mostra = 1;
        $x = 0;
        $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuari(UserModel::getIdentity(),$x);
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUser(UserModel::getIdentity());
        $this->view->pagina = 1;

        $responseLogin=null;
        $responseComent=null;
        $responseVotoFail=null;

        $responseLogin = $this->_request->getParam("responseLogin");
        $responseComent = $this->_request->getParam("responseComent");
        $responseVotoFail = $this->_request->getParam("responseVotoFail");

        //Zend_Debug::dump($responseLogin);
        $this->view->responseLogin = $responseLogin;
        $this->view->responseVotoFail = $responseVotoFail;
        $this->view->responseComent = $responseComent;
        //Zend_Debug::dump($this->_NoticiaTaula->getNoticiesUsuari(UserModel::getIdentity()));
        //$this->_helper->viewRenderer->setNoRender(true);
        //$this->_helper->layout()->disableLayout();
        //$cookie = $this->getRequest()->getCookie('newsup');
        
        

        $this->render();
    }

    public function votadasAction(){
        $x = 0;
        $this->view->pagina = 1;
        $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuariPunts(UserModel::getIdentity(),$x);
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUser(UserModel::getIdentity());
        $this->_helper->layout()->disableLayout();
    }

    public function recientesAction(){
        $x = 0;
        $this->view->pagina = 1;
        $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuari(UserModel::getIdentity(),$x);
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUser(UserModel::getIdentity());
        $this->_helper->layout()->disableLayout();
    }

    public function votaAction(){
        $id = $this->_request->getParam("id");
        $votsNoticia = $this->_VotsTaula->getVotsNoticia($id);
        $user = UserModel::getIdentity();
        $error = false;
        
        if(count($this->_Users->getUsuari($user->id_user))==1){
            foreach($this->_Users->getUsuari($user->id_user) as $user1){
                $usuari = $user1;
            }
        }
        foreach($votsNoticia as $vot){
            if($vot->getidUsuari()== $usuari->getId()){
                $error = true;
            }
        }

        if($error==true){
            $responseLogin = "Esta noticia ya ha sido votada por usted";
            $this->_redirect("index/index/responseVotoFail/$responseLogin");
        }else{
            @$dataHora = date("Y-m-d H:i:s");
            $usuari = UserModel::getIdentity();
            $this->_VotsTaula->afegirVot($id,$usuari,$dataHora);
            $this->view->noticia = $this->_NoticiaTaula->getNoticia($id);

            $this->_helper->layout()->disableLayout();
        }
        //$this->_helper->viewRenderer->setNoRender(true);
    }

    public function afegircomentariprivatAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);


        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("privat/index/responseComent/$responseComent");
    }

    public function afegircomentariAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);

        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $idUsuari = $noticia->getUsuari()->getId();
        if($usuari->id_user != $noticia->getUsuari()->getId()){
            $notificacion = array(
                'idUsuariEnv' => $usuari->id_user,
                'idUsuariRec' => $idUsuari ,
                'idTipus' => 4,
                'idNoticia' => $noticia->getId(),
                'idComentari' => $idComentari,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $pagina = $this->_request->getParam("pagina");
        $tipus = $this->_request->getParam("tipus");
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("index/paginaok/responseComent/$responseComent/pagina/$pagina/tipus/$tipus");
    }


    public function afegircomentarisaveAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);
        //$username = $this->_request->getParam("username");

        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $idUsuari = $noticia->getUsuari()->getId();
        if($usuari->id_user != $noticia->getUsuari()->getId()){
            $notificacion = array(
                'idUsuariEnv' => $usuari->id_user,
                'idUsuariRec' => $idUsuari ,
                'idTipus' => 4,
                'idNoticia' => $noticia->getId(),
                'idComentari' => $idComentari,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("privat/saved/responseComent/$responseComent");
    }

    public function afegircomentariuserAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);
        $username = $this->_request->getParam("username");

        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $idUsuari = $noticia->getUsuari()->getId();
        if($usuari->id_user != $noticia->getUsuari()->getId()){
            $notificacion = array(
                'idUsuariEnv' => $usuari->id_user,
                'idUsuariRec' => $idUsuari ,
                'idTipus' => 4,
                'idNoticia' => $noticia->getId(),
                'idComentari' => $idComentari,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("index/user/nomUser/$username/responseComent/$responseComent");
    }

     public function afegircomentariperfilAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);

        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $idUsuari = $noticia->getUsuari()->getId();
        if($usuari->id_user != $noticia->getUsuari()->getId()){
            $notificacion = array(
                'idUsuariEnv' => $usuari->id_user,
                'idUsuariRec' => $idUsuari ,
                'idTipus' => 4,
                'idNoticia' => $noticia->getId(),
                'idComentari' => $idComentari,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $pagina = $this->_request->getParam("pagina");
        $tipus = $this->_request->getParam("tipus");
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("privat/perfils/responseComent/$responseComent");
    }

    public function votacomentariupAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $this->_VotsComentarisPos->afegirVotPositiu($id,$usuari,$dataHora);
        $ComentariOk = $this->_ComentarisTaula->getComentari($id);

        if(count($ComentariOk)==1){
            foreach($ComentariOk as $coment){
                $this->view->comentari = $coment;
               // Zend_Debug::dump($noticia);
            }
        }

        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
    }

     public function votacomentaridownAction(){
        $id = $this->_request->getParam("id");
        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $this->_VotsComentarisNeg->afegirVotNegatiu($id,$usuari,$dataHora);
        $ComentariOk = $this->_ComentarisTaula->getComentari($id);

         if(count($ComentariOk)==1){
            foreach($ComentariOk as $coment){
                $this->view->comentari = $coment;
               // Zend_Debug::dump($noticia);
            }
        }

        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
    }

    public function editarAction(){

        $user = UserModel::getIdentity();
        $usuari = $this->_Users->getUsuari($user->id_user);
        if(count($usuari)==1){
            foreach($usuari as $user1){
                $this->view->usuario = $user1;
            }
        }
        
        $form = $this->_getFileForm();
        $form->setAction($this->_request->getBaseUrl()."/privat/afegirimatge");
        

        $this->view->mostra = 1;
        $this->view->actiu = 3;
        $this->view->form = $form;
        $this->render();
    }


    public function afegirimatgeAction(){


            $user = UserModel::getIdentity();
            $usuari = $this->_Users->getUsuari($user->id_user);
            if(count($usuari)==1){
                foreach($usuari as $user1){
                    $this->view->usuario = $user1;
                }
            }
            
            $form = $this->_getFileForm();
            if($this->getRequest()->isPost()) {
                $formData = $this->_request->getPost();

            if (!$form->isValid($formData)) {
                $this->view->form = $form;
	        $form->populate($formData);
                return $this->_redirect("privat/editar");
            }

            if (!$form->file->receive()) {
                //print "Error al rebre l'arxiu";
                return $this->_redirect("privat/editar");
            }

            $a = Array();
            if($a == $form->file->getFileName()){
                $this->view->form = $form;
                 return $this->_redirect("privat/editar");
            }else{
                $hash = $form->file->getHash('sha1');

                $ext = $this->_getExtension($form->file->getFileName());
                $filename = $hash.".".$ext;

                if(!file_exists($form->file->getDestination()."/".$filename)){
                    rename($form->file->getFileName(),$form->file->getDestination()."/".$filename);
                }

                if($this->_Users->getImage($user1->getImage())==1){
                    unlink(ROOT_PATH ."/public/img/".$user1->getImage());
                }

                $this->_Users->setImage($user->id_user,$filename);
                //$this->view->file = $file;
                $file = $form->getValues();
                $font = $form->getValue('font');
                
            }
            
            $this->view->file2 = '{"name":"'.$filename.'","type":"image/jpeg","size":"3402400"}';
            
            $this->_helper->layout()->disableLayout();
        }
    }




    private function _getFileForm($font = '')
    {
        $form = new Zend_Form();
        $form->setMethod('post');
        $form->setAttrib('enctype', 'multipart/form-data');
        $form->setAttrib('id', 'file_upload');

        $element = new Zend_Form_Element_File('file');
        $element->setLabel('Cambiar imagen de perfil')
                ->setDestination(ROOT_PATH ."/public/img")
                ->addValidator('Size',false,3700000)
                ->addValidator('Extension', false, array('jpg', 'jpeg', 'png','bmp'));
        
                //->addAttrib('id','labelCambiar');


        $form->addElement($element, 'file');
             //->addElement('submit', 'desar', array('label' => 'Añadir'));
       

        return $form;
    }

    private function _getExtension($filename)
    {
        $filename = strtolower($filename) ;
        $exts = explode(".", $filename) ;
        $n = count($exts)-1;
        return $exts[$n];
    }

    public function paginanextAction(){
        $pagina = $this->_request->getParam("pagina");
        $tipus = $this->_request->getParam("tipus");
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUser(UserModel::getIdentity());
        $x = ($pagina*15)-15;
        switch($tipus){
            //mes
            case 1:
                $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuari(UserModel::getIdentity(),$x);
            break;

            case 2:
                $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuariPunts(UserModel::getIdentity(),$x);
            break;
        }

        $this->view->pagina = $pagina;
        $this->view->tipus = $tipus;
        $this->_helper->layout()->disableLayout();
    }

    public function afegirpositionAction(){
        $id = $this->_request->getParam("id");
        $posicio = $this->_request->getParam("position");
        $posicio = str_replace("(","", $posicio);
        $posicio = str_replace(")","", $posicio);
        @$posicions = explode(",",$posicio);

        //Zend_Debug::dump($posicions);
        $latitude = $posicions[0];
        $longitude = $posicions[1];
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_NoticiaTaula->setPositionNoticia($id,$latitude,$longitude);


        //codigo para crear una notificacion si unusuario coloca una noticia
        //en el mapa i no es suya la noticia
        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $user = UserModel::getIdentity();
        if($user->id_user != $noticia->getUsuari()->getId()){
            $notificacion = array(
                'idUsuariEnv' => $user->id_user,
                'idUsuariRec' => $noticia->getUsuari()->getId(),
                'idTipus' => 1,
                'idNoticia' => $noticia->getId(),
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }


        $responseComent = "La noticia ha sido fijada correctamente.";
        $this->_redirect("index/index/responseComent/$responseComent");
    }

    public function guardarnewsAction(){
        $id = $this->_request->getParam("id");
        //aqui falta insert bbdd
        $user = UserModel::getIdentity();
        $this->_NoticiesFavoritas->setNoticiaFavorita($id,$user->id_user);
        $this->view->id = $id;
        $this->_helper->layout()->disableLayout();
    }

    public function borrarnewsAction(){
        $id = $this->_request->getParam("id");
        //aqui falta insert bbdd
        $user = UserModel::getIdentity();
        $this->_NoticiesFavoritas->removeNoticiaFavorita($id,$user->id_user);
        $this->view->id = $id;
        $this->_helper->layout()->disableLayout();
    }

    public function savedAction(){
        $this->view->mostra = 1;
        $user = UserModel::getIdentity();
        $noticiesGuardades = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
        $this->view->guardadas = $noticiesGuardades;
        $noticiesFinals = array();
        foreach($noticiesGuardades as $noticia){
            $noticiesFinals[] = $this->_NoticiaTaula->getNoticia($noticia);
        }
        $this->view->noticiesFinals = $noticiesFinals;
        $this->view->actiu = 1;

        $responseComent=null;
        $responseComent = $this->_request->getParam("responseComent");
        $this->view->responseComent = $responseComent;

        $this->render();
    }

    public function seguidorokAction(){
        $user = UserModel::getIdentity();
        $id = $this->_request->getParam("id");
        $this->view->id=$id;
        $this->_Seguidors->setNewSeguidor($id,$user->id_user);

        @$datatime = date('Y/m/d/ H:i:s',strtotime("-1 day"));
        //codigo para crear una notificacion si un usuario sige a otro
        $notificacionOk = $this->_Notificaciones->getSeguidor($user->id_user,$id,$datatime);
        if($notificacionOk == 0){
            $notificacion = array(
                'idUsuariEnv' => $user->id_user,
                'idUsuariRec' => $id ,
                'idTipus' => 3,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }

        $this->_helper->layout()->disableLayout();
    }

    public function seguidordownAction(){
        $user = UserModel::getIdentity();
        $id = $this->_request->getParam("id");
        $this->view->id=$id;
        $idConsulta = $this->_Seguidors->getIdSeguiment($id,$user->id_user);
        $this->_Seguidors->removeSeguidor($idConsulta);
        $this->_helper->layout()->disableLayout();
    }

      public function seguidorok2Action(){
        $user = UserModel::getIdentity();
        $id = $this->_request->getParam("id");
        $this->view->id=$id;
        $this->_Seguidors->setNewSeguidor($id,$user->id_user);

        @$datatime = date('Y/m/d/ H:i:s',strtotime("-1 day"));
        //codigo para crear una notificacion si un usuario sige a otro
        $notificacionOk = $this->_Notificaciones->getSeguidor($user->id_user,$id,$datatime);
        if($notificacionOk == 0){
            $notificacion = array(
                'idUsuariEnv' => $user->id_user,
                'idUsuariRec' => $id ,
                'idTipus' => 3,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }
        $this->_helper->layout()->disableLayout();
    }

    public function seguidordown2Action(){
        $user = UserModel::getIdentity();
        $id = $this->_request->getParam("id");
        $this->view->id=$id;
        $idConsulta = $this->_Seguidors->getIdSeguiment($id,$user->id_user);
        $this->_Seguidors->removeSeguidor($idConsulta);
        $this->_helper->layout()->disableLayout();
    }


    public function formdadesAction()
    {
        //$id = $this->_request->getParam('id');
        $dat = explode("-",$this->_getParam('data'));
        $dataok = "$dat[2]".'-'."$dat[1]".'-'."$dat[0]";

        $dades = array(
            //'Id' => $this->_request->getParam('id'),
            'Localidad'=> strip_tags($this->_getParam('localidad')),
            'email'=>  addslashes(strip_tags($this->_getParam('email'))),
            'Tuvida' => strip_tags($this->_getParam('tuVida')),
            'Aficiones' => strip_tags($this->_getParam('aficiones')),
            'Data_neixament' => $dataok
        );
       $user = UserModel::getIdentity();
       $this->_Users->updateDades($dades, $user->id_user);
       $this->view->dadesUser = $this->_Users->getDadesById($user->id_user);
       $this->_helper->layout()->disableLayout();
    }

    public function passwordAction()
    {
        $user = UserModel::getIdentity();
        $usuari = $this->_Users->getDadesById($user->id_user);
        $this->view->pass = $usuari->password;
        $this->view->actiu = 3;
        $this->view->mostra = 1;
        $this->render();
    }

    public function updatepasswordAction()
    {
        $passwordNew = $this->_request->getParam('passNew');
        $user = UserModel::getIdentity();
        
        $this->_Users->updatePassword($passwordNew,$user->id_user);
        $this->canviPassword($user->email, $passwordNew,$user->username);
        $usuari = $this->_Users->getDadesById($user->id_user);
        $this->view->pass = $usuari->password;
        $this->_helper->layout()->disableLayout();

    }


     public function canviPassword($email, $pass, $username2){

        $smtpServer = 'smtp.gmail.com';
        $username = 'service@newsup.es';
        $password = 'Zend05:c3';

        $fromadress = 'service@newsup.es';
        $fromname = 'NewsUP';

        $toadress = $email;

        $subject = 'News UP Cambio de Contraseña';

        $message = '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Newsup Service</title>
</head>
<body class="body" style="margin: 0; padding: 0; background-color: #4d4031;" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
            <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="100%" valign="top" bgcolor="#F0F0F0" style="border:2px solid #669900;">
                        <a href="http://www.newsup.es">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td valign="top" height="90" style="background-position:left; background-repeat: no-repeat;">
					<img src="http://www.newsup.es/images/header-bg.jpg" style="border:0px; border-style:none;" name="Logo NewsUP">
                                </td>
                            </tr>
                        </table>
                        </a>
                        <!--/header-->
                        <!--break-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="15"></td>
                            </tr>
                        </table>
                        <!--/break-->
                        <!--section-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <table width="560" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>
                                                <h2 style="color: #669900; font-size: 21px; font-family:Georgia, \'Times New Roman\', Times, serif; margin: 0px; padding: 0; text-shadow: 1px 1px 1px #fff;">
                                                Servicio de Cambio de Contraseña
                                                </h2>
                                                <!--break-->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td height="10"></td>
                                                    </tr>
                                                </table>
                                                <!--/break-->
						<p>
						Hola '.$username2.',
						</p>
						<p>
						Su contraseña fue cambiada satisfactoriamente.
						</p>
						<p>
						Sus nuevos datos son los siguientes:
						</p>
						<p>
						USUARIO: <b>'.$username2.'</b>
						</p>
						<p>
						CONTRASEÑA: <b>'.$pass.'</b>
						</p>
						<p>
						NewsUP nos gusta estar cerca de los usuarios, para esto les dejamos un servicio en la web llamado Contacta. Si tiene alguna duda o le gustaria comentar algo, sea ya criticar constructivamente o aportar su granito de arena, estaremos encantados a escucharle.
						</p>
						<p>
						El equipo de NewsUP
						</p>
                                                <!--break-->

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
			<!--/break-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="20"></td>
                            </tr>
                        </table>
                        <!--/break-->
                                               <!--/dash-->
                        <table align="center" width="560" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center">
                                    <p style="font-size: 12px; color: #666; margin: 0; padding: 0; font-family: Georgia, \'Times New Roman\', Times, serif; text-transform: uppercase;">
                                    <strong>NewsUP</strong> Todos los Derechos Reservados.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <!--break-->
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td height="25"></td>
                            </tr>
                        </table>
                        <!--/break-->
                        <!--footer-->
                    </td>
                </tr>
            </table>
            <!--/container-->

<!--/100% body table-->
</body>
</html>';
        $config = array('ssl' => 'tls',
                        'auth' => 'login',
                        'username' => $username,
                        'password' => $password);

        $transport = new Zend_Mail_Transport_Smtp($smtpServer, $config);


        $mail = new Zend_Mail('UTF-8');
        $mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);

        $mail->setReplyTo($fromadress, $fromname);
        $mail->addHeader('MIME-Version', '1.0');
        $mail->addHeader('Content-Transfer-Encoding', '8bit');
        $mail->addHeader('X-Mailer:', 'PHP/'.phpversion());

        $mail->setFrom($fromadress, $fromname);
        $mail->addTo($toadress);
        $mail->setSubject($subject);
        $mail->setBodyHtml($message, 'UTF-8', Zend_Mime::ENCODING_BASE64);
        $mail->send($transport);
    }

    public function etiquetasAction(){
        $this->view->actiu = 3;
        $this->render();
    }


    public function afegircomentariokAction(){
        $id = $this->_request->getParam("id");
        $id_pare = $this->_request->getParam("id_pare");

        @$dataHora = date("Y-m-d H:i:s");
        $usuari = UserModel::getIdentity();
        $text = $this->_request->getParam("text");

        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $idUsuari = $noticia->getUsuari()->getId();

        if($id_pare == null){
        $idComentari = $this->_ComentarisTaula->afegirComentari($id,$usuari,$dataHora,$text);
        }else{
        $idComentari = $this->_ComentarisTaula->afegirComentariFill($id,$usuari,$dataHora,$text,$id_pare);
        $idUsuariRep = $this->_ComentarisTaula->getComentariIdUsuari($id_pare);
        if($usuari->id_user != $idUsuariRep){
            $notificacion = array(
                'idUsuariEnv' => $usuari->id_user,
                'idUsuariRec' => $idUsuariRep,
                'idTipus' => 5,
                'idNoticia' => $noticia->getId(),
                'idComentari' => $idComentari,
                'data' => @date('Y-m-d H:i:s'),
                'visto' => false
            );
            $this->_Notificaciones->insertNotificacion($notificacion);
        }


        }
        
        if($usuari->id_user != $noticia->getUsuari()->getId()){
            if($noticia->getUsuari()->getId() != $idUsuariRep){
                $notificacion = array(
                    'idUsuariEnv' => $usuari->id_user,
                    'idUsuariRec' => $idUsuari ,
                    'idTipus' => 4,
                    'idNoticia' => $noticia->getId(),
                    'idComentari' => $idComentari,
                    'data' => @date('Y-m-d H:i:s'),
                    'visto' => false
                );
                $this->_Notificaciones->insertNotificacion($notificacion);
            }
        }
        //Aixo ho tindre que cambiar perque faixi el redirect cap a noticia en cuestio
        $responseComent = "El Comentario ha sido creado satisfactoriamente";
        $this->_redirect("index/noticias2/responseComent/$responseComent/id/$id");
    }

    public function perfilsAction(){

            $this->view->responseComent = $this->_request->getParam("responseComent");
            $user = UserModel::getIdentity();
            $this->view->mostra = 1;
            $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
            $contador=0;
            if($segueixen != null){
            foreach($segueixen as $segueix){
                $usersOk[$contador] = $this->_Users->getUsuariTot($segueix);
                $noticies = $this->_NoticiaTaula->cantitatUserFora($usersOk[$contador]->getId());
                $segueix = $this->_Seguidors->cantitatSegueix($usersOk[$contador]->getId());
                $segueixen = $this->_Seguidors->cantitatSegueixen($usersOk[$contador]->getId());
                $usersOk[$contador]->addNoticies($noticies);
                $usersOk[$contador]->addSegueixen($segueixen);
                $usersOk[$contador]->addSegueix($segueix);
                $contador++;
            }
            //Zend_Debug::dump($where1);
            $x=0;
            $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
            $this->view->usuarisSeguits = $usersOk;
            @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
            $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
            $this->view->pagina = 1;
            $this->view->actiu = 2;
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
            }else{
                $this->view->noticiesFinals == null;
                $this->view->pagina = 1;
                $this->view->cantitat=0;
            }
            $this->render();
    }


    public function recientesperfilAction(){
        $x=0;
        $user = UserModel::getIdentity();
        $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
        if($segueixen != null){
        $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguidesTotes($segueixen);
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguidesTotesRecientes($segueixen,$x);
         }else{
                $this->view->noticiesFinals == null;
                $this->view->cantitat=0;
        }
        $this->view->pagina = 1;
        $this->_helper->layout()->disableLayout();
    }


    public function sietediasperfilAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $user = UserModel::getIdentity();
        $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
         if($segueixen != null){
        @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;
        $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
        $this->view->pagina = 1;
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
        }else{
                $this->view->noticiesFinals == null;
                $this->view->cantitat=0;
                $this->view->pagina = 1;
        }
        $this->_helper->layout()->disableLayout();
    }

    public function unmesperfilAction(){

        @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
        $x = 0;
        $user = UserModel::getIdentity();
        $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
        if($segueixen != null){
        $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
        $this->view->pagina = 1;
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
        }else{
                $this->view->noticiesFinals == null;
                $this->view->cantitat=0;
                $this->view->pagina = 1;
        }
        $this->_helper->layout()->disableLayout();
    }

    public function undiaperfilAction(){

        @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
        $x = 0;
        $user = UserModel::getIdentity();

        $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
        if($segueixen != null){
        $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
        $this->view->pagina = 1;
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
        }else{
                $this->view->noticiesFinals == null;
                $this->view->cantitat=0;
                $this->view->pagina = 1;
        }
        $this->_helper->layout()->disableLayout();
    }

    public function totperfilAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $user = UserModel::getIdentity();
        $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
        if($segueixen != null){
        $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguidesTotes($segueixen);
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguidesTotes($segueixen,$x);
        $this->view->pagina = 1;
         }else{
                $this->view->noticiesFinals == null;
                $this->view->cantitat=0;
                $this->view->pagina = 1;
        }
        $this->_helper->layout()->disableLayout();
    }

        public function siguenperfilAction(){
        $x=0;
        $user = UserModel::getIdentity();
        $segueixen = $this->_Seguidors->getSegueixen($user->id_user,$x);
        $this->view->id = $user->id_user;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueixen($user->id_user);
        $this->view->pagina = 1;
        $usersOk = array();
        $contador =0;
        foreach($segueixen as $segueix){
            $usersOk[$contador] = $this->_Users->getUsuariTot($segueix);
            $noticies = $this->_NoticiaTaula->cantitatUserFora($usersOk[$contador]->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($usersOk[$contador]->getId());
            $segueixen = $this->_Seguidors->cantitatSegueixen($usersOk[$contador]->getId());
            $usersOk[$contador]->addNoticies($noticies);
            $usersOk[$contador]->addSegueixen($segueixen);
            $usersOk[$contador]->addSegueix($segueix);
            $contador++;
        }
        $this->view->usuaris2 = $usersOk;
        $this->_helper->layout()->disableLayout();
    }

        public function sigoperfilAction(){
        $user = UserModel::getIdentity();
        $x=0;
        $segueixen = $this->_Seguidors->getSegueix($user->id_user,$x);
        $this->view->id = $user->id_user;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueix($user->id_user);
        $this->view->pagina = 1;
        $usersOk = array();
        $contador =0;
        foreach($segueixen as $segueix){
            $usersOk[$contador] = $this->_Users->getUsuariTot($segueix);
            $noticies = $this->_NoticiaTaula->cantitatUserFora($usersOk[$contador]->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($usersOk[$contador]->getId());
            $segueixen = $this->_Seguidors->cantitatSegueixen($usersOk[$contador]->getId());
            $usersOk[$contador]->addNoticies($noticies);
            $usersOk[$contador]->addSegueixen($segueixen);
            $usersOk[$contador]->addSegueix($segueix);
            $contador++;
        }
        $this->view->usuaris2 = $usersOk;
        $this->_helper->layout()->disableLayout();
    }

     public function paginanextsegAction(){
        $user = UserModel::getIdentity();
        $pagina = $this->_request->getParam("pagina");
        $this->view->id = $user->id_user;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueixen($user->id_user);
        $x = ($pagina*15)-15;
         $usersOk = array();
        $contador =0;
        $segueixen = $this->_Seguidors->getSegueixen($user->id_user,$x);
        foreach($segueixen as $segueix){
            $usersOk[$contador] = $this->_Users->getUsuariTot($segueix);
            $noticies = $this->_NoticiaTaula->cantitatUserFora($usersOk[$contador]->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($usersOk[$contador]->getId());
            $segueixen = $this->_Seguidors->cantitatSegueixen($usersOk[$contador]->getId());
            $usersOk[$contador]->addNoticies($noticies);
            $usersOk[$contador]->addSegueixen($segueixen);
            $usersOk[$contador]->addSegueix($segueix);
            $contador++;
        }

        $this->view->usuaris2 = $usersOk;
        $this->view->pagina = $pagina;
        $this->_helper->layout()->disableLayout();
    }

     public function paginanextseg2Action(){
        $user = UserModel::getIdentity();
        $pagina = $this->_request->getParam("pagina");
        $this->view->id = $user->id_user;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueix($user->id_user);
        $x = ($pagina*15)-15;
         $usersOk = array();
        $contador =0;
        $segueixen = $this->_Seguidors->getSegueix($user->id_user,$x);
        foreach($segueixen as $segueix){
            $usersOk[$contador] = $this->_Users->getUsuariTot($segueix);
            $noticies = $this->_NoticiaTaula->cantitatUserFora($usersOk[$contador]->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($usersOk[$contador]->getId());
            $segueixen = $this->_Seguidors->cantitatSegueixen($usersOk[$contador]->getId());
            $usersOk[$contador]->addNoticies($noticies);
            $usersOk[$contador]->addSegueixen($segueixen);
            $usersOk[$contador]->addSegueix($segueix);
            $contador++;
        }
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
        }
        $this->view->usuaris2 = $usersOk;
        $this->view->pagina = $pagina;
        $this->_helper->layout()->disableLayout();
    }

    public function paginanextsegeixoAction(){
        $pagina = $this->_request->getParam("pagina");
        $tipus = $this->_request->getParam("tipus");
        $user = UserModel::getIdentity();
        $this->view->cantitat = $this->_NoticiaTaula->cantitat();
        $x = ($pagina*15)-15;


            switch($tipus){
                //mes
                case 1:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;

                    $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
                    $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);

                break;

                case 2:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
                    $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
                    $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
                break;

                case 3:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;
                    $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguides($segueixen,$data);
                    $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguides($segueixen,$x,$data);
                break;

                case 4:
                    $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguidesTotes($segueixen);
                    $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguidesTotes($segueixen,$x);
                break;

                case 5:
                    $segueixen = $this->_Seguidors->getSegueixenid($user->id_user);
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatNoticiesSeguidesTotes($segueixen);
                    $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesSeguidesTotesRecientes($segueixen,$x);
                break;
            }

        $this->view->pagina = $pagina;
        $this->view->tipus = $tipus;
        $this->_helper->layout()->disableLayout();
    }

    public function sugerircambioAction(){
        $id = $this->_request->getParam('id');
        $user = UserModel::getIdentity();
        $noticia = $this->_NoticiaTaula->getNoticia($id);
        $notificacionOk = $this->_Notificaciones->getSugerencias($noticia->getId(),$user->id_user);
        if($notificacionOk == 0){
            $notificacion = $this->_Notificaciones->setSugerencia($noticia->getId(),$noticia->getUsuari()->getId(),$user->id_user);
            $this->_helper->layout()->disableLayout();
        }
    }

    public function recargarnotificacionAction()
    {
        $user = UserModel::getIdentity();

        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
        $this->view->notficaciones = $notificaciones;

        $this->_helper->layout()->disableLayout();
    }
     //historial ***********************************************************
    public function historialnotificacionAction()
    {
        $user = UserModel::getIdentity();
        $y = 15;
	$this->_Notificaciones->clearNotificacions($user->id_user);
        $this->view->cantitat = $this->_Notificaciones->cantitatNotificaciones($user->id_user);
        $notificaciones = $this->_Notificaciones->allNotificaciones($user->id_user,$y);
        $this->view->notificaciones = $notificaciones;
        $data = new Zend_Date();
        $this->view->numMostrats = $y+15;
        $dia = $data->get(Zend_Date::DAY);
        /*Zend_debug::Dump($data);
        die;*/
        $this->view->data = $data;
        $this->render();

    }

    public function historialnotificacionnextAction(){
        $user = UserModel::getIdentity();
        $y = $this->_request->getParam('cantitat');
        //Zend_Debug::dump($y);
        $this->view->cantitat = $this->_Notificaciones->cantitatNotificaciones($user->id_user);
        $notificaciones = $this->_Notificaciones->allNotificaciones($user->id_user,$y);
        $this->view->notificaciones = $notificaciones;
        $data = new Zend_Date();
        $this->view->numMostrats = $y+15;
        $dia = $data->get(Zend_Date::DAY);
        /*Zend_debug::Dump($data);
        die;*/
        $this->view->data = $data;
        $this->_helper->layout()->disableLayout();
    }

    


}

