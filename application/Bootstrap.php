<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
     protected function _initView()
     {
        // Initialize view
            $view = new Zend_View();
            $view->doctype('XHTML1_STRICT');
            $view->headTitle('NewsUp');

            $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
                'ViewRenderer'
            );
            $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
            return $view;
     }

     protected function _initDbCharset()
     {
         $resource = $this->getPluginResource('db');
         $db = $resource->getDbAdapter();
         $db->query("SET NAMES UTF8");
     }

    protected function _initRouter()
    {
        $this->bootstrap('frontController');
        $router = $this->frontController->getRouter();

        $route = new Zend_Controller_Router_Route(
            '/noticia/:titolNoticia',
            array(
                'action'     => 'noticias',
                'controller' => 'index',
                'module'     => 'default'
            )
        );
        $router->addRoute('noticias', $route);

        $route = new Zend_Controller_Router_Route(
            '/usuario/:nomUser',
            array(
                'action'     => 'user',
                'controller' => 'index',
                'module'     => 'default'
            )
        );
        $router->addRoute('user', $route);

       $route = new Zend_Controller_Router_Route(
            '/buscar/:titolEmail',
            array(
                'action'     => 'index',
                'controller' => 'email',
                'module'     => 'default'
            )
        );
        $router->addRoute('email', $route);

        $route = new Zend_Controller_Router_Route(
            '/categoria/:nomCat',
            array(
                'action'     => 'categorias',
                'controller' => 'index',
                'module'     => 'default'
            )
        );
        $router->addRoute('categorias', $route);

        $route = new Zend_Controller_Router_Route(
            '/colocar/:titolNoticia',
            array(
                'action'     => 'colocar',
                'controller' => 'map',
                'module'     => 'default'
            )
        );
        $router->addRoute('colocar', $route);

        $route = new Zend_Controller_Router_Route(
            '/ver/:titolNoticia',
            array(
                'action'     => 'ver',
                'controller' => 'map',
                'module'     => 'default'
            )
        );
        $router->addRoute('ver', $route);

        $route = new Zend_Controller_Router_Route(
            '/cambiar/:titolNoticia',
            array(
                'action'     => 'cambiar',
                'controller' => 'map',
                'module'     => 'default'
            )
        );
        $router->addRoute('cambiar', $route);


        $router = $this->frontController->getRouter();



    }

}

