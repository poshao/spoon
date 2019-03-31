<?php
namespace App\Vba\View;

class Addins extends \Spoon\View
{
    public function __construct()
    {
        parent::__construct();
        $this->_rules=array(
            'name'=>array(
                'type'=>'text'
            ),
            'version'=>array(
                'type'=>'text'
            ),
            'description'=>array(
                'type'=>'text'
            ),
            'file'=>array(
                'type'=>'file'
            ),
            'addinid'=>array(
                'type'=>'text'
            ),
            'loginname'=>array(
                'type'=>'text'
            )
        );
    }
}
