<?php
namespace app\api\server;

use app\dataapi\model\FansCollect;
use app\dataapi\model\GoodsShouc;
use app\dataapi\model\ThirdUsers;
use app\dataapi\model\UserBond;
use app\dataapi\model\Goods;
use app\dataapi\model\Users;
use app\dataapi\model\UserVerifty;
use Defuse\Crypto\KeyProtectedByPassword;
use think\Db;
use app\dataapi\server\UserServer;
use think\Request;
use think\Response;
use think\Config;
use app\dataapi\server\BidOrderServer;
use app\dataapi\model\BidOrder;
use Carbon\Carbon;
use app\dataapi\lib\Image;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class CaseServer
{

    //竞拍成交 案例

    /*
     *
     *
     *
     * return 2 作品参数为空  0 作品不存在  3拍卖结束
     */
    public function CaseUser(Request $request)
    {
        $goods_id = $request->param('goods_id');
        $num = (int)$request->param('num') > 0 ? (int)$request->param('num') : mt_rand(8, 15);
        //mt_rand(8,20);
        $page = $page??1;
        $bid_price = floatval($request->param('bid_price'));
        if ($goods_id <= 0) {
            return 1;
        }

        //判断是否已经拍卖尽速
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->field('start_price,every_add_price,upload_time,endTime,goods_status')->find();
        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }


        if ($datagood['goods_status'] == 2) {
            return 0;
        }
        $time_intervals = $this->randnum($datagood['endTime'] - $datagood['upload_time'], $num);//时间间隔
        // $dd =     Db::query("SELECT user_id,fictitious  FROM `tp_users`  where delete_time is null and fictitious=1  ORDER BY RAND() LIMIT ".$num);
        $BidOrderServer = new BidOrderServer();
        $UserServer = new UserServer();

        Db::startTrans();
        try {
            $i = 0;
            while ($i < $num) {
                $ddd = $this->flagimage();
                $flag = $UserServer->userUpdateLike($ddd['user_id'], ['goods_id' => $goods_id]);
                if ($i == $num - 1) {
                    $i++;
                    //成交
                    $data = $BidOrderServer->vhostChunkAdd($ddd['user_id'], $goods_id, $request, $time_intervals[$i]);
                    if ($data <= 0) {
                        continue;
                    }
                    break;
                } else {
                    $i++;
                    $data = $BidOrderServer->vhostChunkAdd($ddd['user_id'], $goods_id, $request, $time_intervals[$i]);
                    if ($data <= 0) {
                        continue;
                    }
                }

            }

            $nggum = mt_rand(16, 26);
            while ($nggum >= 1) {
                $ddd = $this->flagimage();
                $nggum--;
                $flag = $UserServer->userUpdateLike($ddd['user_id'], ['goods_id' => $goods_id]);
            }

            //更新作品状态


            $bidorder = new BidOrder();

            $bidorderdata_time = $bidorder->where(['goods_id' => $goods_id, 'upload_time' => $datagood['upload_time']])->max('add_time');

            $goods = new   Goods();
            $datagood = $goods->where('goods_id', $goods_id)->update(['goods_status' => 2]);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        //跳转
    }

    public function flagimage()
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        $datauser = Db::name('users')->where('fictitious', 1)->where('level', '>', 1)->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->field('user_id,head_pic')->order('rand()')->find();
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
            return $this->flagimage();
        }
        return $datauser;
    }


    public function flagimageByUserid(int $userid)
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        $datauser = Db::name('users')->where('fictitious', 1)->where('level', '>', 1)->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->where("head_pic <> ''")->field('user_id,head_pic')->order('rand()')->find();
        if (isset($datauser['user_id']) && $datauser['user_id'] == $userid) {
            return $this->flagimageByUserid($userid);
        }
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
            return $this->flagimageByUserid($userid);
        }
        return $datauser;
    }


    public function flagimageByUseridBorder(int $userid, &$BidOrderServer)
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        $datauser = Db::name('users')->alias('u')->where('u.fictitious', 1)->where('u.is_border', 0)->where('u.level', '>', 1)->where('u.reg_time', '>', $unixstamp)->whereNull('u.delete_time')->where("u.head_pic <> ''")->field('u.user_id,u.head_pic')->join('(SELECT CEIL(MAX(user_id)*RAND()) AS mid FROM tp_users) as m', 'u.user_id>=m.mid')->find();
        //$temptid 临时判断是否在最近15天内买过
