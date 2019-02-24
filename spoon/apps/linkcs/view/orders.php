<?php
namespace App\Linkcs\View;

class Orders extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'detail'=>array(
                'type'=>'text'
            ),
            'orderid'=>array(
                'type'=>'number'
            ),
            'status'=>array(
                'type'=>'list',
                'list'=>array('accept','reject')
            ),
            'reason'=>array(
                'type'=>'text'
            )
        );
    }
}
