<?php

namespace App\Vba\View;

class Packages extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            //'loginname'=>array(
            //  'type'=>'text'
            //)
            'name'=>array(
              'type'=>'text'
            ),
            'version'=>array(
              'type'=>'text'
            ),
            'description'=>array(
              'type'=>'text'
            ),
            'search'=>array(
              'type'=>'text'
            )
        );
    }
}
