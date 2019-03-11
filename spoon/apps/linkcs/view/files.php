<?php
namespace App\Linkcs\View;

class Files extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'filename'=>array(
                'type'=>'text'
            ),
            'hashname'=>array(
                'type'=>'text'
            )
        );
    }
}
