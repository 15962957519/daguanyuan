<?php
namespace app\api\controller;
use think\Controller;


class BaseApi  extends Controller
{

    protected $header;

    public function _initialize()
    {
    	$this->header = [
    	    'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'
        ];

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS');
        return true;
    }

}
