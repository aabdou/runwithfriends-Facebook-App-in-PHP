<?php

class IndexController extends Zend_Controller_Action
{
    private $facebook = null;   
    
    /**
      * Init the Facebook object and redirect to the login page if the user is no
      * valid user session.
      */  
    public function preDispatch()
    {        
        //Init the facebook object and get the user session.
        $facebookSettings = $this->getInvokeArg('bootstrap')->getOption('facebook');        
        $this->facebook = new FB_Facebook($facebookSettings);        
        $session = $this->facebook->getSession();  
        
        //If there is no valid session, take the user to the welcome action (login).        
        if($this->_request->getActionName() != 'welcome' && !$session) {
            $this->_request->setActionName('welcome');
        }
        else {        
            $facebookSettings['session'] = $session;
        }
        
        //Get the Layout, and set the FB init values (we need that to init the FB app).      
        $layout = Zend_Layout::getMvcInstance();
        unset($facebookSettings['secret']); //DON'T SEND the SECRET TO THE PUBLIC.
        $layout->assign('facebook', $facebookSettings);
    }    
     

    /**
      *  Display the user's recent runs.
      */
    public function indexAction()
    {
        $form = new Application_Form_Addrun();
        
        //Get the cache and see if we have the user object there.
        $cache = $this->getInvokeArg('bootstrap')
                      ->getResource('cachemanager')                      
                      ->getCache('fbCache');  
        
        //If there is no cache, exit the app.
        if(!$cache) {
            throw new Zend_Controller_Action_Exception('An error occured. Please try again later', 404); 
        }
        
        //If the user object is not in the cache, reload it else read it from the cache.
        if(!$cache->test('user')) {
            $user = new Application_Model_User($this->facebook);
            $user->refreshData();
            $cache->save($user, 'user');
        }
        else {
            $user = $cache->load('user');
        }                          
        
        $this->view->form = $form;
    }
    

    /**
     *  Display a welcome screen and a login button.
     */
    public function welcomeAction()
    {
        
    }
}
