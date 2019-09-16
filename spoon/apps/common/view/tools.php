<?php
namespace App\Common\View;

class Tools extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            //'loginname'=>array(
            //  'type'=>'text'
            //)
            'sql'=>array(
                'type'=>'text'
            )
        );
    }
}
