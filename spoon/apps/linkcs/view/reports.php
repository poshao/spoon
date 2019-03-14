<?php
namespace App\Linkcs\View;

class Reports extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            //'detail'=>array(
            //    'type'=>'text'
            //),
            'reportname'=>array(
                'type'=>'text'
            ),
            'struct'=>array(
                'type'=>'text'
            )
        );
    }
}
