<?php
namespace App\Auth\View;

class Groups extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'workid'=>array(
                'type'=>'regex',
                'pattern'=>'/^\d{7}$/'
            ),
            'groupid'=>array(
                'type'=>'number',
                'min'=>1
            ),
            'group'=>array(
              'type'=>'text',
              'max-length'=>50
            ),
            'info'=>array(
                'type'=>'array',
                'require'=>array(),
                'optional'=>array('groupname','description','roleid')
            )
        );
    }
}
