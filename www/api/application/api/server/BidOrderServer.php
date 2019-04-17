<?php
namespace app\api\server;

use app\dataapi\model\BidOrder;
use app\dataapi\model\Goods;
use think\Db;
use think\Request;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class BidOrderServer
{
    public function getBidOrderRow(int $userid): int
    {
        $time = time() - 15 * 86400;
        $bidorder = new BidOrder();
        $count = $bidorder->where('user_id', $userid)->where('add_time', '>=', $time)->count();
        return intval($count);
    }

    public function add(int $userid, int $goods_id, Request $request): int
    {
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->field('start_price,every_add_price,upload_time')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }
        $bid_price = 0.00;

        $bidorder = new BidOrder();

        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');

        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }

        if ($bid_price != $request->param('bid_price')) {
            return -1;
        }

        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => time(),
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);

        $bidorder->save();
        return $bidorder->id??0;

    }


    public function lists(int $goods_id, int $limit, $flag = false, array $config = ['page' => 1])
    {
        $lists = Db::name('bid_order')->alias('a')->join('tp_users b', 'a.user_id=b.user_id')->where('a.goods_id', $goods_id)->whereNull('a.delete_time')->field('a.id,a.bid_price,a.goods_id,a.upload_time,a.add_time,b.head_pic,b.nickname')->order('a.bid_price desc')->paginate($limit, $flag, $config);
        if (!$lists->isEmpty()) {
            $lists = $lists->toArray();
            return isset($lists['data']) ? $lists['data'] : '';
        }
        return '';
        $bidorder = new BidOrder();
        $lists = $bidorder->where('goods_id', $goods_id)->paginate($limit);

        return $lists;

    }

    public function listsarray(int $goods_id, int $limit, $flag = false, array $config = ['page' => 1])
    {
        $lists = Db::name('bid_order')->alias('a')->where('a.goods_id', $goods_id)->whereNull('a.delete_time')->field('a.id,a.user_id,a.bid_price,a.goods_id,a.upload_time,a.add_time')->order('a.bid_price desc')->paginate($limit, $flag, $config);
        if (!$lists->isEmpty()) {
            $lists = $lists->toArray();
            if (isset($lists['data'])) {
                $user_id_ids = array_column($lists['data'],'user_id');
                $user_data =[];
                $users =  Db::name('third_users')->whereIn('user_id',$user_id_ids)->field(['user_id','nickname','id','head_pic'])->select();
                foreach ($users as $user) {
                    $user_data[$user['user_id']] =$user;
                }
                foreach ($lists['data'] as &$v) {
                    $v['add_time'] = date('y-m-d H:i', $v['add_time']);
                    $v['bid_price'] = number_format($v['bid_price'], 2);
                    $v['nickname']=$user_data[$v['user_id']]['nickname']??'';
                    if (!empty($v['nickname'])) {
                        $v['nickname'] =  preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                    } else {
                        $v['nickname'] = '';
                    }
                    $v['head_pic']=$user_data[$v['user_id']]['head_pic']??'';
                }
                return $lists['data'];
            } else {
                return [];
            }
        }
        return [];
    }


    public function listsarrays(int $goods_id, int $limit, $flag = false, array $config = ['page' => 1])
    {
//        $bidorder =new BidOrder();
//        $lists = $bidorder->where('goods_id',$goods_id)->paginate($limit,true);
        $lists = Db::name('bid_order')->alias('a')->join('tp_users b', 'a.user_id=b.user_id')->where('a.goods_id', $goods_id)->whereNull('a.delete_time')->field('a.id,a.bid_price,a.goods_id,a.upload_time,a.add_time,b.head_pic,b.nickname')->order('a.bid_price desc')->paginate($limit, $flag, $config);

        if (!$lists->isEmpty()) {
            $lists = $lists->toArray();
            if (isset($lists['data'])) {
                foreach ($lists['data'] as &$v) {
                    $v['add_time'] = date('y-m-d H:i', $v['add_time']);
                    $v['bid_price'] = number_format($v['bid_price'], 2);
                    if (!empty($v['nickname'])) {
                        $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                    } else {
                        $v['nickname'] = '';
                    }
                }
                return $lists;
            } else {
                return [];
            }
        }
        return [];
    }

    public function addTime(int $bidorderdata_time)
    {
        $bidorderdata_time_t = $bidorderdata_time + mt_rand(30, 300);
        if ($bidorderdata_time_t > time()) {
            $bidorderdata_time_t = time();
            return $bidorderdata_time_t;
        }
        return $bidorderdata_time_t;
    }


    //自动出价
    public function addTimeByEndtime(int $bidorderdata_time, int $endtime)
    {
        $bidorderdata_time_t = $bidorderdata_time + mt_rand(30, 300);
        if ($bidorderdata_time_t > $endtime) {
            $bidorderdata_time_t = $this->addTimeByEndtime($bidorderdata_time);
            return $bidorderdata_time_t;
        }
        return $bidorderdata_time_t;
    }


    public function vhostAddIsToplatform(int $userid, int $goods_id, Request $request): int
    {
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->where(['goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,reserveprice')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }
        if ($datagood['endTime'] < $_SERVER['REQUEST_TIME']) {
            return -1;
        }
        $bid_price = 0.00;
        $bidorder = new BidOrder();
        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
        $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');
        $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();
        $add_time = (int)$request->param('add_time');//指定出价时间
        if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {
            //重复出价
            return -2;
        }
        $time = time();
        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                return 0;
            }
            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }
        $bidorder = new BidOrder();
        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => $time,
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);
        $bidorder->save();
        return $bidorder->id??0;
    }


    public function vhostAdd(int $userid, int $goods_id, Request $request): int
    {
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->where(['is_toplatform' => 1, 'goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,reserveprice')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }

        if ($datagood['endTime'] < $_SERVER['REQUEST_TIME']) {
            return -1;
        }

        $bid_price = 0.00;

        $bidorder = new BidOrder();

        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
        $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');


        $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();

        $add_time = (int)$request->param('add_time');//指定出价时间

        if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {

            //重复出价
            return -2;
        }


//        if($add_time>0){
//            $time =$add_time;
//        }

//
//        if($bidorderdata_time>0 && $bidorderdata_time<time()){
//            $time =$this->addTime($bidorderdata_time);
//        }else{
//            $time =time();
//        }
        $time = time();
        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                return 0;
            }


            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }


        $bidorder = new BidOrder();
        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => $time,
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);

        $bidorder->save();
        return $bidorder->id??0;

    }


    public function vhostChunkAdd(int $userid, int $goods_id, Request $request, int $everytime): int
    {
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->where(['is_toplatform' => 1, 'goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,reserveprice')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }
//
//        if($datagood['endTime'] <$_SERVER['REQUEST_TIME']){
//            return -1;
//        }

        $bid_price = 0.00;

        $bidorder = new BidOrder();

        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
        $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');


        $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();

        $add_time = (int)$request->param('add_time');//指定出价时间

        if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {

            //重复出价
            return -2;
        }

        if ($bidorderdata_time > 0 && $bidorderdata_time < $datagood['endTime']) {
            $time = $this->addTimeByEndtimeSpecTime($bidorderdata_time, $datagood['endTime'], mt_rand(21600, 29600));
            if ($time == false) {
                return 0;
            }


        } elseif ($bidorderdata_time == 0) {
            $time = $this->addTimeByEndtimeSpecTime($datagood['upload_time'], $datagood['endTime'], mt_rand(14400, 21600));
            if ($time == false) {
                return 0;
            }
        } elseif ($bidorderdata_time > 0 && $bidorderdata_time > $datagood['endTime']) {
            return 0;
        }


        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                return 0;
            }

            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }


        $bidorder = new BidOrder();
        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => $time,
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);

        $bidorder->save();
        return $bidorder->id??0;

    }


    public function vhostAddOrderOver(int $userid, int $goods_id, Request $request): int
    {
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->where(['is_toplatform' => 1, 'goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,reserveprice')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }

        if ($datagood['endTime'] < $_SERVER['REQUEST_TIME']) {
            return -1;
        }

        $bid_price = 0.00;

        $bidorder = new BidOrder();

        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
        $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');
        $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();
        $add_time = (int)$request->param('add_time');//指定出价时间
        if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {
            //重复出价
            return -2;
        }


//        if($add_time>0){
//            $time =$add_time;
//        }

//
        if ($bidorderdata_time > 0 && $bidorderdata_time < $datagood['endTime']) {
            $time = $this->addTimeByEndtime($bidorderdata_time, $datagood['endTime']);
        } elseif ($bidorderdata_time == 0) {
            $time = $this->addTimeByEndtime($datagood['upload_time'], $datagood['endTime']);
        } elseif ($bidorderdata_time > 0 && $bidorderdata_time > $datagood['endTime']) {
            return 0;
        }

        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                return 0;
            }


            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }


        $bidorder = new BidOrder();
        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => $time,
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);

        $bidorder->save();
        return $bidorder->id??0;

    }


    //自动出价
    public function addTimeByEndtimeSpecTime(int $bidorderdata_time, int $endtime, int $spectime)
    {
        $bidorderdata_time_t = $bidorderdata_time + $spectime;
        if ($bidorderdata_time_t > $endtime) {
            return false;
            //  $bidorderdata_time_t =$this->addTimeByEndtimeSpecTime($bidorderdata_time,$endtime,$spectime);
            //   return $bidorderdata_time_t;
        }
        return $bidorderdata_time_t;
    }


    /*
     *
     * 通过结束时间控制
     */
    public function vhostAddByEndTime(int $userid, int $goods_id, Request $request, array $datagood, int $distancehours, int $everytime): int
    {
        $bid_price = 0.00;
        $bidorder = new BidOrder();
        $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
        $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');
        $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();
        $add_time = (int)$request->param('add_time');//指定出价时间


        if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {
            //重复出价
            return -2;
        }
        // 50 8-10  60  8-15
        if ($distancehours > 50) {
            $rand = mt_rand(18000, 29600);
        } elseif ($distancehours > 40) {
            $rand = mt_rand(14400, 29600);
        } elseif ($distancehours > 30) {
            $rand = mt_rand(14400, 29600);
        } elseif ($distancehours > 10) {
            $rand = mt_rand(3600, 10800);
        }

        if ($bidorderdata_time > 0 && $bidorderdata_time < $datagood['endTime']) {
            $time = $this->addTimeByEndtimeSpecTime($bidorderdata_time, $datagood['endTime'], $rand);
            if ($time == false) {
                return 0;
            }
        } elseif ($bidorderdata_time == 0) {
            $time = $this->addTimeByEndtimeSpecTime($datagood['upload_time'], $datagood['endTime'], $rand);
            if ($time == false) {
                return 0;
            }
        } elseif ($bidorderdata_time > 0 && $bidorderdata_time > $datagood['endTime']) {
            return 0;
        }


        if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
            $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
        } else {
            if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                return 0;
            }

            $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
        }


        $bidorder = new BidOrder();
        $bidorder->data([
            'user_id' => $userid,
            'goods_id' => $goods_id,
            'add_time' => $time,
            'upload_time' => $datagood['upload_time'],
            'bid_price' => $bid_price,
        ]);

        $bidorder->save();
        return $bidorder->id??0;

    }


    public function vhostAddByCli(int $userid, int $goods_id): int
    {
// 启动事务
        Db::startTrans();
        try{
            $goods = new   Goods();
            $datagood = $goods->where('goods_id', $goods_id)->where('is_toplatform in (1,3)')->where(['goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,reserveprice')->lock(true)->find();
            if (!empty($datagood)) {
                $datagood = $datagood->toArray();
            } else {
                Db::rollback();
                return 0;
            }
            $time = time();
            if ($datagood['endTime'] < $time) {
                Db::rollback();
                return -1;
            }
            $bid_price = 0.00;

            $bidorder = new BidOrder();
            $bidordercount =  $bidorder->where(['user_id' => $userid])->count();
            if($bidordercount>1){
                return false;
            }
                $bidorderdata = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('bid_price');
            $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');
            $checkuser_id = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->order('add_time desc')->field('user_id')->find();
            if (isset($checkuser_id['user_id']) && $checkuser_id['user_id'] == $userid) {
                //重复出价
                Db::rollback();
                return -2;
            }

            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $datagood['start_price']) {
                $bid_price = bcadd($datagood['start_price'], $datagood['every_add_price'], 2);
            } else {
                if ($datagood['reserveprice'] > $datagood['start_price'] && $bidorderdata > $datagood['reserveprice']) {
                    Db::rollback();
                    return 0;
                }
                $bid_price = bcadd($bidorderdata, $datagood['every_add_price'], 2);
            }
            $bidorder = new BidOrder();
            $bidorder->data([
                'user_id' => $userid,
                'goods_id' => $goods_id,
                'add_time' => $time,
                'upload_time' => $datagood['upload_time'],
                'bid_price' => $bid_price,
            ]);
            if($bidorder->save() ==false){
                Db::rollback();
                return 0;
            }
            // 提交事务
            Db::commit();
            return $bidorder->id??0;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return 0;
    }

    public function bidOrderlists(array $where, &$count = 0, $isbuy = false): array
    {
        $bidorder = new BidOrder();
        $field = $where['field'];
        $userid = $where['user_id'];
        $page = $where['page'];
        $limit= $where['limit']??10;
        $endtime = $where['endtime'];
        if ($isbuy) {
            //买家
            $tempdata = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('a.user_id', $userid)->order('a.goods_id desc')->group('a.goods_id')->limit($page, 1)->field('max(a.bid_price) as bid_price')->column('a.goods_id');

            if (!empty($tempdata)) {
                $id = $tempdata[0];
                $count = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('a.user_id', $userid)->where('g.endTime', '<',time())->where('a.goods_id', '<=', $id)->group('a.goods_id')->count('g.goods_id');
                $lists = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('a.user_id', $userid)->where('g.endTime', '<',time())->where('a.goods_id', '<=', $id)->group('a.goods_id')->order('a.goods_id desc')->limit($limit)->select();
                return $lists;
            }
        } else {
            $tempdata = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('g.user_id', $userid)->order('a.goods_id desc')->limit($page, 1)->column('a.goods_id');
            if (!empty($tempdata)) {
                $id = $tempdata[0];
                $count = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('g.user_id', $userid)->where('g.endTime', '<',time())->where('a.goods_id', '<=', $id)->where('g.endTime', '>', $endtime)->group('a.goods_id')->field('max(a.bid_price) as bid_price,a.goods_id')->count('g.goods_id');
                $lists = Db::name('bid_order')->alias('a')->join('goods g', 'g.goods_id=a.goods_id')->where('g.user_id', $userid)->where('g.endTime', '<',time())->where('a.goods_id', '<=', $id)->where('g.endTime', '>', $endtime)->group('a.goods_id')->order('a.goods_id desc')->limit($limit)->field('max(a.bid_price) as bid_price,a.*,g.*')->select();
                return $lists;
            }
        }
        return [];
    }



    public function Orderlists(array $where, &$count = 0, $isbuy = false): array
    {
        $bidorder = new BidOrder();
        $field = $where['field'];
        $page = $where['page'];
        $limit= $where['limit']??10;
        $endtime = $where['endtime'];
        $map = $where['map']??['1'=>['=',1]];
        if ($isbuy) {
            $a =microtime(true);
            //买家
            $tempdata = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where($map)->order('og.goods_id desc')->limit($page, 1)->field('og.goods_price')->column('og.order_id');
            if (!empty($tempdata)) {
                $id = $tempdata[0];
                $count = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where('a.order_id', '<=', $id)->where($map)->count('a.order_id');
                $lists = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where('a.order_id', '<=', $id)->where($map)->order('a.order_id desc')->limit($limit)->select();
                return $lists;
            }
        } else {
            $tempdata = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where($map)->order('og.goods_id desc')->limit($page, 1)->field('og.goods_price')->column('og.order_id');
            if (!empty($tempdata)) {
                $id = $tempdata[0];
                $count = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where($map)->where('a.order_id', '<=', $id)->count('a.order_id');
                $lists = Db::name('order')->alias('a')->join('order_goods og', 'a.order_id=og.order_id')->where($map)->where('a.order_id', '<=', $id)->order('a.order_id desc')->limit($limit)->select();
                return $lists;
            }
        }
        return [];
    }





    public  function build_order_no() {
        return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(true), 7, 14), 1))), 0, 9).mt_rand(10000,99999);
    }


    public function getorderstatusdescforbuyer(int $status,array $goods)
    {
        //对于买家
        // status  '1 未付款 (没有过期情况下)  2已付款  3已经发货 4已结收货成功 5已经评价 6订单自动取消 7 协议退款 8 协议退货 9 退货加退款'
        $desc = '';
        if(time() >($goods['endTime']+172800) && $status==1){
            //已经超过48小时了
            $desc = '订单自动取消,没有及时付款';
            return  $desc;
        }



        switch ($status) {
            case 0:
                $desc = '待付款';
                break;
            case 1:
                $desc = '待付款';
                break;
            case 2:
                $desc = '已付款';
                break;
            case 3:
                $desc = '已经发货';
                break;
            case 4:
                $desc = '已结收货成';
                break;
            case 5:
                $desc = '已经评价';
                break;
            case 6:
                $desc = '订单自动取消';
                break;
            case 7:
                $desc = '协议退款';
                break;
            case 8:
                $desc = '协议退货';

                break;
            case 9:
                $desc = '退货加退款';
                break;
                case 10:
                $desc = '出局了';
                break;
            default:
                break;
        }
        return $desc;
    }
    public function getorderstatusdesc(int $status,array $goods)
    {
        //对于买家
        // status  '1 未付款 (没有过期情况下)  2已付款  3已经发货 4已结收货成功 5已经评价 6订单自动取消 7 协议退款 8 协议退货 9 退货加退款'
        $desc = '';
        if(time() >($goods['endTime']+172800) && $status==1){
        //已经超过48小时了
            $desc = '订单自动取消,没有及时付款';
            return  $desc;
        }



        switch ($status) {
            case 1:
                $desc = '未付款';
                break;
            case 2:
                $desc = '已付款';
                break;
            case 3:
                $desc = '已经发货';
                break;
            case 4:
                $desc = '已结收货成';
                break;
            case 5:
                $desc = '已经评价';
                break;
            case 6:
                $desc = '订单自动取消';
                break;
            case 7:
                $desc = '协议退款';
                break;
            case 8:
                $desc = '协议退货';

                break;
            case 9:
                $desc = '退货加退款';
                break;
            default:
                break;
        }
        return $desc;
    }
}