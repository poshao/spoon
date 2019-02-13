<?php
namespace App\Auth\View;

class Roles extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{7}$/'
            ),
            'roleid'=>array(
                'type'=>'number',
                'min'=>1
            ),
            'info'=>array(
                'type'=>'array',
                'require'=>array(),
                'optional'=>array('rolename','description')
            )
        );
    }
}
