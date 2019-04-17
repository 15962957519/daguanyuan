<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class FansCollect extends Model
{
    protected $table = 'tp_fans_collect';
    protected $pk = 'collect_id';



    use SoftDelete;


    protected $deleteTime = 'delete_time';


}
