<?php
namespace App\Auth\View;

class Resources extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{7}$/'
            )
            );
    }
}
