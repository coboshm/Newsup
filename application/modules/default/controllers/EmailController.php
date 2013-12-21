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
require_once 'facebook/facebook.php';
require_once 'msn/windowslivelogin.php';
require_once 'msn/settings.php';
require_once 'Notificacion.php';
require_once 'BD/Notificaciones.php';
require_once 'BD/TipoNotificacion.php';

class EmailController extends Zend_Controller_Action
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
                $this->view->loggedIn = true;
                $this->view->user = UserModel::getIdentity();
                $user = UserModel::getIdentity();
                $usuari = $this->_Users->getUsuari($user->id_user);
                
                if(count($usuari)==1){
                    foreach($usuari as $user1){
                        $this->view->usuario = $user1;
                        
                        $dades = $this->_Users->getDadesById((int)$user1->getId()); 
                        //Zend_Debug::dump($user1);
                        //die;
                        $this->view->dadesUser = $dades;
                    }
                }
                //ALEX---------------------------------------NOTIFICACIONES
                $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                $this->view->notficaciones = $notificaciones;
                $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);
        
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

                                $dades = $this->_Users->getDadesById((int)$user1->getId());
                                //Zend_Debug::dump($user1);
                                //die;
                                $this->view->dadesUser = $dades;
                            }
                        }
                        //ALEX---------------------------------------NOTIFICACIONES
                        $notificaciones = $this->_Notificaciones->getNotificacionByUserNoVisto($user->id_user);
                        $this->view->notficaciones = $notificaciones;
                        $this->view->seguiments= $this->_Seguidors->getSeguidorsUser($user->id_user);

                }
            }else{
                $responseLogin = "Tienes que hacer login para acceder aqui";
                $this->_redirect("index/index/responseLogin/$responseLogin");
            }
        }
    }
    
    public function indexAction()
    {

        //Tema Gmail
        $limit       = 500; // maximo de resultados
        $orderby     = 'lastmodified'; // de momento solo se puede ordenar por ultimo modificado
        $sortorder   = 'descending'; // ascending o descending
        
        $nextUrl = 'http://www.newsup.es/email/mostra';
        $scope = 'http://www.google.com/m8/feeds/contacts/default/thin?max-results=' . $limit . '&orderby=' . $orderby . '&sortorder=' . $sortorder;
        $secure = 0;  // set $secure=1 to request secure AuthSub tokens
        $session = 1;
        $authSubUrl = Zend_Gdata_AuthSub::getAuthSubTokenUri($nextUrl, $scope, $secure, $session);
        $this->view->auth_url = $authSubUrl;

        //Tema Facebook
          // Create our Application instance.
        $facebook = new Facebook(array(
          'appId' => '110386902375619',
          'secret' => 'ad710eea1008d26574bd5ce993042b90',
          'cookie' => true,
        ));
        // Extraemos la sesiÃ³n, sin ella no sabremos si graph va a funcionar
        $fbSession = $facebook->getSession();

          $url = $facebook->getLoginUrl(array(
                'canvas' => 0,
                'fbconnect' => 1,
                'next' =>  'http://www.newsup.es/email/facebook/',
                'cancel_url' =>  'http://www.newsup.es/email/facebook',
                'req_perms' => 'read_stream,publish_stream,email,read_friendlists'
          ));
          //echo '<a href= "'.$url.'">Enviar</a>';
          $this->view->urlFacebook = $url;


           include "msn/settings.php";

            // Initialize the WindowsLiveLogin module.
            $wll = WindowsLiveLogin::initFromXml($KEYFILE);
            $wll->setDebug($DEBUG);

            //Get the consent URL for the specified offers.
            $consenturl = $wll->getConsentUrl($OFFERS);
            $this->view->url = $consenturl;

            $token = null;



          $this->view->rightBuscaOut = 1;
          $this->render();

    }
    
    public function mostraAction()
    {       
        // se confirmado la solicitud ?
         $limit       = 500; // maximo de resultados
        $orderby     = 'lastmodified'; // de momento solo se puede ordenar por ultimo modificado
        $sortorder   = 'descending'; // ascending o descending
        $schema_url  = 'http://schemas.google.com/g/2005';
        $t_emails    = array();
         $request_url = 'http://www.google.com/m8/feeds/contacts/default/thin?max-results=' . $limit . '&orderby=' . $orderby . '&sortorder=' . $sortorder;
        
        if( isset( $_GET['token'] ) && $_GET['token'] ) {

            $curl = curl_init($request_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: AuthSub token=\"" . $_GET['token'] .  "\""));
            $response = curl_exec($curl);
            curl_close($curl);
            
            $usuersBd = $this->_Users->allUsers();
            $user = UserModel::getIdentity();
            $emailVista = array();
            $emailNoVista = array();
            $usuarisEmail = array();
            if($response){
                $entries = new SimpleXMLElement($response);
                if($entries){
                    //bucle para cojer todos los emails del usuario
                    foreach ($entries->entry as $entry ) {
                        $defaults = $entry->children($schema_url);
                        if(@$defaults->email->attributes()->address!=""){
                            if ($user->email != @$defaults->email->attributes()->address){
                                $t_emails[] = @(string)$defaults->email->attributes()->address;
                            }
                        }
                    }
                    $contador = 0;
                    //bucle para comparar i guardar por una parte los usuarios que tenemos nosotros i lo usuarios que no tenemos
                    for($x = 0; $x<count($usuersBd); $x++)
                    {
                        for($y = 0; $y<count($t_emails); $y++)
                        {
                            if($usuersBd[$x]->getEmail() == $t_emails[$y]){
                              $usuarisEmail[$contador] = $this->_Users->getDadesByEmail($t_emails[$y]);
                              $noticies = $this->_NoticiaTaula->cantitatUserFora($usuarisEmail[$contador]->getId());
                              $segueix = $this->_Seguidors->cantitatSegueix($usuarisEmail[$contador]->getId());
                              $segueixen = $this->_Seguidors->cantitatSegueixen($usuarisEmail[$contador]->getId());
                              $usuarisEmail[$contador]->addNoticies($noticies);
                              $usuarisEmail[$contador]->addSegueixen($segueixen);
                              $usuarisEmail[$contador]->addSegueix($segueix);
                              $noticies = null;
                              $segueix=null;
                              $segueixen=null;
                              $t_emails[$y] = null;
                              $contador++;
                            }
                        }
                    }
                   
                    foreach ($t_emails as $emailRow)
                    {
                        if($emailRow != null)
                        {
                            $emailNoVista[] = $emailRow;
                        }
                    }
                    
                    //codigo para enviar 3 emails aleatoriamente
                    $emailAleatorio = array();
                    for($x = 0; $x<2; $x++){
                        $aleatorio = rand(0,count($emailNoVista));
                        $emailAleatorio[] = $emailNoVista[$aleatorio];
                    }
                }
            }
        } 

        //enviem els emails

        //$conta=0;
        foreach ($emailAleatorio as $emailEnviar){
            $this->emailBusca($emailEnviar);
        }

        $this->view->amics = $usuarisEmail;
        //mostrem per pantalla
        $this->render("freinds");
       
    }

    public function freindsAction()
    {

        
    }

    public function emailBusca($email){
         $smtpServer = 'smtp.gmail.com';
        $username = 'service@newsup.es';
        $password = 'Zend05:c3';

        $fromadress = 'service@newsup.es';
        $fromname = 'NewsUP';

        $toadress = $email;

        $subject = 'NewsUP - Alguien le ha buscado';

        $message = '<html>
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
                                                <h2 style="color: #66990; font-size: 21px; font-family:Georgia, \'Times New Roman\', Times, serif; margin: 0px; padding: 0; text-shadow: 1px 1px 1px #fff;">
                                                ¡Alguien le ha buscado en <a href="www.newsup.es"<span style="color: #669900;">NewsUP</span></a>!
                                                </h2>
                                                <!--break-->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td height="10"></td>
                                                    </tr>
                                                </table>
                                                <!--/break-->
						<p>
						Hola,
						</p>
						<p>
						Algún usuario le ha estado buscando en NewsUP.
						</p>
						<p>
						Si aún no sabe que es NewsUP le invitamos a que entre y lo descubra.
						</p>
						<p>
						NewsUP es un servicio web de noticias que le permite compartir sus noticias con el resto del mundo.
						</p>
                                                <p>
						Podrá colocar su noticia en el mapa terrestre, puntuar noticias de otros usuarios...
                                                </p>
						En resumen, tendrá todas las noticias del mundo en un solo clic. <a href="www.newsup.es"><span style="color: #669900;">www.newsup.es</span></a>
						<p>
						¿Aún no formas parte de NewsUP? ¿A qué esperas?
						</p>
						<p>
						
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

    public function facebookAction(){

         $facebook = new Facebook(array(
          'appId' => '110386902375619',
          'secret' => 'ad710eea1008d26574bd5ce993042b90',
          'cookie' => true,
        ));
        // Extraemos la sesiÃ³n, sin ella no sabremos si graph va a funcionar
        $fbSession = $facebook->getSession();
        // Usando la Api
        // Primero validamos que la sesiÃ³n haya sido iniciada correctamente...
        if ($fbSession )
        {
          // el php sdk usa excepciones php asÃ­ que al usarlo lo hacemos dentro de un try para capturarlas...
          try
          {
            // para rescatar tus propios datos de usuario...
            $myUser = $facebook->api('/me');
            // y ahora a por los amigos...
            $myFriends = $facebook->api('/me/friends');
            //echo '<p>Y estas las de mis amigos...</p>';
          //echo "<ul id='lista-de-amigos'>";

            $usuersBd = $this->_Users->allUsers();
            $amigosVista = array();
            foreach ($usuersBd as $userRow)
            {
                foreach ($myFriends['data'] as $amigoRow)
                {
                     if((strtolower($userRow->getNomCognoms()) == strtolower($amigoRow['name']))||(strtolower($userRow->getNomCognom()) == strtolower($amigoRow['name']))){
                          $noticies = $this->_NoticiaTaula->cantitatUserFora($userRow->getId());
                          $segueix = $this->_Seguidors->cantitatSegueix($userRow->getId());
                          $segueixen = $this->_Seguidors->cantitatSegueixen($userRow->getId());
                          $userRow->addNoticies($noticies);
                          $userRow->addSegueixen($segueixen);
                          $userRow->addSegueix($segueix);
                          $amigosVista[] = $userRow;
                     }
                }
            }
            

            // Publicando en el muro del usuario
             $newPostId = $facebook->api( '/me/feed', 'POST', array (
                // configuraciÃ³n del array para hacer un post en http://developers.facebook.com/docs/reference/api/post
                'message' => 'Os he estado buscando en NewsUP!',
                'link' => 'www.newsup.es',
                'name' => 'Newsup Noticias! - La nueva forma de vivir las noticias',
                'picture' => 'http://www.newsup.es/images/newsupgran.jpg'
             ));
              
          }
          catch (FacebookApiException $e)
          {
            // Funciones propias para capturar el error de la excepcion "$e"
            //   var_dump($e);
          }
        
            //mostrem per pantalla
            $this->view->amics = $amigosVista;
        //mostrem per pantalla
        $this->render("freinds");
        }
    }

    public function mostrarmsnAction()
    {
        include "msn/settings.php";
        /**
         * This page handles the 'delauth' Delegated Authentication action.
         * When you create a Windows Live application, you must specify the URL
         * of this handler page.
         */

        // Initialize the WindowsLiveLogin module.
        $wll = WindowsLiveLogin::initFromXml($KEYFILE);
        $wll->setDebug($DEBUG);

        // Extract the 'action' parameter, if any, from the request.
        $action = $_REQUEST['action'];
        
        if($action == 'delauth') {
          $consent = $wll->processConsent($_REQUEST);

        // If a consent token is found, store it in the cookie that is
        // configured in the settings.php file and then redirect to
        // the main page.
          if ($consent) {
            setcookie($COOKIE, $consent->getToken(), $COOKIETTL);
          }
          else {
            setcookie($COOKIE);
          }
        }
       // $token = $this->_getParam('ConsentToken');
        $cookie = $_COOKIE[$COOKIE];
        if ($cookie) {
            $token = $wll->processConsentToken($cookie);
        }

        if ($token && !$token->isValid()) {
            $token = null;
        }
        if ($token)
        {
            // Convert Unix epoch time stamp to user-friendly format.
            $expiry = $token->getExpiry();
            $expiry = date(DATE_RFC2822, $expiry);


            //*******************CONVERT HEX TO DOUBLE LONG INT ***************************************
            $hexIn = $token->getLocationID();
            include "msn/hex.php";
            $longint=$output;		//here's the magic long integer to be sent to the Windows Live service

            //*******************CURL THE REQUEST ***************************************
            $uri = "https://livecontacts.services.live.com/users/@C@".$output."/LiveContacts";
            $session = curl_init($uri);

            curl_setopt($session, CURLOPT_HEADER, true);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_USERAGENT, "Windows Live Data Interactive SDK");
            curl_setopt($session, CURLOPT_HTTPHEADER, array("Authorization: DelegatedToken dt=\"".$token->getDelegationToken()."\""));
            curl_setopt($session, CURLOPT_VERBOSE, 1);
    //	curl_setopt($session, CURLOPT_HTTPPROXYTUNNEL, TRUE);
    //	curl_setopt($session, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    //	curl_setopt($session, CURLOPT_PROXY,$PROXY_SVR);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($session, CURLOPT_TIMEOUT, 120);
            $response_h = curl_exec($session);
            curl_close($session);

            //*******************PARSING THE RESPONSE ****************************************************
            $response=strstr($response_h,"<?xml version");

            try {
            $xml = new SimpleXMLElement($response);
            }
            catch (Exception $e) {
                //aqui render a error
            }

            $t_emails = array();
            $usuersBd = $this->_Users->allUsers();
            $user = UserModel::getIdentity();
            $emailNoVista = array();
            $usuarisEmail = array();

            $lengthArray=count($xml->Contacts->Contact);
            for ($i=0;$i<$lengthArray;$i++)
            {
                // convertir objeto xml a string
                $firstname = (string)$xml->Contacts->Contact[$i]->Profiles->Personal->FirstName;
                $lastname  = (string)$xml->Contacts->Contact[$i]->Profiles->Personal->LastName;
                $email     = @(string)$xml->Contacts->Contact[$i]->Emails->Email->Address;

                // a veces el contacto no tiene ningun mail asociado, es posible que la cuenta estÃ© desactivada pero sigue como contacto
                if($email){
                    $t_emails[] = $email;
                }
            }

            //bucle para comparar i guardar por una parte los usuarios que tenemos nosotros i lo usuarios que no tenemos
            $contador = 0;
            
            for($x = 0; $x<count($usuersBd); $x++)
            {
                for($y = 0; $y<count($t_emails); $y++)
                {
                    if($usuersBd[$x]->getEmail() == $t_emails[$y]){

                      $usuarisEmail[$contador] = $this->_Users->getDadesByEmail($t_emails[$y]);
                      $noticies = $this->_NoticiaTaula->cantitatUserFora($usuarisEmail[$contador]->getId());
                      $segueix = $this->_Seguidors->cantitatSegueix($usuarisEmail[$contador]->getId());
                      $segueixen = $this->_Seguidors->cantitatSegueixen($usuarisEmail[$contador]->getId());
                      $usuarisEmail[$contador]->addNoticies($noticies);
                      $usuarisEmail[$contador]->addSegueixen($segueixen);
                      $usuarisEmail[$contador]->addSegueix($segueix);
                      $noticies = null;
                      $segueix=null;
                      $segueixen=null;
                      $t_emails[$y] = null;
                      $contador++;

                    }
                }
            }

            foreach ($t_emails as $emailRow)
            {
                if($emailRow != null)
                {
                    $emailNoVista[] = $emailRow;
                }
            }

            //codigo para enviar 3 emails aleatoriamente
            $emailAleatorio = array();
            for($x = 0; $x<4; $x++){
                $aleatorio = rand(0,count($emailNoVista));
                $emailAleatorio[] = $emailNoVista[$aleatorio];
            }

            //$conta=0;
            foreach ($emailAleatorio as $emailEnviar){
                $this->emailBusca($emailEnviar);
            }
            
            $this->view->amics = $usuarisEmail;
            
        }
        $this->render("freinds");
    }

}

