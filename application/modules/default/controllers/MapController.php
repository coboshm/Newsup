<?php
require_once 'BD/UserModel.php';
require_once 'BD/VotsTaula.php';
require_once 'Vot.php';
require_once 'BD/NoticiaTaula.php';
require_once 'BD/Users.php';
require_once 'Noticia.php';
require_once 'BD/Categories.php';
require_once 'Categoria.php';
require_once 'Comentari.php';
require_once 'BD/ComentarisTaula.php';
require_once 'Notificacion.php';
require_once 'BD/Notificaciones.php';
require_once 'BD/TipoNotificacion.php';

class MapController extends Zend_Controller_Action
{

    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_Notificaciones;

    public function init()
    {
        /* Initialize action controller here */
         $this->_Users = new Users();
         $this->_VotsTaula = new VotsTaula();
         $this->_Categories = new Categories();
         $this->_NoticiaTaula = new NoticiaTaula();
         $this->_ComentarisTaula = new ComentarisTaula();
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
                }
            }
            $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
            $this->view->notficaciones = $notificaciones;
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
                    $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                    $this->view->notficaciones = $notificaciones;
                }
            }
        }
        //$this->view->ranking = $this->_NoticiaTaula->allNoticiasRanking();
        $this->view->llistaCategories = $this->_Categories->getCategories();
        
    }

    public function indexAction()
    {
        $this->view->mapaSi = true;
        @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
        $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesMapa();
        $this->view->mostra = 3;
        $this->render();

    }

    public function colocarAction()
    {
        $this->view->mapaSi2 = true;

        $cadena1 = $this->_request->getParam('titolNoticia');
        $var1 =explode("-",$cadena1);
        $idNoticia = $var1[1];

        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);

        $this->render();

    }

    public function verAction()
    {
        $this->view->mapaSi3 = true;

        $cadena1 = $this->_request->getParam('titolNoticia');
        
        $var1 =explode("-",$cadena1);
        
        $idNoticia = $var1[1];

        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);

        $this->render();

    }

     public function cambiarAction()
    {
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $cadena1 = $this->_request->getParam('titolNoticia');
            $var1 =explode("-",$cadena1);
            $idNoticia = $var1[1];
            if($user->id_user==$this->_NoticiaTaula->getUserNoticia($idNoticia)){
                $this->view->mapaSi4 = true;
                $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);
                $this->render();
            }else{
                $responseLogin = "Tienes que ser el propietario para poder realizar esta acción";
                $this->_redirect("index/index/responseLogin/$responseLogin");
            }
        }else{
            $responseLogin = "Tienes que hacer login para acceder aqui";
            $this->_redirect("index/index/responseLogin/$responseLogin");
        }

    }



    public function ver2Action()
    {

        $this->view->mapaSi3 = true;
        $idNoticia = $this->_request->getParam("id");
        $idNotificacion = $this->_request->getParam("idNotificacion");
        $this->_Notificaciones->updateVisto($idNotificacion);
        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);
        //ALEX---------------------------------------NOTIFICACIONES
        $user = UserModel::getIdentity();
        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
        $this->view->notficaciones = $notificaciones;
        $this->render('ver');

    }

    public function cambiar2Action()
    {

        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $idNoticia = $this->_request->getParam("id");
            if($user->id_user==$this->_NoticiaTaula->getUserNoticia($idNoticia)){
                $this->view->mapaSi4 = true;
                $idNotificacion = $this->_request->getParam("idNotificacion");
                $this->_Notificaciones->updateVisto($idNotificacion);
                $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);
                //ALEX---------------------------------------NOTIFICACIONES
                $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                $this->view->notficaciones = $notificaciones;
                $this->render('cambiar');
            }else{
                $responseLogin = "Tienes que ser el propietario para poder realizar esta acción";
                $this->_redirect("index/index/responseLogin/$responseLogin");
            }
        }else{
            $responseLogin = "Tienes que hacer login para acceder aqui";
            $this->_redirect("index/index/responseLogin/$responseLogin");
        }
        

    }



}

?>
