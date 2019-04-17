<?php
/**
 * 类的简述
 *
 * @category 技术组
 * @package  controller
 * @author   ericssonon@163.com
 * @license  www.haiousystem.com
 * @link     ericssonon@163.com
 */
namespace app\dataapi\controller;

use think\Controller;
use think\Db;
use think\Config;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\model\Goods;
use app\dataapi\server\HttpConsumer;
use  app\dataapi\server\Weixin;
use app\dataapi\server\BidOrderServer;
//生成订单 根据倒计时生成订单


class FictitiousGeneration extends BaseApi
{
    /**
     * 更新虚拟会员到指定表
     * @param OBJ $request 参数解释
     * @return  json
     */
    public function updateFictitiousGenerationserver(Request $request)
    {

        $this->copytpuserstotpUserstemp();


        Response::create(['status' => '200', 'code' => '1', 'error' => '', 'message' => 'to user is sucess'], 'json')->header($this->header)->send();
    }




    //复制表信息到临时表

    private function copytpuserstotpUserstemp()
    {
        $imageobj = new Image();
        $userpic = [];
        $hot_users = Db::name('users')
            ->where('fictitious', 1)
            ->whereNotNull('head_pic')
            ->whereNull('delete_time')
            ->field('user_id,head_pic')
            ->chunk(30,
                function ($vulue) use ($imageobj) {
                    $data = [];
                    foreach ($vulue as $v) {
                        if (!$v['user_id'] || empty($v['head_pic'])) {
                            continue;
                        }
                        $flag = $imageobj->imagetest($v['head_pic']);
                        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
                            continue;
                        }
                        //判断是否存在在表里
                        $data[] = ['user_id' => $v['user_id'], 'head_pic' => $v['head_pic']];
                        echo "{$v['user_id']}正在执行\r\n";
                    }
                    if (isset($data) && !empty($data)) {
                        $table_name =   Config::get('updateFictitiousGenerationserver');
                        Db::name($table_name)->insertAll($data);
                    }
                }, 'user_id');
    }

}
