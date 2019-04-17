<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class SendMessagelists extends Model
{
    protected $pk = 'id';

    protected $readonly = ['goods_id'];

    use SoftDelete;


    protected $deleteTime = 'delete_time';

}
