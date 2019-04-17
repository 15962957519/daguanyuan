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
use app\dataapi\server\Jssdk;
use app\dataapi\server\UserServer;
use  app\dataapi\server\Weixin;
use app\dataapi\server\BidOrderServer;
//生成订单 根据倒计时生成订单


class OrderGeneration extends BaseApi
{
    /**
     * 自动更新拍卖结束后生成订单列表
     * @param OBJ $request 参数解释
     * @return  json
     */
    public function autoremindproductserver(Request $request)
    {
        $subQuery = Db::name('bid_order')
            ->alias('bo')
            ->where('bo.goods_id=a.goods_id')
            ->where('bo.is_gernerorder', 0)
            ->whereNull('bo.delete_time')
            ->field('bo.*')
            ->buildSql();
        $BidOrderServer = new BidOrderServer();
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->where('a.is_gernerorder', 0)
            ->where('a.endTime', '<', time())
            ->whereNull('a.delete_time')
            ->whereExists($subQuery)
            ->field('a.goods_id,a.upload_time,a.user_id,a.goods_name,a.goods_content,a.protect_price')
            ->limit(1)
            ->select();


        if (!empty($hot_goods)) {
            foreach ($hot_goods as $v) {
                //发送给微信
                $tempborderdata = Db::name('bid_order')->where('goods_id', $v['goods_id'])->where('upload_time', $v['upload_time'])
                    ->where('bid_price', '=', function ($query) use ($v) {
                        $query->name('bid_order')
                            ->where('goods_id', $v['goods_id'])
                            ->where('upload_time', $v['upload_time'])
                            ->whereNull('delete_time')
                            ->field('max(bid_price) as bid_price');
                    })->find();


                if ($v['goods_name'] == '') {
                    $v['goods_name'] = mb_substr($v['goods_content'], 10);
                }
                //计算出保留价问题
                if ($v['protect_price'] > 0 && $v['protect_price'] > $tempborderdata['bid_price']) {
                    Db::name('bid_order')->where('id', $tempborderdata['id'])->update(['is_gernerorder' => 1]);
                    //清除生成的出价信息
                    Db::name('bid_order')->where('goods_id', $v['goods_id'])->update(['delete_time' => time()]);
                    continue;
                }

                if (!isset($tempborderdata['id'])) {
                    //清除生成的出价信息
                    Db::name('bid_order')->where('goods_id', $v['goods_id'])->update(['delete_time' => time()]);
                    continue;
                }
                $tempborderdataordergoods = Db::name('order_goods')->where('goods_id', $v['goods_id'])->where('goods_price', $tempborderdata['bid_price'])->where('upload_time', $v['upload_time'])->count();
                if ($tempborderdataordergoods > 0) {
                    continue;
                }
                Db::startTrans();
                // 启动事务
                try {
                    $order_no = $BidOrderServer->build_order_no();
                    //生成订单表数据
                    $data = [
                        'order_sn' => $order_no,
                        'user_id' => $tempborderdata['user_id'] ?? 0,
                        'add_time' => time(),
                        'sale_user_id' => $v['user_id'],
                        'order_amount' => $tempborderdata['bid_price'],
                        'total_amount' => $tempborderdata['bid_price']
                    ];

                    Db::name('order')->insert($data);
                    $insetid = Db::name('order')->getLastInsID();
                    Db::name('Goods')->where('goods_id', $v['goods_id'])->update(['goods_status' => 2, 'is_gernerorder' => 1]);
                    Db::name('bid_order')->where('id', $tempborderdata['id'])->update(['is_gernerorder' => 1]);
                    if ($insetid > 0) {
                        $dataorder = [
                            'order_id' => $insetid,
                            'goods_id' => $v['goods_id'],
                            'goods_price' => $tempborderdata['bid_price'],
                            'upload_time' => $v['upload_time'],
                        ];
                        Db::name('order_goods')->insertGetId($dataorder);
                        $dataorderaction = [
                            'order_id' => $insetid,
                            'log_time' => time(),
                        ];
                        Db::name('order_action')->insertGetId($dataorderaction);
                        $insetid = 0;
                        // 提交事务
                        Db::commit();

                        //给发布人发信息
//                        $urlto = Config::get('domain') . "index.html?#!dist/item?a={$v['goods_id']}";
                        $urlto = Config::get('domain') . "item/{$v['goods_id']}.html";
                        $nickname = UserServer::getThirdUserinfoByUserids($v['user_id'], 'user_id,nickname,openid');
                        $content = " 尊敬的“{$nickname['nickname']}”您好，您发布的商品区（{$v['goods_name']}）已经拍出<a href='{$urlto}'>点击进入 </a>";
                        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'), Config::get('weixin_api.secret'));
                        $jssdkobj->push_msg($nickname['openid'], $content);

                        //生成订单通知
                        $urlto_order = Config::get('domain') . "dp_item/{$insetid}.html";
                        $template_id ='jZO8V1b5UPbqvcgMkWjUAWCIhtfpLgx1v9J9EFtElHw';
                        $datatime =(new \DateTime())->format("Y-m-d H:i:s");
                        $jssdkobj =   new Jssdk(Config::get('weixin_api.appid'),Config::get('weixin_api.secret'));
                        $content=[
                            'first'  =>  ["value"=>"订单生成通知",'color'=>"#173177"],
                            'keyword1'  =>  ["value"=>$datatime,'color'=>"#173177"],
                            'keyword2'  =>  ["value"=>$v['goods_name'],'color'=>"#173177"],
                            'keyword2'  =>  ["value"=>$order_no,'color'=>"#173177"],
                            'remark'  => ["value"=>"订单生成成功!",'color'=>"#173177"],
                        ];
                        $getorederperson = UserServer::getThirdUserinfoByUserids($tempborderdata['user_id'], 'user_id,nickname,openid');
                        $jssdkobj->activePushMsg($getorederperson['openid'],$template_id,$urlto_order,$content);

                        /*try{
                            $JobQeueobj = new  \app\dataapi\server\MqProducer();
                            //发送给粉丝的信息
                            $ordercanceltime =  (int)Config::get('ordercanceltime');
                            $ordercanceltime = time()+$ordercanceltime*60;
                            $jobData = ['type'=>'ordercancel','ts' => $ordercanceltime, 'bizId' => uniqid(), 'data' => json_encode(['id'=>$order_no,'msg'=>"订单取消"]), 'is_or_member' => '0'];
                            if($JobQeueobj->process($jobData)){
                                Db::name('order')->where('order_sn',$order_no)->limit(1)->update(['is_sendcancel'=>1]);
                            }
                        }catch (\ErrorException $e){

                        }*/

                    } else {
                        Db::rollback();
                    }
                } catch (PDOException $e) {
                    // 回滚事务
                    Db::rollback();
                    continue;
                }
            }
            Response::create(['status' => '200', 'code' => '1', 'error' => '', 'message' => 'to mycat is sucess'], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '0', 'error' => '', 'message' => 'empty'], 'json')->header($this->header)->send();
    }


}
