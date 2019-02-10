<?php
namespace App\Linkcs\View;

class Detail extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'detail'=>array(
                'type'=>'text'
            ),
        );
    }
}
