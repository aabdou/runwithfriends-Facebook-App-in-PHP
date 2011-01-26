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
  }
  
  public function refreshData()
  {
    $this->facebook->api();
  }
}

