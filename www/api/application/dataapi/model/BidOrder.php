<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class BidOrder extends Model
{
    protected $pk = 'id';



    use SoftDelete;


    protected $deleteTime = 'delete_time';


}