//       $tempdatacount = $BidOrderServer->getBidOrderRow($datauser['user_id']);
//        if($tempdatacount>0){
//            return    $this->flagimageByUseridBorder($userid,$BidOrderServer);
//        }
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
            return $this->flagimageByUseridBorder($userid, $BidOrderServer);
        }
        Db::name('users')->where('user_id', $datauser['user_id'])->update(['is_border' => 1]);
        return $datauser;
    }


    public function imagetest($url)
    {
        if (empty($url)) {
            return false;
        }
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);
        return $imageSize;
    }

    public function randnum($total, $div)
    {
        $total = $total; //待划分的数字
        $div = $div; //分成的份数
        $area = 2500; //各份数间允许的最大差值
        $average = round($total / $div);
        $sum = 0;
        $result = array_fill(1, $div, 0);

        for ($i = 1; $i < $div; $i++) {
            //根据已产生的随机数情况，调整新随机数范围，以保证各份间差值在指定范围内
            if ($sum > 0) {
                $max = 0;
                $min = 0 - round($area / 2);
            } elseif ($sum < 0) {
                $min = 0;
                $max = round($area / 2);
            } else {
                $max = round($area / 2);
                $min = 0 - round($area / 2);
            }

            //产生各份的份额
            $random = rand($min, $max);
            $sum += $random;
            $result[$i] = $average + $random;
        }

        //最后一份的份额由前面的结果决定，以保证各份的总和为指定值
        $result[$div] = $average - $sum;
        foreach ($result as $temp) {
            $data[] = $temp;
        }
        return $data;
    }



    //竞拍成交 案例

    /*
     *从截止时间往前退
     *
     *
     * return 2 作品参数为空  0 作品不存在  3拍卖结束
     */
    public function CaseUserByEndtime(Request $request)
    {
        $goods_id = $request->param('goods_id');
        $page = $page??1;
        $bid_price = floatval($request->param('bid_price'));
        if ($goods_id <= 0) {
            return 1;
        }
        //判断是否已经拍卖尽速
        $configdata = Config::get('vhostmember');
        $cacseuserid = $configdata['membertime']['cacseuserid'];
        $goods = new   Goods();
        $datagood = $goods->where('goods_id', $goods_id)->where('user_id',$cacseuserid)->where('endTime', '<=', $_SERVER['REQUEST_TIME'])->where(['goods_status' => 1])->field('start_price,every_add_price,upload_time,endTime,goods_status,reserveprice')->lock(false)->find();


        if (!empty($datagood)) {
            $datagood = $datagood->toArray();
        } else {
            return 0;
        }
        if ($datagood['goods_status'] == 2) {
            return 0;
        }
        //生成随机值

        $num = (int)$request->param('num') > 0 ? (int)$request->param('num') : mt_rand(8, 15);
        if ($datagood['endTime'] > $_SERVER['REQUEST_TIME']) {
            return 3;
        }
        $datagood['upload_time'] = $datagood['endTime'] - mt_rand(172800, 259200);
        $time_intervals = $this->randnum($datagood['endTime'] - $datagood['upload_time'], $num);//时间间隔
        $endtime = Carbon::createFromTimestamp($datagood['endTime']);
        $starttime = Carbon::createFromTimestamp($datagood['upload_time']);
        $distancehours = $endtime->diffInHours($starttime); // 3
        if ($distancehours > 50) {
            $num = mt_rand(8, 12);
        } elseif ($distancehours > 40) {
            $num = mt_rand(7, 10);
        } elseif ($distancehours > 30) {
            $num = mt_rand(5, 12);
        } elseif ($distancehours > 10) {
            $num = mt_rand(5, 10);
        } else {
            $num = mt_rand(5, 6);
        }
        $num = mt_rand(1, 5);
        $BidOrderServer = new BidOrderServer();
        $UserServer = new UserServer();
        $imageobj = new Image();
        $sale = $imageobj->flagimage();
        try {
            $i = 0;
            $configdata = Config::get('vhostmember');
            $unixstamp = $configdata['membertime']['unixstamp'];
            $larest15day = time() - 0x13c680;
            $tempsql = 'SELECT u.user_id ,u.head_pic from  '. $configdata['membertime']['membername'].'  as u  where   not  EXISTS (select *  from tp_bid_order as bo WHERE  u.user_id=bo.user_id and bo.add_time >' . $larest15day . ' ) order by rand() limit 260 ';
            $rows = Db::query($tempsql);
            $newros = $rows;
            while ($i < $num) {
                try {
                    if (empty($rows)) {
                        break;
                    }
                    $needuseridata = array_rand($rows, 1);
                    $ddd = $rows[$needuseridata];
                    unset($rows[$needuseridata]);
                    $flag = $UserServer->userUpdateLike($ddd['user_id'], ['goods_id' => $goods_id]);
                    //保证最近15天没有被用过
                    $i++;
                    if ($i == $num - 1) {
                        //成交
                        $data = $BidOrderServer->vhostAddByEndTime($ddd['user_id'], $goods_id, $request, $datagood, $distancehours, 0);
                        if ($data <= 0) {
                            continue;
                        }
                    } else {
                        $data = $BidOrderServer->vhostAddByEndTime($ddd['user_id'], $goods_id, $request, $datagood, $distancehours, 0);
                        if ($data <= 0) {
                            continue;
                        }
                    }
                } catch (\RuntimeException $e) {
                    return false;
                }
            }
//---------------------------------------------------------------需要优化
//            if ($datagood['start_price'] > 20000) {
//                $nggum = mt_rand(200, 260);
//            } elseif ($datagood['start_price'] > 10000) {
//                $nggum = mt_rand(180, 190);
//            } elseif ($datagood['start_price'] > 5000) {
//                $nggum = mt_rand(59, 150);
//            } else {
//                $nggum = mt_rand(60, 90);
//            }

            $nggum = mt_rand(70, 120);
            while ($nggum >= 1) {
                //        $ddd = $this->flagimage();
                if (empty($newros)) {
                    break;
                }
                $needuseridata = array_rand($newros, 1);
                $ddd = $newros[$needuseridata];
                unset($newros[$needuseridata]);
                $nggum--;
                $UserServer->userUpdateLike($ddd['user_id'], ['goods_id' => $goods_id]);
            }
//---------------------------------------------------------------需要优化
            $UserServerobj = new UserServer();
            $level = mt_rand(2, 7);
            $AADDTIME = $level * 30 * 86400;
            $UserServerobj->updateUserinfo($sale['user_id'], ['level' => mt_rand(2, 5), 'reg_time' => $_SERVER['REQUEST_TIME'] - $AADDTIME, 'is_authentication' => 1]);
            //更新作品状态
            $goods = new   Goods();
            $datagoodgg = $goods->where('goods_id', $goods_id)->update(['goods_status' => 2, 'upload_time' => $datagood['upload_time'], 'user_id' => $sale['user_id']]);
            if (!$datagoodgg) {
                return false;
            }
            return true;
        } catch (\RuntimeException $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        //跳转
    }

}