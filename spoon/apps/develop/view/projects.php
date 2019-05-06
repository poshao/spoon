<?php
namespace App\Develop\View;

class Projects extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            //'loginname'=>array(
            //  'type'=>'text'
            //)
            'subject'=>array(
                'type'=>"text"
            ),
            'description'=>array(
                'type'=>'text'
            ),
            'status'=>array(
                'type'=>'list',
                'list'=>array('request','pass','check','process','finish','pending','cancel')
            ),
            'projectid'=>array(
                'type'=>'text'
            )
        );
    }
}
