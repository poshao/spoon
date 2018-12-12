<?php
namespace App\Auth\Controller;
use \App\Auth\Model\User as mUser;
use \App\Auth\View\User as vUser;

class User extends \Spoon\Controller{

    public function __construct(){
        $this->_view=new vUser();
        $this->_model=new mUser();
    }

    public function process(){
        $this->view()->checkParam();
        
    }
}

?>