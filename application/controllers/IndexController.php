<?php

class IndexController extends Zend_Controller_Action
{

    private $facebook = null;    
    
    public function preDispatch()
    {        
        //Init the facebook object.
        $facebookSettings = $this->getInvokeArg('bootstrap')->getOption('facebook');        
        $this->facebook = new FB_Facebook($facebookSettings);
        
        //Set some common layout vars.      
        $layout = Zend_Layout::getMvcInstance();
        unset($facebookSettings['secret']); //DON'T SEND the SECRET TO THE PUBLIC.      
        $layout->assign('facebook', $facebookSettings);
        
        //If there is no valid session, take the user to the welcome action.
        $session = $this->facebook->getSession();  
        if($this->_request->getActionName() != 'welcome' && !$session) {
            $this->_request->setActionName('welcome');
        }
        else {            
            $layout->assign('session', $session);
        }
    }     

    //Display the user recent runs.
    public function indexAction()
    {
        
    }

    //Display a welcome screen and a login button.
    public function welcomeAction()
    {
        
    }
}
