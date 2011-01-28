<?php

class IndexController extends Zend_Controller_Action
{
    /**
     * The interface to the Facebook APIs.
     * @var FB_Facebook
     */
    private $facebook = null;   
    
    /**
     * App bootstrap object.
     * @var Zend_Application_Bootstrap_Bootstrap
     */
    private $bootstrap = null;  
    
    /**
     * It wraps the info of the current user if there is a valid FB session.
     * @var Application_Model_User
     */  
    private $user = null;
    
    /**
      * Common controller init.
      */
    public function init()
    {
        //Get the bootstrap (so we easily read the config in the controller).
        $this->bootstrap = $this->getInvokeArg('bootstrap');
        
        //Init the facebook object.
        $facebookSettings = $this->bootstrap->getOption('facebook');        
        $this->facebook = new FB_Facebook($facebookSettings);        
        
        //Do we have a valid FB session?
        $session = $this->facebook->getSession();
        if($session) {
            $facebookSettings['session'] = $session;            
            $this->user = $this->getUser();
        }        
        
        //Clear the secret so we don't print it on the page.
        unset($facebookSettings['secret']);
        
        //Send the FB settings and the user object to the layout.
        Zend_Layout::getMvcInstance()->assign('facebook', $facebookSettings)
                                     ->assign('user', $this->user);
    }   
    
    /**
      * Redirect to the login page if there is no valid FB session.
      */  
    public function preDispatch()
    {           
        if($this->_request->getActionName() != 'welcome' && !$this->user) {
            $this->_request->setActionName('welcome')->setDispatched(false);
        }    
    }         

    /**
      *  Display the user's recent runs and save a run.
      */
    public function indexAction()
    {           
        $form = new Application_Form_Addrun();
        $runTable = new Application_Model_DbTable_Run();
        
        //Do we have a form post? filter, validate and insert a run.
        if($this->_request->isPost() && $this->_request->getPost('no_csrf_fb') && $form->isValid($this->_request->getPost())) {            
            $values = $form->getValues();
            $values['user_id'] = $this->user->getId();
            
            $runTable->insert($values);           
        }
        
        //Get the recent N runs for the current user.
        $this->view->usrRecentRuns = $runTable->getRecentRunsByUser($this->user->getId());
        
        //TODO: Get friends list (id, name and picture).
        
        $this->view->form = $form;
    }
    

    /**
     *  Display a welcome screen and a login button.
     */
    public function welcomeAction()
    {
        
    }
    
    /**
     *  Get the cached user object or create a new one if none is cached.
     *  @return Application_Model_User
     */
    private function getUser()
    {
        //Get the cache resource.
        $cache = $this->getCache('fbCache');
        
        //If the user object is not in the cache, reload it else read it from the cache.
        if(!$cache->test('user')) {
            $user = new Application_Model_User($this->facebook);
            $user->refreshData();
            $cache->save($user, 'user');
        }
        else {
            $user = $cache->load('user');
        }
        
        return $user;
    }
    
    /**
     *  Get the named cache object.
     *  @param string cache name
     *  @throws Zend_Controller_Action_Exception
     *  @return Zend_Cache
     */
    private function getCache($cache_name)
    {
        //Get the cache and see if we have the user object there.
        $cache = $this->bootstrap->getResource('cachemanager')                      
                                 ->getCache($cache_name);          
                                 
        //If there is no cache, exit the app.
        if(!$cache) {
            throw new Zend_Controller_Action_Exception('An error occured. Please try again later', 404); 
        }
        
        return $cache;
    }
}
