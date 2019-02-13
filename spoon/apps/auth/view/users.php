<?php
namespace App\Auth\View;

class Users extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{7}$/'
            ),
            'password'=>array(
                'type'=>'text',
                'length-max'=>16,
                'length-min'=>6
            ),
            'username'=>array(
                'type'=>'text',
                'length-max'=>20
            ),
            'depart'=>array(
                'type'=>'list',
                'list'=>array('Logistics','CS')
            ),
            'infofields'=>array(
                'type'=>'list',
                'list'=>array('username','depart')
            ),
            'info'=>array(
                'type'=>'array',
                'require'=>array(),
                'optional'=>array('username','depart')
            )
        );
    }
}
