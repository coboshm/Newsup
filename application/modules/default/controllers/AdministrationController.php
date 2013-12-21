<?php
require_once 'BD/AdminModel.php';
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

class AdministrationController extends Zend_Controller_Action
{

    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_NoticiesFavoritas;
    private $_Seguidors;
    private $_Contactar;

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

    }

    public function preDispatch(){

    }


    public function indexAction()
    {
        $responseLogin=null;


        $responseLogin = $this->_request->getParam("responseLogin");

        $this->view->responseLogin = $responseLogin;
        $this->view->adminSi = true;
        $this->render();

    }

    public function intoAction()
    {
        $this->view->adminSi2 = true;
        $this->render();

    }


   public function loginAction(){
        $responseLogin=null;
        $email = $this->_request->getParam("name");
        $password = $this->_request->getParam("password");
        //Zend_Debug::dump($email);
        //Zend_Debug::dump($password);
        $encript_password = sha1($password);
        try{
            $user = new AdminModel();
            $user->setMessage('El nombre de usuario introducido es incorrecto.', AdminModel::NOT_IDENTITY);
            $user->setMessage('La contraseña ingresada es incorrecta. Inténtelo de nuevo.', AdminModel::INVALID_CREDENTIAL);
            $user->setMessage('Los campos de Usuario y Password no pueden dejarse en blanco.', AdminModel::INVALID_LOGIN);
            $user->login($email,$encript_password);
        } catch(Exception $e){
            $responseLogin = $e->getMessage();
            //$this->view->responseLogin = $responseLogin;
            //Zend_Debug::dump($responseLogin);

        }
        //Zend_Debug::dump($user);

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        if($responseLogin==null){
            $this->_redirect('administration/into');
        }else{
            $this->_redirect("administration/index/responseLogin/$responseLogin");
        }
    }





}

?>
