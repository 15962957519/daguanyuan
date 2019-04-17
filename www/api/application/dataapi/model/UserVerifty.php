<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class UserVerifty extends Model
{
    protected $pk = 'id';


    use SoftDelete;


    protected $deleteTime = 'delete_time';

    //    //// 软删除
    //User::destroy(1);
    //// 真实删除




}
