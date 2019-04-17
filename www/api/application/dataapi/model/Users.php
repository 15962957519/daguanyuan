<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class Users extends Model
{
    protected $pk = 'user_id';

    protected $readonly = ['user_id'];

    use SoftDelete;


    protected $deleteTime = 'delete_time';

    //    //// 软删除
    //User::destroy(1);
    //// 真实删除


    protected $rule = [
        'user_name'  =>  'require|max:40',
        'email' =>  'email|max:60',
    ];

    protected $message = [
        'name.require'  =>  '用户名必须',
        'email' =>  '邮箱格式错误',
    ];

    protected $scene = [
        'add'   =>  ['user_name','email'],
        'edit'  =>  ['email'],
    ];



}
