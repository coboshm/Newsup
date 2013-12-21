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
require_once 'BD/NoticiesFavoritas.php';

class SearchController extends Zend_Controller_Action
{
    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_NoticiesFavoritas;

    public function init()
    {
        /* Initialize action controller here */
         $this->_Users = new Users();
         $this->_VotsTaula = new VotsTaula();
         $this->_Categories = new Categories();
         $this->_NoticiaTaula = new NoticiaTaula();
         $this->_ComentarisTaula = new ComentarisTaula();
         $this->_NoticiesFavoritas = new NoticiesFavoritas();

         
    }

    public function preDispatch(){
        if (UserModel::isLoggedIn()) {
                $this->view->loggedIn = true;
                $this->view->user = UserModel::getIdentity();
                $user = UserModel::getIdentity();
                $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                //Zend_Debug::dump(UserModel::getIdentity());
                $usuari = $this->_Users->getUsuari($user->id_user);
                if(count($usuari)==1){
                    foreach($usuari as $user1){
                        $this->view->usuario = $user1;
                    }
                }
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
                    $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                    //Zend_Debug::dump(UserModel::getIdentity());
                    $usuari = $this->_Users->getUsuari($user->id_user);
                    if(count($usuari)==1){
                        foreach($usuari as $user1){
                            $this->view->usuario = $user1;
                        }
                    }
                }
            }


        }
        $this->view->llistaCategories = $this->_Categories->getCategories();
        
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

    public function indexAction(){
        $paraula= $this->_request->getParam("buscador");
        $this->view->paraula=$paraula;
        $noticies = $this->_NoticiaTaula->getNotciaSearch($paraula);
        $this->view->noticiesFinals = $noticies;
        $this->view->noticiesUsuaris = $this->_Users->getNotciaSearch($paraula);
        $this->view->llistaCategories = $this->_Categories->getCategories();
        $this->view->idCategoria = 1;
        $this->render();
    }

    public function searchundiaAction(){
        $paraula= $this->_request->getParam("paraula");
        $idCategoria= $this->_request->getParam("categoria");
        @$data=date('Y-m-d H:i:s',  strtotime("-1 day"));
        if($idCategoria== 1){
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDia(utf8_decode($paraula),$data);
            $this->view->idCategoria = 1;
        }else{
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDiaCategoria(utf8_decode($paraula),$data,$idCategoria);
            $this->view->idCategoria = $idCategoria;
        }

        $this->view->paraula=$paraula;
        $this->view->noticiesUsuaris = $this->_Users->getNotciaSearch($paraula);
        $this->render();
    }
    public function searchunasemanaAction(){
        @$data=date('Y-m-d H:i:s',  strtotime("-1 week"));
        $paraula= $this->_request->getParam("paraula");
        $idCategoria= $this->_request->getParam("categoria");
        if($idCategoria == 1){
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDia(utf8_decode($paraula),$data);
            $this->view->idCategoria = 1;
        }else{
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDiaCategoria(utf8_decode($paraula),$data,$idCategoria);
            $this->view->idCategoria = $idCategoria;
        }

        $this->view->paraula=$paraula;
        $this->view->noticiesUsuaris = $this->_Users->getNotciaSearch($paraula);
        $this->render();
    }
    public function searchunmesAction(){
        @$data=date('Y-m-d H:i:s',  strtotime("-1 month"));
        $paraula= $this->_request->getParam("paraula");
        $idCategoria= $this->_request->getParam("categoria");
        if($idCategoria== 1){
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDia(utf8_decode($paraula),$data);
            $this->view->idCategoria = 1;
        }else{
            $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchUnDiaCategoria(utf8_decode($paraula),$data,$idCategoria);
            $this->view->idCategoria = $idCategoria;
        }

        $this->view->paraula=$paraula;
        $this->view->noticiesUsuaris = $this->_Users->getNotciaSearch($paraula);
        $this->render();
    }
    public function searchcategoriaAction(){
        $paraula= $this->_request->getParam("paraula");
        $id= $this->_request->getParam("id");
        $this->view->paraula=$paraula;
        $this->view->idCategoria=$id;
        $utf = $this->isUTF8($paraula);
        if($id==1){
            if($utf==false){
                $noticies = $this->_NoticiaTaula->getNotciaSearch(utf8_decode($paraula));
                $this->view->noticiesFinals = $noticies;
            }else{
                $noticies = $this->_NoticiaTaula->getNotciaSearch($paraula);
                $this->view->noticiesFinals = $noticies;
            }
        }else{
            if($utf==false){
                $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchCategoria(utf8_decode($paraula),$id);
            }else{
                $this->view->noticiesFinals = $this->_NoticiaTaula->getNotciaSearchCategoria($paraula,$id);
            }
        }
        $this->view->noticiesUsuaris = $this->_Users->getNotciaSearch($paraula);
        $this->render();
    }
  
}