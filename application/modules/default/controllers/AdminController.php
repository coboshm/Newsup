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
require_once 'BD/NoticiesFavoritas.php';

class AdminController extends Zend_Controller_Action
{

    private $_Users;
    private $_Categories;
    private $_NoticiaTaula;
    private $_VotsTaula;
    private $_ComentarisTaula;
    private $_NoticiesFavoritas;
    private $_Seguidors;

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
                $this->view->guardadas = $this->_NoticiesFavoritas->getGuardadas($user->id_user);
                //Zend_Debug::dump(UserModel::getIdentity());
        }
        $this->view->llistaCategories = $this->_Categories->getCategories();
    }

    public function indexAction()
    {
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
        $this->view->mostra = 2;

        //Agafar les noticies de la principal

        @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
        $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
        $x = 0;
        $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
        $this->view->pagina = 1;
        //Zend_Debug::dump($noticies);

    }

    public function noticiesAction(){
        
        //Zend_Debug::dump($this->_NoticiaTaula->getNoticiesUsuari(UserModel::getIdentity()));
        //$this->_helper->viewRenderer->setNoRender(true);
        //$this->_helper->layout()->disableLayout();
        $this->render();
    }
}
?>
