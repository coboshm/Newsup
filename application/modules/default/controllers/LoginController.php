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

class LoginController extends Zend_Controller_Action
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

    }
    
    public function preDispatch(){
       
        if (UserModel::isLoggedIn()) {
            $this->_redirect("index");
        }

    }

    public function indexAction(){
        $responseLogin=null;
        $email = $this->_request->getParam("email_login");
        $password1 = $this->_request->getParam("password_login");

        
        //Zend_Debug::dump($email);
        //Zend_Debug::dump($password);
        $password = sha1($password1);
        try{
            $user = new UserModel();
            $user->setMessage('El nombre de usuario introducido es incorrecto.', UserModel::NOT_IDENTITY);
            $user->setMessage('La contraseña ingresada es incorrecta. Inténtelo de nuevo.', UserModel::INVALID_CREDENTIAL);
            $user->setMessage('Los campos de Usuario y Password no pueden dejarse en blanco.', UserModel::INVALID_LOGIN);
            $user->login($email,$password);
        } catch(Exception $e){
            $responseLogin = $e->getMessage();
            //$this->view->responseLogin = $responseLogin;
            //Zend_Debug::dump($responseLogin);

        }
        //Zend_Debug::dump($user);

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        if($responseLogin==null){
            $user2 = $this->_Users->getDadesByUsername($email);

            if($user2->getActivat()==0){
                $user->logout();
                setcookie("newsup","",time()+60*60*24*31,"/");
                $responseLogin = "Este Usuario no ha sido Activado Correctamente.";
                $this->_redirect("index/index/responseLogin/$responseLogin");
            }else{
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
                $this->_redirect('privat');
            }
        }else{
            $this->_redirect("index/index/responseLogin/$responseLogin");
        }
    }
}

