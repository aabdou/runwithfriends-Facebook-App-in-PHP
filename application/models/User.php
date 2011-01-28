<?php

class Application_Model_User
{
  private $user_id;
  private $access_token;
  private $name;
  private $picture;
  private $email;
  private $friends;
  private $dirty;
  private $facebook;  

  public function __construct($facebook)
  {
    $this->facebook = $facebook;
    $session = $facebook->getSession();
    $this->access_token = $session['access_token'];
    $this->user_id = $session['uid'];
  }
  
  /*
   * Reload data from the Facebook API.
   */ 
  public function refreshData()
  {
    $result = $this->facebook->api('/me', 
        array('fields' => 'picture,friends', 'access_token' => $this->access_token));
        
    $this->name = $result['name'];   
    $this->email = $result['email'];
    $this->picture = $result['picture']; 
    $this->dirty = false;
  }
  

  public function getId()
  {
    return $this->user_id;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getPicture()
  {
    return $this->picture;
  }
  
  public function getLogoutUrl()
  {
    return $this->facebook->getLogoutUrl(); //array('next' => $next)
  }
}

