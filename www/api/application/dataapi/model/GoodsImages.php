<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class GoodsImages extends Model
{
    protected $pk = 'img_id';
    use SoftDelete;


    protected $deleteTime = 'delete_time';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

    }


}
