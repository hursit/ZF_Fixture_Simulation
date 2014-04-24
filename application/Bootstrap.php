<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_memberType = null;
    
    //Sistemimizde backend ve guest olarak iki oturum tipi tutuyoruz bu site icin.
    //Kucuk sistem oldugu icin bunu controller yardimiyla ayirabiliriz.
    //Eger buyuk capli bir sistem olsaydi bunu module yardimiyla ayirmamiz gerekirdi
    //Ve ayni zamanda buna gore ACL pluginini elle yazmamiz gerekecekti.
    
    //Bu fonksiyon ile uye tipini belirliyoruz. Buna gore yonlendirmeler,layout secmeler 
    //yapilacak.
    public function _initMemberType(){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $this->_memberType = "backend";
        }else {
            $this->_memberType = "default";
        }
    }
    
    //Bu fonksiyon ile default yani guest kullanicinin backend(admin) panele erismesini engelliyoruz
    public function _initRedirects(){
        $url = explode('/', $_SERVER['REQUEST_URI']);
        $frontController = Zend_Controller_Front::getInstance();
        $response = new Zend_Controller_Response_Http();
       
        if($this->_memberType == "default" && in_array("backend",$url)){
            $response->setRedirect('/');
            $frontController->setResponse($response);
        }
    }
    
    //Bu bolumde layout'umuzu seciyoruz ve ekliyoruz...
    protected function _initLayout(){
        $layout = null;
        $url = explode('/', $_SERVER['REQUEST_URI']);
        
        if(in_array('backend', $url)){
            $layout = 'backend';
        }else {
           $layout = 'default';
        }
          $options = array(
                 'layout'     => 'layout',
                 'layoutPath' => APPLICATION_PATH."/layouts/".$layout."/scripts"
          );
        Zend_Layout::startMvc($options);
    }
    protected function _initRoutes(){
        $this->bootstrap('frontController');
        $frontController = $this->getResource('frontController');
        $router = $frontController->getRouter(); 
        
        $default_route = new Zend_Controller_Router_Route(
            ':controller/:action/:id',
            array(
                'controller' => 'index',
                'action'     => 'index',
                'id' => 'id'
            )
        );
        $router->addRoute('default_route', $default_route);
       
    }
}

