<?php
namespace App\Auth\View;
class Verify extends \Spoon\View{
    public function __construct(){
        parent::__construct();
        $this->_rules=array();
    }
}
?>