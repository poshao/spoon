<?php
namespace App\Auth\View;

class Tokens extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{5,7}$/'
            ),
            'password'=>array(
                'type'=>'text',
                'length-max'=>16,
                'length-min'=>6
            ),
            'token'=>array(
                'type'=>'text',
                'length-max'=>50,
                'length-min'=>40
            ),
            'socketid'=>array(
                'type'=>'text',
                'length'=>32
            ),
        );
    }
}
