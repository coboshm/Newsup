<?php
require_once 'BD/Users.php';
require_once 'BD/NoticiaTaula.php';
require_once 'Usuari.php';

class RegistrarController extends Zend_Controller_Action
{

    private $_Users;
    private $_NoticiaTaula;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->_Users = new Users();
        $this->_NoticiaTaula = new NoticiaTaula();
    }

    public function preDispatch(){

        //$this->view->ranking = $this->_NoticiaTaula->allNoticiasRanking();
    }

    public function indexAction()
    {
        $this->view->mostra = 4;
        $error=null;


        $error = $this->_request->getParam("error");


        //Zend_Debug::dump($responseLogin);
        $this->view->error = $error;

    }


    public function afegirAction(){
      require_once('recaptchalib.php');

        $nombre = $this->_request->getParam("nombre");
        $email = $this->_request->getParam("email");
        $username = $this->_request->getParam("username");
        $password = $this->_request->getParam("password");
        $error=0;
        $data = @date('Y-m-d H:i:s');

        if($nombre=="" || $nombre==null){
            $this->view->error = 3;
            $error = 1;
        }

        if($username=="" || $username==null){
            $this->view->error = 3;
            $error = 1;
        }

        if($password=="" || $password==null){
            $this->view->error = 3;
            $error = 1;
        }

        if(!preg_match("/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/", $email)){
            //Zend_Debug::dump($emial);
            $this->view->error = 3;
            $error = 1;
        }

        if($error==1){
            $this->_redirect("index/index/tipus/3");
        }


      $privatekey = "6LeOu8ISAAAAAPmi9yINYW08St8TCrejIk_f44U1";

      $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $this->_request->getParam("recaptcha_challenge_field"),
                                    $this->_request->getParam("recaptcha_response_field"));


      if (!$resp->is_valid) {
            $this->_redirect("index/index/tipus/2");
      } else {
        
        //Comprovarem si hi ha un email ja amb aquest email
        $emailok = $this->_Users->getEmail($email);
        $usernameok = $this->_Users->getUsernameOk($username);
        $error=0;
        if($emailok!=0){
         
            $this->_redirect("index/index/tipus/1");
        }else if($usernameok !=0){
            
             $this->_redirect("index/index/tipus/1");
        }else{
            //Zend_Debug::dump($password1);
            $encript_password = sha1($password);
            

            $registrat = $this->_Users->setClientNou($nombre,$email,$encript_password,$username,$data);
            //Zend_Debug::dump($registrat);
            $user = $this->_Users->getDadesByEmail($email);
            //Zend_Debug::dump($user);
            $encript_id = sha1($user->getId());
            //Zend_Debug::dump($encript_id);
            $this->_Users->setCodi($user->getId(), $encript_id);
            $codi = $encript_id;
            //Zend_Debug::dump($codi);
            //die();
            $this->emailRegister($email, $password, $username, $nombre, $codi);

        }
        
        $this->render();
      }
      
        
        
    }

    public function camposAction(){
        $this->view->error = $this->_request->getParam("tipus");
        $this->render();
    }



     public function emailRegister($email, $pass, $username2, $nom,  $codi){

        $smtpServer = 'smtp.gmail.com';
        $username = 'service@newsup.es';
        $password = 'Zend05:c3';

        $fromadress = 'service@newsup.es';
        $fromname = 'NewsUP';

        $toadress = $email;

        $subject = 'News UP Registro';

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
                                                <h2 style="color: #66990; font-size: 21px; font-family:Georgia, \'Times New Roman\', Times, serif; margin: 0px; padding: 0; text-shadow: 1px 1px 1px #fff;">
                                                Bienvenido/a a un nuevo mundo de Información
                                                </h2>
                                                <!--break-->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td height="10"></td>
                                                    </tr>
                                                </table>
                                                <!--/break-->
						<p>
						Hola, '.$nom.'. Gracias por registrarse a NewsUP.
						</p>
						<p>
						Le damos la bienvenida a esta comunidad. Esperamos que sea de su agrado.
						</p>
						<p>
						Sus datos son los siguientes:
						</p>
						<p>
						USUARIO: <b>'.$username2.'</b>
						</p>
						<p>
						CONTRASEÑA: <b>'.$pass.'</b>
						</p>
                                                <p style="font-size=15;">
                                                Ahora solo le queda un paso para completar el registro: Active su cuenta haciendo clic al siguiente enlace: <a href="http://www.newsup.es/registrar/validate/key/'.$codi.'">http://www.newsup.es/registrar/validate/key/'.$codi.'</a></b>
                                                </p>
						<p>
						NewsUP nos gusta estar cerca de los usuarios, por esto les dejamos un servicio en la web llamado Contacta. Si tiene alguna duda o le gustaría comentar algo, sea ya criticar constructivamente o aportar su granito de arena, estaremos encantados a escucharle.
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

    public function validateAction() {
	//http://127.0.0.1/newsup/public/index/validate/key/12
	$codi = $this->_getParam("key");
        $user = $this->_Users->getCodi($codi);

        if($user!=null){
            $this->_Users->setActivat($user->getId());
            $this->emailValidate($user->getEmail(), $user->getUsername());
        }
        else{
            $this->view->error = 1;
            $error=1;
        }
        $responseComent = "Su cuenta ha sido validada correctamente. Ya puede loguearse!";
        $this->_redirect("index/index/responseComent/$responseComent");
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
    }

    public function emailValidate($email, $username2){

        $smtpServer = 'smtp.gmail.com';
        $username = 'service@newsup.es';
        $password = 'Zend05:c3';

        $fromadress = 'service@newsup.es';
        $fromname = 'NewsUP';

        $toadress = $email;

        $subject = 'News UP Cuenta habilitada';

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
                                                <h2 style="color: #66990; font-size: 21px; font-family:Georgia, \'Times New Roman\', Times, serif; margin: 0px; padding: 0; text-shadow: 1px 1px 1px #fff;">
                                                Cuenta habilitada. Bienvenido/a a NewsUP
                                                </h2>
                                                <!--break-->
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td height="10"></td>
                                                    </tr>
                                                </table>
                                                <!--/break-->
						<p>
						Hola, '.$username2.'. Gracias por registrarse a NewsUP.
						</p>
						<p>
						Su cuenta fue <span style="color: #669900;">habilitada</span> satisfactoriamente.
						</p>
						<p>
						Ya puede loguearse a NewsUP con sus datos. Esperamos que disfrute.
						</p>
						<p>
						Y recuerde, NewsUP nos gusta estar cerca de los usuarios, por esto le dejamos un servicio en la web llamado Contacta. Si tiene alguna duda o le gustaría comentar algo, sea ya criticar constructivamente o aportar su granito de arena, estaremos encantados a escucharle.
						</p>
                                                <p>
                                                NewsUP guarda los datos de los usuarios de forma segura y confidencial. Más información al apartado de Términos de Servicio.
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



 

}

