<?php
namespace App\Vba\View;

class Funs extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'addinname'=>array(
              'type'=>'text'
            ),
            'loginname'=>array(
              'type'=>'text'
            ),
            'username'=>array(
              'type'=>'text'
            )
            //'detail'=>array(
            //    'type'=>'text'
            //),
        );
    }
}
