<?php

namespace app\dataapi\server;

use app\dataapi\model\GoodsShouc;
use app\dataapi\model\Goods;
use think\Db;
use think\Config;
use app\dataapi\server\Jssdk;
use think\Exception;
use think\exception\ErrorException;
use think\Request;
use app\Home\Logic\UsersLogic;
use app\dataapi\controller\JobQeue;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class GoodMessageQuene
{

    //关注商品区结束前14分钟微信消息提醒
    public  static function careFinishWarm(Request $request)
    {
        $goods_id = $request->param('id');
        $care_goods = Db::name('goods')
            ->where('goods_id', $goods_id)
            ->whereNull('delete_time')
            ->field('goods_id,goods_name,start_price,user_id')
            ->find();
        if(!empty($care_goods)){
            $userLogic = new UsersLogic();
            $sellermsg = $userLogic->get_infobyfield($care_goods['user_id'], 'openid,user_id'); // 获取卖家用户信息

            //获取商品区当前价
            $bid_goods = Db::name('bid_order')
                ->where(['goods_id' => $care_goods['goods_id']])
                ->field('bid_price')
                ->order('id DESC')
                ->find();

            if(empty($bid_goods)){
                $bid_goods['bid_price'] = $care_goods['start_price'];
            }
            try{
                //给卖家发送消息
                $template_id = 'PHxktgJK_S_sytaXXotSjcAHqzHCfJkPSVB6VWxSaVQ';
                $urlto = Config::get('domain')."item/{$care_goods['goods_id']}.html";  //http://w.sijiweipai.com/item/136349.html
                $jssdkobj = new Jssdk(Config::get('weixin_api.appid'), Config::get('weixin_api.secret'));
                //微信消息提醒
                $content = [
                    'first' => ["value" => "您发布的商品即将14分钟后结束，请您注意时间"],
                    'keyword1' => ["value" => "{$care_goods['goods_name']}", 'color' => "#173177"],
                    'keyword2' => ["value" => "{$bid_goods['bid_price']}", 'color' => "#173177"],
                    'remark' => ["value" => "立即前往查看！", 'color' => "#173177"],
                ];

                $jssdkobj->activePushMsg($sellermsg['openid'], $template_id, $urlto, $content);
                //给商品关注者发送消息
                self::send2carers($care_goods,$bid_goods);

                return true;
            }catch (\Exception $e){

            }
        }

        return false;
    }

    //给商品关注者发送信息
    public static function send2carers($care_goods,$bid_goods){
        $userLogic = new UsersLogic();
        try{
            $JobQeueobj = new  \app\dataapi\server\MqProducer();
            $template_id = 'PHxktgJK_S_sytaXXotSjcAHqzHCfJkPSVB6VWxSaVQ';
            $urlto = Config::get('domain')."item/{$care_goods['goods_id']}.html";
            //给商品区关注者发送消息
            $userids = Db::name('goods_collect')->where(['goods_id' => $care_goods['goods_id']])->column('user_id');
            foreach ($userids as $kk => $vv) {
                $user_info = $userLogic->get_infobyfield($vv, 'openid,user_id'); // 获取用户信息
                //微信消息提醒
                $content = [
                    'first' => ["value" => "您关注的商品即将14分钟后结束，请您注意时间"],
                    'keyword1' => ["value" => "{$care_goods['goods_name']}", 'color' => "#173177"],
                    'keyword2' => ["value" => "{$bid_goods['bid_price']}", 'color' => "#173177"],
                    'remark' => ["value" => "立即前往出价！", 'color' => "#173177"],
                ];
                $sendcont = json_encode(['openid'=>$user_info['openid'],'template_id'=>$template_id,'urlto'=>$urlto,'content'=>$content]);
                $jobData = ['from'=>'fanghua','type'=>'sendToCarers','ts' => time(), 'bizId' => uniqid(), 'data' => $sendcont, 'is_or_member' => false];
                $JobQeueobj->process($jobData);
            }
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        return true;
    }


    //给商品关注者发送结束提醒
    public  static function carersMsg(Request $request)
    {
        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'), Config::get('weixin_api.secret'));
        $tmp = $request->param('data');
        $tmp = \GuzzleHttp\json_decode($tmp, true);
        return $jssdkobj->activePushMsg($tmp['openid'], $tmp['template_id'], $tmp['urlto'], $tmp['content']);

    }
    //发送微信消息
    public  static function send2fansmessage(Request $request)
    {
        $jssdkobj = new Jssdk(Config::get('weixin_api.appid'), Config::get('weixin_api.secret'));
        $tmp = $request->param('data');
        file_put_contents(LOG_PATH . 'lllll.php', var_export($tmp, true));
        try {
            $tmp = \GuzzleHttp\json_decode($tmp, true);
            $jssdkobj->push_msg($tmp['id'], $tmp['msg']);

        } catch (ErrorException $e) {

            return false;
        } catch (\InvalidArgumentException $e) {
            return false;

        }
        return true;
    }

    //订单超过24h自动取消 (生成订单时，改商品区状态显示已被购买，24h不支付订单取消，状态恢复正常)
    public static function ordercancel(Request $request){
        $order_sn = $request->param('id');
        $order_sn= trim($order_sn);
        $ordercanceltime =  (int)Config::get('ordercanceltime');
        $ordercanceltime = time()-$ordercanceltime*60;
        $cancelorder = Db::name('order')->where('order_sn',$order_sn)->where('add_time','<=',$ordercanceltime)->where('cancel_time',0)->field('order_id,order_sn,user_id,add_time,cancel_time,is_sendcancel')->find();
        if(empty($cancelorder)){
            return false;
        }

        if(2==$cancelorder['is_sendcancel']){
          $msg =  self::sendwexinmessage($cancelorder);
            if(isset($msg['errcode']) && $msg['errcode']==0){
                Db::name('order')
                    ->where(['order_sn'=>$order_sn])
                    ->update(['is_sendcancel'=>3]);
                return true;
            }else{
                return false;
            }
        }
        Db::startTrans();
        try{
            //订单取消
            Db::name('order')
                ->where(['order_sn'=>$order_sn])
                ->update(['order_status'=>3,'cancel_time'=>time()]);
            //商品区重置
            Db::name('order')
                ->alias('O')
                ->join('__ORDER_GOODS__ OG','O.order_id=OG.order_id')
                ->join('__GOODS__ G','OG.goods_id=G.goods_id')
                ->where('O.order_sn',$order_sn)
                ->update(['g.is_gernerorder'=>0,'g.goods_status'=>1,'o.is_sendcancel'=>2]);
            Db::commit();
            $msg = self::sendwexinmessage($cancelorder);
            if(isset($msg['errcode']) && $msg['errcode']==0){
                Db::name('order')
                    ->where(['order_sn'=>$order_sn])
                    ->update(['is_sendcancel'=>3]);
                return true;
            }
            return false;
        }catch(\Exception $e){
            Db::rollback();
            return false;
        }
        return false;
    }


    public static function  sendwexinmessage($cancelorder){
  //给买家发送订单取消消息
            $template_id = 'C7e9MLeb7cBI7ofGTlKxD7rBzr6Czmjx1p9ZEACg4gg';
            $urlto = Config::get('domain')."index.html?#!dist/order_details?a={$cancelorder['order_id']}";   //index.html?#!dist/order_details?a={{$value.order_id}}
            $jssdkobj = new Jssdk(Config::get('weixin_api.appid'), Config::get('weixin_api.secret'));
            //微信消息提醒
            $content = [
                'first' => ["value" => "您的订单已超过24小时,系统自动取消"],
                'keyword1' => ["value" => "{$cancelorder['order_sn']}", 'color' => "#173177"],
                'keyword2' => ["value" => "{$cancelorder['add_time']}", 'color' => "#173177"],
                'keyword3' => ["value" => "{$cancelorder['cancel_time']}", 'color' => "#173177"],
                'remark' => ["value" => "立即前往查看！", 'color' => "#173177"],
            ];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_infobyfield($cancelorder['user_id'], 'openid,user_id'); // 获取用户信息
        $msg = $jssdkobj->activePushMsg($user_info['openid'], $template_id, $urlto, $content);
        return $msg;
    }


    //商品区浏览和点赞
    public static function  modifytodaynogoods(Request $request){

        $goods_id =(int)$request->param('id');
        $ctime = time();
        $JobQeueobj = new  JobQeue();
        $goodsobj = new Goods();
        $goods_data=$goodsobj->where('goods_id',$goods_id)->field('goods_id,user_id,is_upload,endTime,is_heler_likeand')->find();
        if($goods_data){
            if($goods_data->is_heler_likeand==1){
                return true;
            }
            //新添加的增加粉丝
            if ($goods_data['user_id'] != 4164 && $goods_data['user_id'] != 4145 && $goods_data['is_upload'] == 1) {
              //  $activemqserverobj = new  ActiveMqServer();
               // $activemqserverobj->autoForFansererVhostbyAli($goods_data['user_id'], $ctime);
            }

            //使用消息队列
            $JobQeueobj->autoLikeGoods($goods_data['user_id'], $goods_data['goods_id'], $goods_data['endTime']);
            Db::name('goods')->where('goods_id', $goods_data['goods_id'])->update(['is_heler_likeand' => 1]);
            return true;
        }
        return false;
    }


}