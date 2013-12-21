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

class IndexController extends Zend_Controller_Action
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
        //$cookie = ;
       // Zend_Debug::dump($this->getRequest()->getCookie('newsup'));
       // die();
        //Zend_Debug::dump($this->getRequest()->getCookie('newsup'));
        //die();
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
                    $responseLogin = "Este usuario no ha sido activado correctamente.";
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
        $this->view->llistaCategories = $this->_Categories->getCategories();
        //$this->view->ranking = $this->_NoticiaTaula->allNoticiasRanking();
    }

    public function indexAction()
    {

        
        $responseLogin=null;
        $responseComent=null;
        $responseVotoFail=null;
        $responsePropietario=null;
        
        $responseLogin = $this->_request->getParam("responseLogin");
        $responseComent = $this->_request->getParam("responseComent");
        $responseVotoFail = $this->_request->getParam("responseVotoFail");
        $responsePropietario = $this->_request->getParam("responsePropietario");
        $this->view->error = $this->_request->getParam("tipus");
        //Zend_Debug::dump($responseLogin);
        $this->view->responseLogin = $responseLogin;
        $this->view->responseVotoFail = $responseVotoFail;
        $this->view->responseComent = $responseComent;
        $this->view->responsePropietario = $responsePropietario;
        $this->view->mostra = 2;

        //Agafar les noticies de la principal
        
        @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
        $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
        $x = 0;
        $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
        $this->view->pagina = 1;
        //Zend_Debug::dump($noticies);
              
    }

    public function afegirnewAction(){
        
        $url = $this->_request->getParam("url");
        //Zend_Debug::dump($url);
        $postsi = $this->_request->getParam("post");

        if($postsi=="si"){
           $elementspost = $this->_request->getParam("elementspost");
           $elementsjunts =  str_replace(",","&", $elementspost);
           $numpost = $this->_request->getParam("numeroelements");
           $urlCompleta = $url."?".$elementsjunts;

        }else{
            $urlCompleta = $url;
        }

        $urlinici = $url;

        @$urltrosos = explode("/",$urlinici);
        @$urlinici = $urltrosos[0]."//".$urltrosos[1].$urltrosos[2];


        //Zend_Debug::dump($urlinici);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"$url");
        

        //Sa de revisar quan hi ha paginas amb elements post
        /*if($postsi=="si"){
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS,$elementsjunts);
            //Zend_Debug::dump($elementsjunts);
        }*/
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        //curl_setopt($ch, CURLOPT_PROXY, "223.254.254.1");
        //curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
        //curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "internet:Password12");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $resultado = curl_exec ($ch);

        //Zend_Debug::dump($resultado);
        //$resultadomin = strtolower($resultado);
        //Zend_Debug::dump($resultadomin);

        $pieces = explode("<title>", $resultado);
        if(count($pieces)>1){
            $result = explode("</title>",$pieces[1]);
            $utf = $this->isUTF8($result[0]);
            if($utf==false){
                $this->view->titul = utf8_encode($result[0]);
            }else{
                $this->view->titul = $result[0];
            }
            
        }else{
            $this->view->titul = "Error, escriba usted el título porfavor";
        }
        //Zend_Debug::dump($result[0]);

        @$description = explode("<meta name=\"description\" content=\"", $resultado);
        //Zend_Debug::dump(count($description));
        if(count($description)>1){
        @$descriptionok = explode("\"",$description[1]);
        $utf = $this->isUTF8($descriptionok[0]);
        if($utf==false){
            $this->view->descripcion = utf8_encode($descriptionok[0]);
        }else{
            $this->view->descripcion = $descriptionok[0];
        }
        }else{
            //aqui descripcio per defecte
            @$descriptionmay = explode("<meta name=\"Description\" content=\"", $resultado);
            //Zend_Debug::dump($descriptionmay);
            if(count($descriptionmay)>1){
            @$descriptionok = explode("\"",$descriptionmay[1]);
            $utf = $this->isUTF8($descriptionok[0]);
            if($utf==false){
                $this->view->descripcion = utf8_encode($descriptionok[0]);
            }else{
                $this->view->descripcion = $descriptionok[0];
            }
            }else{
                @$descriptionesp = explode("<meta name=\"Description\"  content=\"", $resultado);
                if(count($descriptionesp)>1){
                    @$descriptionok = explode("\"",$descriptionesp[1]);
                    $utf = $this->isUTF8($descriptionok[0]);
                    if($utf==false){
                        $this->view->descripcion = utf8_encode($descriptionok[0]);
                    }else{
                        $this->view->descripcion = $descriptionok[0];
                    }
                }else{
                    $this->view->descripcion = "No hay descripción, escrívela usted porfavor";
                }
            }
        }
        
        
        @$pieces2 = explode("<body", $resultado);
        @$resultBody = explode("</body>",$pieces2[1]);
        //result[0] conte tot el body de la url

        @$resultImg = explode(" src=\"",$resultBody[0]);
        $urlsImages = array();
        $piece3 = array();
        //Zend_Debug::dump($resultImg);
        for($x=0;$x<count($resultImg);$x++){
            @$piece3[$x] = explode("\"",$resultImg[$x]);
            //Zend_Debug::dump($x);
            if($x!=0){
                if(substr($piece3[$x][0],0,7)=="http://"){
                    $urlsImages[] = utf8_encode($piece3[$x][0]);
                }else{
                    $exsist = $urlinici.$piece3[$x][0];
                    $urlsImages[] = utf8_encode($exsist);
                }
            }
        }

        $urlsimatgesOk = array();
        $y=0;
        $x=0;
        for($x=0;$x<count($urlsImages);$x++){
            @$tamanys = getimagesize($urlsImages[$x]);
            if($tamanys!=false){
                //Zend_Debug::dump($tamanys);
                if($tamanys[0]>=138 && $tamanys[1]>=140){
                    $urlsimatgesOk[$y] = $urlsImages[$x];
                    $y++;
                    //Zend_Debug::dump($urlsimatgesOk);
                }
            }
        }



        
        //Zend_Debug::dump($urlsImages);
        $this->view->url = $urlCompleta;
        $this->view->imatges = $urlsimatgesOk;
        //$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function logoutAction(){
        $user = new UserModel();
        $user->logout();
        setcookie("newsup","",time()+60*60*24*31,"/");
        $this->_redirect("index");
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

    public function guardarnewAction(){
        $url = $this->_request->getParam("url-pujada");
        $categoria = $this->_request->getParam("categoria-pujada");
        $descripcion = $this->_request->getParam("user-desc-text");
        $titul = $this->_request->getParam("user-title-text");
        $imatge = $this->_request->getParam("imatgeFinal");
        $dataHora = date("Y-m-d H:i:s");

        if($imatge == "/images/noimage.png" || $imatge == null){
            $nomnou = null;
        }else{
            $ext = $this->_getExtension($imatge);
            $hash = sha1($imatge);
            $nomnou = date("M-Y")."/".$hash.".".$ext;
            //Zend_Debug::dump($nomnou);
           //copy($imagen,ROOT_PATH ."/public/imgnotice".$nomnou);
            $mi_curl = curl_init ($imatge);
            if (!file_exists(ROOT_PATH."/public/imgnotice/".date("M-Y"))) {
                mkdir(ROOT_PATH."/public/imgnotice/".date("M-Y"));
            }
            
            $fs_archivo = fopen (ROOT_PATH ."/public/imgnotice/".$nomnou, "w");
    
            curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);
            curl_setopt ($mi_curl, CURLOPT_HEADER, 0);
            curl_exec ($mi_curl);
            curl_close ($mi_curl);
            fclose ($fs_archivo);
            
        }
        
        //Zend_Debug::dump($dataHora);
        $usuari = UserModel::getIdentity();
      
        $this->_NoticiaTaula->setNoticia($nomnou,strip_tags($titul),strip_tags($descripcion),$dataHora,$usuari,$url,$categoria);

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $this->_redirect('privat');
    }

    private function _getExtension($filename)
    {
        $filename = strtolower($filename) ;
        $exts = explode(".", $filename) ;
        $n = count($exts)-1;
        $exts2 = explode("?",$exts[$n]);
        
        return $exts2[0];
    }

    public function noticiasAction(){
        
        $cadena1 = $this->_request->getParam('titolNoticia');
        $var1 =explode("-",$cadena1);
        $idNoticia = $var1[1];

        $metanom = $this->_NoticiaTaula->getTitol($idNoticia);
        $metadescripcio = $this->_NoticiaTaula->getDescripcio($idNoticia);

        $this->view->metanom = $metanom;
        $this->view->metadescripcio = $metadescripcio;

        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);
        $this->render();

    }

    // vai
    public function noticias2Action(){

        $responseComent = $this->_request->getParam("responseComent");
        $idNoticia =  $this->_request->getParam("id");

        $metanom = $this->_NoticiaTaula->getTitol($idNoticia);
        $metadescripcio = $this->_NoticiaTaula->getDescripcio($idNoticia);

        $this->view->metanom = $metanom;
        $this->view->metadescripcio = $metadescripcio;

        $this->view->responseComent = $responseComent;
        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);
        $this->render('noticias');
    }

    public function comentarisnousAction(){
        $idNoticia = $this->_request->getParam("id");
        $this->view->noticia = $idNoticia;
        $this->view->comentaris = $this->_ComentarisTaula->getComentarisNoticia($idNoticia);
        $this->_helper->layout()->disableLayout();
    }

    public function comentarisanticAction(){
        $idNoticia = $this->_request->getParam("id");
        $this->view->noticia = $idNoticia;
        $this->view->comentaris = $this->_ComentarisTaula->getComentarisNoticiaAntic($idNoticia);
        $this->_helper->layout()->disableLayout();
    }

    public function comentarispuntsAction(){
        $idNoticia = $this->_request->getParam("id");
        $this->view->noticia = $idNoticia;
        $this->view->comentaris = $this->_ComentarisTaula->getComentarisNoticiaPunts($idNoticia);
        $this->_helper->layout()->disableLayout();
    }


    public function recientesAction(){
        $x=0;
        $idCategoria = $this->_request->getParam("id");
        if($idCategoria == 1){
            $this->view->cantitat = $this->_NoticiaTaula->cantitat();
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesRecientes($x);
        }else{
           $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaRecientes($idCategoria,$x);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategori($idCategoria);
        }
        $this->view->idCategoria = $idCategoria;
        $this->view->pagina = 1;
        $this->view->tipus = 5;
        $this->_helper->layout()->disableLayout();
    }


    public function sietediasAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $idCategoria = $this->_request->getParam("id");
        @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;

        if($idCategoria == 1){
            $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
        }else{
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($idCategoria,$x,$data);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($idCategoria,$data);
        }
        $this->view->idCategoria = $idCategoria;
        $this->view->pagina = 1;
        $this->view->tipus = 3;
        $this->_helper->layout()->disableLayout();
    }

    public function unmesAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $idCategoria = $this->_request->getParam("id");
        @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
        if($idCategoria == 1){
            $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
        }else{
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($idCategoria,$x,$data);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($idCategoria,$data);
        }
        $this->view->idCategoria = $idCategoria;
        $this->view->pagina = 1;
        $this->view->tipus = 1;
        $this->_helper->layout()->disableLayout();
    }

    public function undiaAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $idCategoria = $this->_request->getParam("id");
        @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
        if($idCategoria == 1){
            $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
        }else{
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($idCategoria,$x,$data);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($idCategoria,$data);
        }
        $this->view->idCategoria = $idCategoria;
        $this->view->pagina = 1;
        $this->view->tipus = 2;
        $this->_helper->layout()->disableLayout();
    }

    public function totAction(){
        //$this->view->pagina = 1;
        $x = 0;
        $idCategoria = $this->_request->getParam("id");
        if($idCategoria == 1){
            $this->view->cantitat = $this->_NoticiaTaula->cantitat();
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticies($x);
        }else{
           $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoria($idCategoria,$x);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategori($idCategoria);
        }
        $this->view->idCategoria = $idCategoria;
        $this->view->tipus = 4;
        $this->view->pagina = 1;
        $this->_helper->layout()->disableLayout();
    }

    public function paginaokAction(){
        $pagina = $this->_request->getParam("pagina");
        $x = ($pagina*15)-15;

        $responseComent = null;
        $responseComent = $this->_request->getParam("responseComent");
        $this->view->responseComent = $responseComent;
        $tipus = $this->_request->getParam("tipus");

         switch($tipus){
                //mes
                case 1:

                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 2:

                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 3:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 4:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitat();
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticies($x);
                break;

                case 5:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitat();
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesRecientes($x);
                break;
            }
        //$this->view->categoria = $categoria;
        $this->view->pagina = $pagina;
        $this->view->tipus = $tipus;
        $this->view->mostra = 2;
        $this->render();
    }

    public function paginanextAction(){
        $pagina = $this->_request->getParam("pagina");
        $tipus = $this->_request->getParam("tipus");
        $categoria = $this->_request->getParam("categoria");
        
        $x = ($pagina*15)-15;

        if($categoria==1){
            switch($tipus){
                //mes
                case 1:

                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 2:

                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 3:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatData($data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesFiltreData($data,$x);
                break;

                case 4:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitat();
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticies($x);
                break;

                case 5:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitat();
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesRecientes($x);
                break;
            }
        }else{
            switch($tipus){
                //mes
                case 1:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($categoria,$data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($categoria,$x,$data);
                break;

                case 2:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-1 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($categoria,$data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($categoria,$x,$data);
                break;

                case 3:
                    @$data= date('Y/m/d/ H:i:s',strtotime("-7 day")) ;
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatCategoriaData($categoria,$data);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($categoria,$x,$data);
                break;

                case 4:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatCategori($idCategoria);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoria($idCategoria,$x);
                break;

                case 5:
                    $this->view->cantitat = $this->_NoticiaTaula->cantitatCategori($idCategoria);
                    $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaRecientes($idCategoria,$x);
                break;
            }
        }
        $this->view->categoria = $categoria;
        $this->view->pagina = $pagina;
        $this->view->tipus = $tipus;
        $this->_helper->layout()->disableLayout();
    }

    public function categoriasAction(){
        $categoria = $this->_request->getParam("nomCat");
        $consulta = $this->_Categories->getCategoriaByName($categoria);
        $id = $consulta->id_categoria;
        $x=0;
        
        if($id==1){
            $this->_redirect("index/index");
        }else{
            @$data= date('Y/m/d/ H:i:s',strtotime("-1 month")) ;
            $this->view->noticiesFinals  = $this->_NoticiaTaula->getNoticiesCategoriaData($id,$x,$data);
            $this->view->cantitat = $this->_NoticiaTaula->cantitatCategori($id);
        }
        $this->view->idCategoria = $id;
        $this->view->mostra = 2;
        $this->render();
    }

    public function passwrenewAction(){
        $email = $this->_request->getParam("email_olvidada");
        $id = $this->_Users->getId($email);
        
        
        if ($id!=NULL){
           $newpass=substr(uniqid(rand(), true), 2, 8);
           $protectpass=sha1($newpass);
           $username2 = $this->_Users->getUsername($id);
           $this->_Users->setPassword($id,$protectpass);
           $this->sendLostPassword($email, $newpass, $username2);
           $responseComent = "La contraseña ha sido cambiada satisfactoriamente, mire su E-mail.";
           $this->_redirect("index/index/responseComent/$responseComent");
        }else{
            //ERROR: L'EMAIL INTRODUIT eS INCORRECTE.
            $this->view->responseLogin = "El Email introducido no exsiste";
           
        }
        //$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function sendLostPassword($email, $pass, $username2){

        $smtpServer = 'smtp.gmail.com';
        $username = 'service@newsup.es';
        $password = 'Zend05:c3';

        $fromadress = 'service@newsup.es';
        $fromname = 'NewsUP';

        $toadress = $email;

        $subject = 'News UP Recuperación de Contraseña';

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
					<img src="http://www.newsup.es/images/header-bg.jpg" name="Logo NewsUP" style="border:0px; border-style:none;">
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
                                                Servicio de Recuperación de Contraseña
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



    public function userAction(){
        $nomuser = $this->_request->getParam("nomUser");
        $id = $this->_Users->getIdUsername($nomuser);
        $x=0;
        $usuari = $this->_Users->getUsuari($id);

        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
        }
        $this->view->dadesSeguit = $this->_Users->getDadesById($id);
        $this->view->segueix = $this->_Seguidors->cantitatSegueix($id);
        $this->view->segueixen = $this->_Seguidors->cantitatSegueixen($id);
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesUsuariFora($id,$x);
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUserFora($id);
        $this->view->pagina = 1;
        if(count($usuari)==1){
            foreach($usuari as $user1){
                $this->view->userfora = $user1;
            }
        }

        $responseComent=null;
        $responseComent = $this->_request->getParam("responseComent");
        $this->view->responseComent = $responseComent;
        $this->render();
    }

    public function paginanextintAction(){
        $id = $this->_request->getParam("id");
        $pagina = $this->_request->getParam("pagina");
        $this->view->id = $id;
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUserFora($id);
        $x = ($pagina*15)-15;
        $this->view->noticiesMevas = $this->_NoticiaTaula->getNoticiesUsuariFora($id,$x);

        $this->view->pagina = $pagina;
        $this->_helper->layout()->disableLayout();
    }

    public function paginanextsegAction(){
        $id = $this->_request->getParam("id");
        $pagina = $this->_request->getParam("pagina");
        $this->view->id = $id;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueixen($id);
        $x = ($pagina*15)-15;
         $usersOk = array();
        $contador =0;
        $segueixen = $this->_Seguidors->getSegueixen($id,$x);
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

     public function paginanextseg2Action(){
        $id = $this->_request->getParam("id");
        $pagina = $this->_request->getParam("pagina");
        $this->view->id = $id;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueix($id);
        $x = ($pagina*15)-15;
         $usersOk = array();
        $contador =0;
        $segueixen = $this->_Seguidors->getSegueix($id,$x);
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


    public function lesiguenAction(){
        $id = $this->_request->getParam("id");
        $x=0;
        $segueixen = $this->_Seguidors->getSegueixen($id,$x);
        $this->view->id = $id;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueixen($id);
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
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
        }
        $this->view->usuaris2 = $usersOk;
        $this->_helper->layout()->disableLayout();
    }

    public function sigueAction(){
        $id = $this->_request->getParam("id");
        $x=0;
        $segueixen = $this->_Seguidors->getSegueix($id,$x);
        $this->view->id = $id;
        $this->view->cantitat = $this->_Seguidors->cantitatSegueix($id);
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
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
        }
        $this->view->usuaris2 = $usersOk;
        $this->_helper->layout()->disableLayout();
    }

    public function noticiasseguidorAction(){
        $id = $this->_request->getParam("id");
        $x=0;
        $this->view->noticiesFinals = $this->_NoticiaTaula->getNoticiesUsuariFora($id,$x);
        $this->view->cantitat = $this->_NoticiaTaula->cantitatUserFora($id);
        $this->view->pagina = 1;
        $usuari = $this->_Users->getUsuari($id);
        if(count($usuari)==1){
            foreach($usuari as $user1){
                $this->view->userfora = $user1;
            }
        }
        $this->_helper->layout()->disableLayout();
    }


    //Contactar Actions

    public function contactarAction(){
        $error = $this->_request->getParam("error");
        $username = $this->_request->getParam("username");
        $nom = $this->_request->getParam("nom");
        $email = $this->_request->getParam("email");
        $option = $this->_request->getParam("option");
        $missatge = $this->_request->getParam("missatge");

        //Zend_Debug::dump($responseLogin);
        $this->view->usernamepar = $username;
        $this->view->nompar = $nom;
        $this->view->emailpar = $email;
        $this->view->optionpar = $option;
        $this->view->missatgepar = $missatge;
        $this->view->error = $error;
        $this->render();
    }

    public function contactar2Action(){
        $username = $this->_request->getParam("username");
        $nom = $this->_request->getParam("nom");
        $email = $this->_request->getParam("email");
        $option = $this->_request->getParam("option");
        $missatge = $this->_request->getParam("missatge");

        $option2;
        switch($option){
            case 1:
                $option2="Sugerencia";
            break;
            case 2:
                $option2="Queja";
            break;
            case 3:
                $option2="Publicidad";
            break;
            case 4:
                $option2="Otros";
            break;
        }


        if($this->_Users->getUsernameOk($username)>0){
              require_once('recaptchalib.php');
              $privatekey = "6LeOu8ISAAAAAPmi9yINYW08St8TCrejIk_f44U1";

              $resp = recaptcha_check_answer ($privatekey,
                                            $_SERVER["REMOTE_ADDR"],
                                            $this->_request->getParam("recaptcha_challenge_field"),
                                            $this->_request->getParam("recaptcha_response_field"));


              if (!$resp->is_valid) {
                $this->_redirect("index/contactar/error/2/username/$username/nom/$nom/email/$email/option/$option/missatge/$missatge");
              }else{

                  $this->_Contactar->insertRow($username,$nom,$email,$option2,$missatge);

                  $this->sendContactar($username, $nom, $email, $option2, $missatge);

                  $responseComent = "Gracias por su Colaboración, en breve le contestaremos";
                  $this->_redirect("index/index/responseComent/$responseComent");
              }
        }else{
            $responseLogin = "Ese Usuario no Exsiste";
            $this->_redirect("index/index/responseComent/$responseLogin");
        }

    }

        public function sendContactar($username, $nom, $email2, $option2, $missatge2){

                $smtpServer = 'smtp.gmail.com';
                $username = 'support@newsup.es';
                $password = 'Zend05:c3';

                $fromadress = $email2;
                $fromname = $option2;

                $toadress = 'support@newsup.es';

                $subject = $option2;

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
                                                            <img src="http://www.newsup.es/images/header-bg.jpg" style="border:0px; " name="Logo NewsUP">
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
                                                                    '.$option2.'
                                                                    </h2>
                                                                    <!--break-->
                                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tr>
                                                                            <td height="10"></td>
                                                                        </tr>
                                                                    </table>
                                                                    <!--/break-->
                                                                    <p>
                                                                    USERNAME: <b>'.$username.'</b>
                                                                    </p>
                                                                    <p>
                                                                    NAME: <b>'.$nom.'</b>
                                                                    </p>
                                                                    <p>
                                                                    EMAIL: <b>'.$email2.'</b>
                                                                    </p>
                                                                    <p>
                                                                    <u>MESSAGE</u>
                                                                    </p>
                                                                    <p>
                                                                    '.$missatge2.'
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

    
    public function noticiasnotificacionAction(){

        $idNoticia =  $this->_request->getParam("idNoticia");

        $metanom = $this->_NoticiaTaula->getTitol($idNoticia);
        $metadescripcio = $this->_NoticiaTaula->getDescripcio($idNoticia);

        $this->view->metanom = $metanom;
        $this->view->metadescripcio = $metadescripcio;

        $this->view->noticia = $this->_NoticiaTaula->getNoticia($idNoticia);

        $this->canviarVisto();
        //ALEX---------------------------------------NOTIFICACIONES
        $this->render('noticias');
    }
    //ALEX---------------------------------------NOTIFICACIONES
    public function canviarVisto()
    {
        $user = UserModel::getIdentity();
        $idNotificacion = $this->_request->getParam("idNotificacion");
        $this->_Notificaciones->updateVisto($idNotificacion);
        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
        $this->view->notficaciones = $notificaciones;
    }
    //ALEX---------------------------------------NOTIFICACIONES
    public function usuariAction()
    {
        $nomuser = $this->_request->getParam("nomUser");
        $this->canviarVisto();
        $this->_redirect("index/user/nomUser/$nomuser");
    }

    public function recargarnotificacionAction()
    {
        $user = UserModel::getIdentity();

        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
        $this->view->notficaciones = $notificaciones;

        $this->_helper->layout()->disableLayout();
    }


    public function rankingAction()
    {
        $pagina = $this->_request->getParam("pagina");
        $userRankingOk = array();
        $limt = 0;
        $this->view->cantitat = $this->_NoticiaTaula->countRanking();
        $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingLimit($limt);
        $this->view->filtre = 1;
        $this->view->pagina = $pagina;
        foreach ($usuarisRankig as $userRow)
        {

            $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

            $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
            $userRow->getUser()->addNoticies($noticies);
            $userRow->getUser()->addSegueixen($segueixen);
            $userRow->getUser()->addSegueix($segueix);
            $userRankingOk[] = $userRow;

        }
        $this->view->pagina = 1;
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
            $this->view->posicioRanking = $this->buscarPosicio($user->id_user);
        }
        //Zend_debug::Dump($userRankingOk->getUser());
        //die;
        $this->view->ranking = $userRankingOk;
        $this->render();
    }
    public function mediapuntosAction()
    {

        $limt = 0;
        $pagina = $this->_request->getParam("pagina");
        $this->view->pagina = $pagina;
        if($pagina != null)
        {
            $this->view->pagina = $pagina;
            $limt = ($pagina*15)-15;
        }else{
            $this->view->pagina = 1;
            $limt = 0;
        }
        $this->view->filtre = 2;
        $this->view->cantitat = $this->_NoticiaTaula->countRanking();
        $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingMediaLimit($limt);
        $userRankingOk = array();
        //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRankingMedia();

        foreach ($usuarisRankig as $userRow)
        {

            $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

            $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
            $userRow->getUser()->addNoticies($noticies);
            $userRow->getUser()->addSegueixen($segueixen);
            $userRow->getUser()->addSegueix($segueix);
            $userRankingOk[] = $userRow;

        }
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
            $this->view->posicioRanking = $this->buscarPosicio2($user->id_user);

        }
        //Zend_debug::Dump($userRankingOk->getUser());
        //die;
        $this->view->ranking = $userRankingOk;
        $this->_helper->layout()->disableLayout();
    }

    public function buscarPosicio($idUser)
    {
        $cont = 0;
        $usuarisRankig = $this->_NoticiaTaula->getNoticiasRanking();
        foreach ($usuarisRankig as $userRow)
        {
            $cont++;
            if($userRow->getUser()->getId() == $idUser)
            {
                return $cont;
            }
        }
        return 0;
    }

    public function buscarPosicio2($idUser)
    {
        $cont = 0;
        $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingMedia();
        foreach ($usuarisRankig as $userRow)
        {
            $cont++;
            if($userRow->getUser()->getId() == $idUser)
            {
                return $cont;
            }
        }
        return 0;
    }

    public function rankingpuntosAction()
    {

        $pagina = $this->_request->getParam("pagina");


        if($pagina != null)
        {
            $this->view->pagina = $pagina;
            $limt = ($pagina*15)-15;
        }else{
            $this->view->pagina = 1;
            $limt = 0;
        }

        $this->view->filtre = 1;
        $this->view->cantitat = $this->_NoticiaTaula->countRanking();
        $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingLimit($limt);
        $userRankingOk = array();
        //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRanking();

        foreach ($usuarisRankig as $userRow)
        {

            $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
            $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

            $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
            $userRow->getUser()->addNoticies($noticies);
            $userRow->getUser()->addSegueixen($segueixen);
            $userRow->getUser()->addSegueix($segueix);
            $userRankingOk[] = $userRow;

        }
        if (UserModel::isLoggedIn()) {
            $user = UserModel::getIdentity();
            $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
            $this->view->posicioRanking = $this->buscarPosicio($user->id_user);
        }
        //Zend_debug::Dump($userRankingOk->getUser());
        //die;
        $this->view->ranking = $userRankingOk;
        $this->_helper->layout()->disableLayout();
    }
    
    public function paginaciorankingAction(){

        $pagina = $this->_request->getParam("pagina");
        $filtre = $this->_getParam('filtre');

        $this->view->cantitat = $this->_NoticiaTaula->countRanking();
        $this->view->pagina = $pagina;
        $limt = ($pagina*15)-15;
        if($filtre == 1)
        {
            $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingLimit($limt);
            $userRankingOk = array();
            //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRanking();

            foreach ($usuarisRankig as $userRow)
            {

                $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());//posible mejor eliminando la linia
                $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

                $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
                $userRow->getUser()->addNoticies($noticies);//posible mejora poniendo $userRow->news
                $userRow->getUser()->addSegueixen($segueixen);
                $userRow->getUser()->addSegueix($segueix);
                $userRankingOk[] = $userRow;

            }
            if (UserModel::isLoggedIn()) {
                $user = UserModel::getIdentity();
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
                $this->view->posicioRanking = $this->buscarPosicio($user->id_user);
            }
            //Zend_debug::Dump($userRankingOk->getUser());
            //die;
            $this->view->ranking = $userRankingOk;
            $this->view->filtre = 1;

        }else{
            $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingMediaLimit($limt);
            $userRankingOk = array();
            //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRankingMedia();

            foreach ($usuarisRankig as $userRow)
            {

                $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
                $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

                $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
                $userRow->getUser()->addNoticies($noticies);
                $userRow->getUser()->addSegueixen($segueixen);
                $userRow->getUser()->addSegueix($segueix);
                $userRankingOk[] = $userRow;

            }
            if (UserModel::isLoggedIn()) {
                $user = UserModel::getIdentity();
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
                $this->view->posicioRanking = $this->buscarPosicio2($user->id_user);

            }
            //Zend_debug::Dump($userRankingOk->getUser());
            //die;
            $this->view->ranking = $userRankingOk;
            $this->view->filtre = 2;
        }

        $this->_helper->layout()->disableLayout();
    }

    public function paginarankingAction()
    {
        $posicio = $this->_getParam('posicio');
        $filtre = $this->_getParam('filtre');
        $pagina = ceil($posicio/15);
        $limt = ($pagina*15)-15;
        $this->view->cantitat = $this->_NoticiaTaula->countRanking();
        $this->view->pagina = $pagina;
        if($filtre == 1)
        {
            $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingLimit($limt);
            $userRankingOk = array();
            //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRanking();

            foreach ($usuarisRankig as $userRow)
            {

                $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
                $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

                $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
                $userRow->getUser()->addNoticies($noticies);
                $userRow->getUser()->addSegueixen($segueixen);
                $userRow->getUser()->addSegueix($segueix);
                $userRankingOk[] = $userRow;

            }
            if (UserModel::isLoggedIn()) {
                $user = UserModel::getIdentity();
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
                $this->view->posicioRanking = $this->buscarPosicio($user->id_user);
            }
            //Zend_debug::Dump($userRankingOk->getUser());
            //die;
            $this->view->ranking = $userRankingOk;
            $this->view->filtre = 1;

        }else{
            $usuarisRankig = $this->_NoticiaTaula->getNoticiasRankingMediaLimit($limt);
            $userRankingOk = array();
            //$usuarisRankig = $this->_NoticiaTaula->allNoticiasRankingMedia();

            foreach ($usuarisRankig as $userRow)
            {

                $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getUser()->getId());
                $segueix = $this->_Seguidors->cantitatSegueix($userRow->getUser()->getId());

                $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getUser()->getId());
                $userRow->getUser()->addNoticies($noticies);
                $userRow->getUser()->addSegueixen($segueixen);
                $userRow->getUser()->addSegueix($segueix);
                $userRankingOk[] = $userRow;

            }
            if (UserModel::isLoggedIn()) {
                $user = UserModel::getIdentity();
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
                $this->view->posicioRanking = $this->buscarPosicio2($user->id_user);

            }
            //Zend_debug::Dump($userRankingOk->getUser());
            //die;
            $this->view->ranking = $userRankingOk;
            $this->view->filtre = 2;
        }

        $this->_helper->layout()->disableLayout();
    }


    public function newsupAction(){
        $this->render();
    }

    public function terminosAction(){
       $this->render();
    }

    public function privacidadAction(){
        $this->render();
    }

    public function faqAction(){
        $this->render();
    }
}

