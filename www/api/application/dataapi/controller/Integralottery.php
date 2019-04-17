<?php

namespace app\dataapi\controller;


use app\dataapi\model\Goods;
use app\dataapi\model\GoodsImages;
use app\dataapi\model\UserVerifty;
use app\dataapi\server\AliyunOss;
use app\dataapi\server\Weixin;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Db;
use think\Cache;
use think\Hook;
use think\Request;
use think\Response;
use app\Home\Logic\UsersLogic;
use app\dataapi\server\UserServer;
use app\dataapi\controller\JobQeue;
use think\Log;
use  app\dataapi\model\GoodsShouc;
use think\exception\ErrorException;


//积分抽奖
class Integralottery extends BaseApi
{

    private $user_data;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appEnd', $this->user_data);
        parent::_initialize();
    }


    public function index()
    {
        $userid = $this->user_data['user_id'];
       $datalists =  Db::name('lottery_gifts')->order('lottery_pr')->cache(true)->select();
        $datalistsdata =[];
        $userLogic = new UsersLogic();
        $abcdata = $userLogic->get_info($userid);
//        $prize_arr = array(
//            '0' => array('id' => 1, 'min' => 1, 'max' => 29, 'prize' => '一等奖', 'v' => 1),
//            '1' => array('id' => 2, 'min' => 302, 'max' => 328, 'prize' => '二等奖', 'v' => 2),
//            '2' => array('id' => 3, 'min' => 242, 'max' => 268, 'prize' => '三等奖', 'v' => 5),
//            '3' => array('id' => 4, 'min' => 182, 'max' => 208, 'prize' => '四等奖', 'v' => 7),
//            '4' => array('id' => 5, 'min' => 122, 'max' => 148, 'prize' => '五等奖', 'v' => 10),
//            '5' => array('id' => 6, 'min' => 62, 'max' => 88, 'prize' => '六等奖', 'v' => 25),
//            '6' => array('id' => 7, 'min' => 332, 'max' => 358, 'prize' => '七等奖', 'v' => 15),
//            '7' => array('id' => 8, 'min' =>  272, 'max' =>  298, 'prize' => '8等奖', 'v' => 15),
//            '8' => array('id' => 9, 'min' => 212, 'max' =>  238, 'prize' => '9等奖', 'v' => 20),
//            //min数组表示每个个奖项对应的最小角度 max表示最大角度
//            //prize表示奖项内容，v表示中奖几率(若数组中七个奖项的v的总和为100，如果v的值为1，则代表中奖几率为1%，依此类推)
//        );
        //函数getRand()会根据数组中设置的几率计算出符合条件的id，我们可以接着调用getRand()。
        foreach ($datalists as $v) {
            $arr[$v['lottery_id']] = $v['lottery_pr'];
        }
        $prize_id = $this->getRand($arr); //根据概率获取奖项id
        $res = $datalists[$prize_id - 1]; //中奖项
//        $min = $res['min'];
//        $max = $res['max'];
//        if ($res['id'] == 10) { //七等奖
//            $i = mt_rand(0, 5);
//            $data['angle'] = mt_rand($min[$i], $max[$i]);
//        } else {
//            $data['angle'] = mt_rand($min, $max); //随机生成一个角度
//        }
        $data['prize'] = $res['lottery_name'];
        $data['id'] = $res['lottery_id'];

        $lotterydata['user_id']=$userid;
        $lotterydata['lottery_id']=$res['lottery_id'];
        $lotterydata['addtime']=$_SERVER['REQUEST_TIME'];
        $lotterydata['day']=$_SERVER['REQUEST_TIME'];
        $lotterydata['mobile']=$abcdata['result']['mobile'];
        Db::name('lottery_users')->insert($lotterydata);

        $lotterydatalogs['lottery_id']=$res['lottery_id'];
        $lotterydatalogs['user_id']=$userid;
        $lotterydatalogs['addtime']=$_SERVER['REQUEST_TIME'];
        $lotterydatalogs['day']=$_SERVER['REQUEST_TIME'];

        Db::name('lottery_logs')->insert($lotterydatalogs);

        Response::create(['data' => $data, 'code' => '2000', 'message' => '获取作品上传信息分类等' . '成功'], 'json')->header($this->header)->send();
    }


    //   中奖概率方法我们之前在jQuery砸金蛋_PHP砸金蛋讲过，代码如下
    public function getRand($proArr)
    {
        $data = '';
        $proSum = array_sum($proArr); //概率数组的总概率精度

        foreach ($proArr as $k => $v) { //概率数组循环
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {
                $data = $k;
                break;
            } else {
                $proSum -= $v;
            }
        }
        unset($proArr);

        return $data;
    }


    public function lottery(Request $request = null)
    {
        $goods_category = Db::name('lottery_users')->alias('lu')->join('__LOTTERY_GIFTS__ g ','lu.lottery_id=g.lottery_id')->where('g.status',0)->limit(12)->order('lu.id desc')->field('lu.mobile,g.lottery_name')->select();
        Response::create(['data' => $goods_category, 'code' => '2000', 'message' => '' . '成功'], 'json')->header($this->header)->send();
    }

    public function lotterymy(Request $request = null)
    {
        $userid = $this->user_data['user_id'];
        $goods_category = Db::name('lottery_users')->alias('lu')->join('__LOTTERY_GIFTS__ g ','lu.lottery_id=g.lottery_id')->where('lu.user_id',$userid)->order('lu.id desc')->field('lu.mobile,g.lottery_name,lu.day')->paginate(10);
        Response::create(['data' => $goods_category, 'code' => '2000', 'message' => '' . '成功'], 'json')->header($this->header)->send();
    }

}
