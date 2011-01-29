<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  /**
   * Initialize the view resource.
   */
  protected function _initPlaceholders()
  {  
    $this->bootstrap('View');
    $view = $this->getResource('View');
    $view->doctype('XHTML1_STRICT');
    $view->headTitle('Run with friends')
         ->setSeparator(' - ');   
  }
  
  /**
   * Register custom and 3rd party namespaces.
   */ 
  protected function _initAutoLoad()
  {
    $autoloader = Zend_Loader_Autoloader::getInstance();
    $autoloader->registerNamespace('FB_');
    $autoloader->registerNamespace('AA_');

    return $autoloader;
  }
  
  /**
   *  Register custom helpers with the broker.
   */
  protected function _initHelpersBroker()
  {
    Zend_Controller_Action_HelperBroker::addPrefix('AA_Controller_Action_Helper_');
  }
}
