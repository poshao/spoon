<?php
namespace App\vba\View;

class Users extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'loginname'=>array(
              'type'=>'text'
            ),
            'username'=>array(
              'type'=>'text'
            ),
            'userid'=>array(
              'type'=>'text'
            ),
            'addinid'=>array(
              'type'=>'text'
            ),
            'funid'=>array(
              'type'=>'text'
            )
        );
    }
}
