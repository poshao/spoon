<?php
namespace App\Auth\View;

class Permissions extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{7}$/'
            ),
            'permissionid'=>array(
                'type'=>'number',
                'min'=>1
            ),
            'permission'=>array(
              'type'=>'text',
              'max-length'=>50
            ),
            'info'=>array(
                'type'=>'array',
                'require'=>array(),
                'optional'=>array('permissionname','description')
            )
        );
    }
}
