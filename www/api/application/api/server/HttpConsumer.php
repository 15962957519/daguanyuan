<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 * 参考地址 http://www.cnblogs.com/txw1958/p/weixin-js-sharetimeline.html
 * http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html  微信JS-SDK说明文档
 */

namespace app\api\server;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use  app\dataapi\server\UtilMq as Util;
use think\queue\Job;
use app\dataapi\lib\Image;
use app\dataapi\server\UserServer;
use app\dataapi\model\LikegoodMessageList;
use app\dataapi\server\AutoBidProductServer;
use Carbon\Carbon;
use think\Db;
use app\dataapi\server\Weixin;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Hook;
use think\Cache;

/**
 * 阿里云消息发送
 * Class CatsLogic
 * @package Home\Logic
 */
class HttpConsumer
{
    //签名
    private static $signature = "Signature";
    //Consumer ID
    private static $consumerid = "ConsumerID";
    //访问码
    private static $ak = "AccessKey";
    //配置信息
    private static $config = null;

    //构造函数
    function __construct($unixstampdata)
    {
        //读取配置信息
        $this::$config = $unixstampdata;
    }

    //订阅流程


    static function object2array(&$object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    public function process()
    {
        //打印配置信息
     //   var_dump($this::$config);
        //获取Topic
        $topic = self::$config["Topic"];
        //获取Topic的URL路径
        $url = self::$config["URL"];
        //阿里云身份验证码
        $ak = self::$config["Ak"];
        //阿里云身份验证密钥
        $sk = self::$config["Sk"];
        //Consumer ID
        $cid = self::$config["ConsumerID"];
        $newline = "\n";
        //构造工具对象
        $util = new Util();
        while (true) {
            try {
                //构造时间戳
                $date = time() * 1000;
                //签名字符串
                $signString = $topic . $newline . $cid . $newline . $date;
                //计算签名
                $sign = $util->calSignature($signString, $sk);
                //构造签名标记
                $signFlag = $this::$signature . ":" . $sign;
                //构造密钥标记
                $akFlag = $this::$ak . ":" . $ak;
                //标记
                $consumerFlag = $this::$consumerid . ":" . $cid;
                //构造HTTP请求发送内容类型标记
                $contentFlag = "Content-Type:text/html;charset=UTF-8";
                //构造HTTP头部信息
                $headers = array(
                    $signFlag,
                    $akFlag,
                    $consumerFlag,
                    $contentFlag,
                );
                //构造HTTP请求URL
                $getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
                //初始化网络通信模块
                $ch = curl_init();
                //填充HTTP头部信息
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //设置HTTP请求类型,此处为GET
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //设置HTTP请求URL
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                //构造执行环境
                ob_start();
                //开始发送HTTP请求
                curl_exec($ch);
                //获取请求应答消息
                $result = ob_get_contents();


                //清理执行环境
                ob_end_clean();
                //打印请求应答信息
                //关闭HTTP网络连接
                curl_close($ch);
                //解析HTTP应答信息
                $messages = json_decode($result, true);
                //如果应答信息中的没有包含任何的Topic信息,则直接跳过
//                if (count($messages) == 0) {
//                    continue;
//                }
                //依次遍历每个Topic消息
                foreach ((array)$messages as $message) {
                    $res = json_decode($message['body']);
                    $res = self::object2array($res);
                    self::like($res);
                    //获取时间戳
                    $date = (int)($util->microtime_float() * 1000);
                    //构造删除Topic消息URL
                    $delUrl = $url . "/message/?msgHandle=" . $message['msgHandle'] . "&topic=" . $topic . "&time=" . $date;
                    //签名字符串
                    $signString = $topic . $newline . $cid . $newline . $message['msgHandle'] . $newline . $date;
                    //计算签名
                    $sign = $util->calSignature($signString, $sk);
                    //构造签名标记
                    $signFlag = $this::$signature . ":" . $sign;
                    //构造密钥标记
                    $akFlag = $this::$ak . ":" . $ak;
                    //构造消费者组标记
                    $consumerFlag = $this::$consumerid . ":" . $cid;
                    //构造HTTP请求头部信息
                    $delheaders = array(
                        $signFlag,
                        $akFlag,
                        $consumerFlag,
                        $contentFlag,
                    );
                    //初始化网络通信模块
                    $ch = curl_init();
                    //填充HTTP请求头部信息
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $delheaders);
                    //设置HTTP请求URL信息
                    curl_setopt($ch, CURLOPT_URL, $delUrl);
                    //设置HTTP请求类型,此处为DELETE
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    //构造执行环境
                    ob_start();
                    //开始发送HTTP请求
                    curl_exec($ch);
                    //获取请求应答消息
                    $result = ob_get_contents();
                    //清理执行环境
                    ob_end_clean();
                    //打印应答消息
                    // var_dump($result);
                    //关闭连接
                    curl_close($ch);
                }
            } catch (Exception $e) {
                //打印异常信息
                echo $e->getMessage();
            }
          //  break;
        }
    }


    static function like($data)
    {
        $goods_id = $data['goods_id'];
        $randlike = mt_rand(1, 3);
        $image = new Image();
        $UserServer = new UserServer();
        $tmpdatauseridarray = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status',1)->field(['user_id','endTime'])->find();
        if(isset($tmpdatauseridarray['endTime']) && $tmpdatauseridarray['endTime']<=time()){
            return false;
        }
        do {
            $randlike--;
            $ddd = $image->flagimage();
            $userid = $ddd['user_id'];
            echo "is like {$goods_id}", PHP_EOL;
            if ($userid > 0 && $goods_id > 0) {
                $UserServer->vhostuserUpdateLike($userid, ['goods_id' => $goods_id]);
            }
            echo "is like {$goods_id} end", PHP_EOL;
        } while ($randlike > 0);
        //更新数据库
        $LikegoodMessageList = new LikegoodMessageList();
        $LikegoodMessageList->where('id', $data['id'])->delete(true);
        unset($LikegoodMessageList,$UserServer,$image);
        return true;
    }


    //消费喜欢
    public function processfanserver()
    {
        //打印配置信息
        //var_dump($this::$config);
        //获取Topic
        $topic = self::$config["Topic"];
        //获取Topic的URL路径
        $url = self::$config["URL"];
        //阿里云身份验证码
        $ak = self::$config["Ak"];
        //阿里云身份验证密钥
        $sk = self::$config["Sk"];
        //Consumer ID
        $cid = self::$config["ConsumerID"];
        $newline = "\n";
        //构造工具对象
        $util = new Util();
        while (true) {
            try {
                //构造时间戳
                $date = time() * 1000;
                //签名字符串
                $signString = $topic . $newline . $cid . $newline . $date;
                //计算签名
                $sign = $util->calSignature($signString, $sk);
                //构造签名标记
                $signFlag = $this::$signature . ":" . $sign;
                //构造密钥标记
                $akFlag = $this::$ak . ":" . $ak;
                //标记
                $consumerFlag = $this::$consumerid . ":" . $cid;
                //构造HTTP请求发送内容类型标记
                $contentFlag = "Content-Type:text/html;charset=UTF-8";
                //构造HTTP头部信息
                $headers = array(
                    $signFlag,
                    $akFlag,
                    $consumerFlag,
                    $contentFlag,
                );
                //构造HTTP请求URL
                $getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
                //初始化网络通信模块
                $ch = curl_init();
                //填充HTTP头部信息
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //设置HTTP请求类型,此处为GET
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //设置HTTP请求URL
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                //构造执行环境
                ob_start();
                //开始发送HTTP请求
                curl_exec($ch);
                //获取请求应答消息
                $result = ob_get_contents();


                //清理执行环境
                ob_end_clean();
                //打印请求应答信息
              //  var_dump($result);
                //关闭HTTP网络连接
                curl_close($ch);
                //解析HTTP应答信息
                $messages = json_decode($result, true);
                //如果应答信息中的没有包含任何的Topic信息,则直接跳过
                if (count($messages) == 0) {
                    continue;
                }
                //依次遍历每个Topic消息
                foreach ((array)$messages as $message) {
                    //var_dump($message);
                    $res = json_decode($message['body']);
                    $res = self::object2array($res);
                    self::index($res);
                    //获取时间戳
                    $date = (int)($util->microtime_float() * 1000);
                    //构造删除Topic消息URL
                    $delUrl = $url . "/message/?msgHandle=" . $message['msgHandle'] . "&topic=" . $topic . "&time=" . $date;
                    //签名字符串
                    $signString = $topic . $newline . $cid . $newline . $message['msgHandle'] . $newline . $date;
                    //计算签名
                    $sign = $util->calSignature($signString, $sk);
                    //构造签名标记
                    $signFlag = $this::$signature . ":" . $sign;
                    //构造密钥标记
                    $akFlag = $this::$ak . ":" . $ak;
                    //构造消费者组标记
                    $consumerFlag = $this::$consumerid . ":" . $cid;
                    //构造HTTP请求头部信息
                    $delheaders = array(
                        $signFlag,
                        $akFlag,
                        $consumerFlag,
                        $contentFlag,
                    );
                    //初始化网络通信模块
                    $ch = curl_init();
                    //填充HTTP请求头部信息
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $delheaders);
                    //设置HTTP请求URL信息
                    curl_setopt($ch, CURLOPT_URL, $delUrl);
                    //设置HTTP请求类型,此处为DELETE
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    //构造执行环境
                    ob_start();
                    //开始发送HTTP请求
                    curl_exec($ch);
                    //获取请求应答消息
                    $result = ob_get_contents();
                    //清理执行环境
                    ob_end_clean();
                    //打印应答消息
                  //  var_dump($result);
                    //关闭连接
                    curl_close($ch);
                }
            } catch (Exception $e) {
                //打印异常信息
                echo $e->getMessage();
            }
        }
    }


    //消费出价
    public function processbid()
    {
        //打印配置信息
        //获取Topic
        $topic = self::$config["Topic"];
        //获取Topic的URL路径
        $url = self::$config["URL"];
        //阿里云身份验证码
        $ak = self::$config["Ak"];
        //阿里云身份验证密钥
        $sk = self::$config["Sk"];
        //Consumer ID
        $cid = self::$config["ConsumerID"];
        $newline = "\n";
        //构造工具对象
        $util = new Util();
        while (true) {
            try {
                //构造时间戳
                $date = time() * 1000;
                //签名字符串
                $signString = $topic . $newline . $cid . $newline . $date;
                //计算签名
                $sign = $util->calSignature($signString, $sk);
                //构造签名标记
                $signFlag = $this::$signature . ":" . $sign;
                //构造密钥标记
                $akFlag = $this::$ak . ":" . $ak;
                //标记
                $consumerFlag = $this::$consumerid . ":" . $cid;
                //构造HTTP请求发送内容类型标记
                $contentFlag = "Content-Type:text/html;charset=UTF-8";
                //构造HTTP头部信息
                $headers = array(
                    $signFlag,
                    $akFlag,
                    $consumerFlag,
                    $contentFlag,
                );
                //构造HTTP请求URL
                $getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
                //初始化网络通信模块
                $ch = curl_init();
                //填充HTTP头部信息
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //设置HTTP请求类型,此处为GET
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //设置HTTP请求URL
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                //构造执行环境
                ob_start();
                //开始发送HTTP请求
                curl_exec($ch);
                //获取请求应答消息
                $result = ob_get_contents();


                //清理执行环境
                ob_end_clean();
                //打印请求应答信息
              //  var_dump($result);
                //关闭HTTP网络连接
                curl_close($ch);
                //解析HTTP应答信息
                $messages = json_decode($result, true);
                //如果应答信息中的没有包含任何的Topic信息,则直接跳过
                if (count($messages) == 0) {
                    continue;
                }
                //依次遍历每个Topic消息
                foreach ((array)$messages as $message) {
                  //  var_dump($message);
                    $res = json_decode($message['body']);
                    //获取body
                    $res = self::object2array($res);
                    if(!self::indexbid($res)){
                        continue;
                    }
                    //获取时间戳
                    $date = (int)($util->microtime_float() * 1000);
                    //构造删除Topic消息URL
                    $delUrl = $url . "/message/?msgHandle=" . $message['msgHandle'] . "&topic=" . $topic . "&time=" . $date;
                    //签名字符串
                    $signString = $topic . $newline . $cid . $newline . $message['msgHandle'] . $newline . $date;
                    //计算签名
                    $sign = $util->calSignature($signString, $sk);
                    //构造签名标记
                    $signFlag = $this::$signature . ":" . $sign;
                    //构造密钥标记
                    $akFlag = $this::$ak . ":" . $ak;
                    //构造消费者组标记
                    $consumerFlag = $this::$consumerid . ":" . $cid;
                    //构造HTTP请求头部信息
                    $delheaders = array(
                        $signFlag,
                        $akFlag,
                        $consumerFlag,
                        $contentFlag,
                    );
                    //初始化网络通信模块
                    $ch = curl_init();
                    //填充HTTP请求头部信息
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $delheaders);
                    //设置HTTP请求URL信息
                    curl_setopt($ch, CURLOPT_URL, $delUrl);
                    //设置HTTP请求类型,此处为DELETE
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    //构造执行环境
                    ob_start();
                    //开始发送HTTP请求
                    curl_exec($ch);
                    //获取请求应答消息
                    $result = ob_get_contents();
                    //清理执行环境
                    ob_end_clean();
                    //打印应答消息
                   // var_dump($result);
                    //关闭连接
                    curl_close($ch);
                }
            } catch (Exception $e) {
                //打印异常信息
                echo $e->getMessage();
            }
            break;
        }
    }

    //通用方法
    public function processcommon($methed)
    {
        //打印配置信息
        //获取Topic
        $topic = self::$config["Topic"];
        //获取Topic的URL路径
        $url = self::$config["URL"];
        //阿里云身份验证码
        $ak = self::$config["Ak"];
        //阿里云身份验证密钥
        $sk = self::$config["Sk"];
        //Consumer ID
        $cid = self::$config["ConsumerID"];
        $newline = "\n";
        //构造工具对象
        $util = new Util();
        while (true) {
            try {
                //构造时间戳
                $date = time() * 1000;
                //签名字符串
                $signString = $topic . $newline . $cid . $newline . $date;
                //计算签名
                $sign = $util->calSignature($signString, $sk);
                //构造签名标记
                $signFlag = $this::$signature . ":" . $sign;
                //构造密钥标记
                $akFlag = $this::$ak . ":" . $ak;
                //标记
                $consumerFlag = $this::$consumerid . ":" . $cid;
                //构造HTTP请求发送内容类型标记
                $contentFlag = "Content-Type:text/html;charset=UTF-8";
                //构造HTTP头部信息
                $headers = array(
                    $signFlag,
                    $akFlag,
                    $consumerFlag,
                    $contentFlag,
                );
                //构造HTTP请求URL
                $getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
                //初始化网络通信模块
                $ch = curl_init();
                //填充HTTP头部信息
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //设置HTTP请求类型,此处为GET
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //设置HTTP请求URL
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                //构造执行环境
                ob_start();
                //开始发送HTTP请求
                curl_exec($ch);
                //获取请求应答消息
                $result = ob_get_contents();
                //清理执行环境
                ob_end_clean();
                //打印请求应答信息
                //关闭HTTP网络连接
                curl_close($ch);
                //解析HTTP应答信息
                $messages = json_decode($result, true);
                //如果应答信息中的没有包含任何的Topic信息,则直接跳过
                if (count($messages) == 0) {
                    continue;
                }
                //依次遍历每个Topic消息
                foreach ((array)$messages as $message) {
                    $res = json_decode($message['body']);
                    //获取body
                    $res = self::object2array($res);
                    call_user_func($methed,$res);
                    //获取时间戳
                    $date = (int)($util->microtime_float() * 1000);
                    //构造删除Topic消息URL
                    $delUrl = $url . "/message/?msgHandle=" . $message['msgHandle'] . "&topic=" . $topic . "&time=" . $date;
                    //签名字符串
                    $signString = $topic . $newline . $cid . $newline . $message['msgHandle'] . $newline . $date;
                    //计算签名
                    $sign = $util->calSignature($signString, $sk);
                    //构造签名标记
                    $signFlag = $this::$signature . ":" . $sign;
                    //构造密钥标记
                    $akFlag = $this::$ak . ":" . $ak;
                    //构造消费者组标记
                    $consumerFlag = $this::$consumerid . ":" . $cid;
                    //构造HTTP请求头部信息
                    $delheaders = array(
                        $signFlag,
                        $akFlag,
                        $consumerFlag,
                        $contentFlag,
                    );
                    //初始化网络通信模块
                    $ch = curl_init();
                    //填充HTTP请求头部信息
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $delheaders);
                    //设置HTTP请求URL信息
                    curl_setopt($ch, CURLOPT_URL, $delUrl);
                    //设置HTTP请求类型,此处为DELETE
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    //构造执行环境
                    ob_start();
                    //开始发送HTTP请求
                    curl_exec($ch);
                    //获取请求应答消息
                    $result = ob_get_contents();
                    //清理执行环境
                    ob_end_clean();
                    //打印应答消息
                    var_dump($result);
                    //关闭连接
                    curl_close($ch);
                }
                break;
            } catch (Exception $e) {
                //打印异常信息
                echo $e->getMessage();
            }
        }
    }


    /*
     *@//自动粉丝
     *
     */
    public function index(array $data)
    {
        $user_id = $data['user_id'];
        $inseridid = $data['collectgoodmessagelist_insertid'] ?? 0;
        $this->autoGiveFanUser($user_id, $inseridid);
        return true;
    }


    /*
 * 赠送会员粉丝
 *
 */
    private function autoGiveFanUser(int $user_id, int $inseridid)
    {
        $userpic = [];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($user_id); // 获取用户信息

        if (isset($user_info['result']['level']) && $user_info['result']['level'] == 1) {
            $fancollectcount = Db::name('fans_collect')->alias('a')->join('third_users w', 'a.fans_id=w.user_id')->where('a.user_id', $user_id)->whereNull('a.delete_time')->whereNull('w.delete_time')->count(); //获取收藏数量    粉丝
            if ($fancollectcount > 80) {
                //减少粉丝
                $limit = mt_rand(1, 20);
                $useridarray = Db::table('tp_fans_collect')->where('user_id', $user_id)->whereNull('delete_time')->order('add_time asc')->limit($limit)->column('collect_id');
                if (!empty($useridarray)) {
                    $useridarray = array_unique($useridarray);
                }
                Db::table('tp_fans_collect')
                    ->whereIn('collect_id', $useridarray)
                    ->update([
                        'delete_time' => time()
                    ]);

                if ($inseridid > 0) {
                    Db::table('tp_collectgoodmessagelist')
                        ->where('id', $inseridid)
                        ->delete();
                }
                return true;
            }
        }

        if (isset($user_info['result']['level']) && $user_info['result']['level'] > 0) {
            $memberlevel = $user_info['result']['level'];
            $memberProductCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'lastPembeFansCount', $memberlevel);
            echo "正在执行{$user_id}\r\n";
            if (!$memberProductCount) {
                return false;
            }
            $x = $memberProductCount;
            $Image = new Image();
            do {
                // 启动事务
                Db::startTrans();
                try {
                    $ddd = $Image->flagimage();
                    $flag = Db::table('tp_fans_collect')->where(['user_id' => $user_id, 'fans_id' => $ddd['user_id']])->find();

                    if (!empty($flag)) {
                        Db::rollback();
                        continue;
                    }
                    if ($flag['level'] == 1) {
                        Db::table('tp_users')
                            ->where('user_id', $ddd['user_id'])
                            ->update([
                                'level' => mt_rand(2, 5)
                            ]);
                    }


                    Db::table('tp_fans_collect')->insertGetId([
                        'user_id' => $user_id,
                        'fans_id' => $ddd['user_id'],
                        'add_time' => time()
                    ]);
                    echo $x;
                    echo "\r\n";
                    $x--;
                    // 提交事务
                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return false;
                }
            } while ($x > 0);
            Db::table('tp_users')
                ->where('user_id', $user_id)
                ->update([
                    'is_give_freefans' => ['exp', 'is_give_freefans+' . $x],
                    'is_give_freefans_datetime' => time()
                ]);

            if ($inseridid > 0) {
                Db::table('tp_collectgoodmessagelist')
                    ->where('id', $inseridid)
                    ->delete();

            }

            return true;
        }
        return false;
    }


    /*
   *@//自动出价
   *
   */
    public function indexbid(array $data)
    {
        $good_id = $data['goods_id'];
        $inseridid = $data['id'];
        //插入id
        if ($good_id <= 0) {
            return;
        }
        //获取执行时间 匹配是否需要再执行
        $implement_datetime = Db::name('forindex')->where('id', $inseridid)->value('datetime');
        if ($implement_datetime && $implement_datetime > 0 && ($implement_datetime + 100 < time())) {
            return true;
        }
        $Image = new Image();
        $datauser = $Image->flagimage();
        $userid = $datauser['user_id'];
        if ($userid <= 0) {
            return false;
        }



        $countmax = Db::name('goods')->where('goods_id', $good_id)->field('is_toplatform,upload_time,goods_id,endTime')->find();
        if ($countmax['upload_time'] && $countmax['endTime'] > time()) {
            $countmax_id = Db::name('forindex')->where('goods_id', $good_id)->where('datetime', '>', $countmax['upload_time'])->where('is_over', 1)->count('id');
            if ($countmax['is_toplatform'] == 3) {
                Db::name('goods')->where('goods_id', $good_id)->update(['on_time' => time()]);
            }
            if ($countmax_id > 13) {
                return true;
            }
        } else {
            return true;
        }
        $BidOrderServer = new BidOrderServer();
        $data = $BidOrderServer->vhostAddByCli($userid, $good_id);
        if(!$data){
            return false;
        }
        //更新是否完成了
        if ($inseridid > 0) {
            $goods = Db::name('forindex')->where('id', $inseridid)->update(['is_over' => 1, 'delete_time' => time()]);
            if ($goods != false) {
                $UserServer = new UserServer();
                $mumber = mt_rand(2, 4);
                while ($mumber > 0) {
                    $mumber--;
                    $ddd = $Image->flagimage();
                    $userid = $ddd['user_id'];
                    if ($userid > 0 && $good_id > 0) {
                        $flag = $UserServer->userUpdateLike($userid, ['goods_id' => $good_id]);
                        if (!$flag) {
                            continue;
                        }
                    }
                }
            }
        }
        if ($data > 0) {
            return true;
        }
        return false;
    }



    //基于http消费
    public function processbyhttp()
    {
        //打印配置信息
        //获取Topic
        $topic = self::$config["Topic"];
        //获取Topic的URL路径
        $url = self::$config["URL"];
        //阿里云身份验证码
        $ak = self::$config["Ak"];
        //阿里云身份验证密钥
        $sk = self::$config["Sk"];
        //Consumer ID
        $cid = self::$config["ConsumerID"];
        $newline = "\n";
        //构造工具对象
        $util = new Util();
            try {
             //   var_dump("ing get messages from aliyun",PHP_EOL) ;
                //构造时间戳
                $date = time() * 1000;
                //签名字符串
                $signString = $topic . $newline . $cid . $newline . $date;
                //计算签名
                $sign = $util->calSignature($signString, $sk);
                //构造签名标记
                $signFlag = $this::$signature . ":" . $sign;
                //构造密钥标记
                $akFlag = $this::$ak . ":" . $ak;
                //标记
                $consumerFlag = $this::$consumerid . ":" . $cid;
                //构造HTTP请求发送内容类型标记
                $contentFlag = "Content-Type:text/html;charset=UTF-8";
                //构造HTTP头部信息
                $headers = array(
                    $signFlag,
                    $akFlag,
                    $consumerFlag,
                    $contentFlag,
                );
                //构造HTTP请求URL
                $getUrl = $url . "/message/?topic=" . $topic . "&time=" . $date . "&num=32";
                //初始化网络通信模块
                $ch = curl_init();
                //填充HTTP头部信息
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //设置HTTP请求类型,此处为GET
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                //设置HTTP请求URL
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                //构造执行环境
                ob_start();
                //开始发送HTTP请求
                curl_exec($ch);
                //获取请求应答消息
                $result = ob_get_contents();
                //清理执行环境
                ob_end_clean();
                //打印请求应答信息
                //关闭HTTP网络连接
                curl_close($ch);
                //解析HTTP应答信息
                $messages = json_decode($result, true);
                //如果应答信息中的没有包含任何的Topic信息,则直接跳过
                //依次遍历每个Topic消息
                foreach ((array)$messages as $message) {
                    $res = json_decode($message['body']);
                    $res = self::object2array($res);
                    self::like($res);
                    //获取时间戳
                    $date = (int)($util->microtime_float() * 1000);
                    //构造删除Topic消息URL
                    $delUrl = $url . "/message/?msgHandle=" . $message['msgHandle'] . "&topic=" . $topic . "&time=" . $date;
                    //签名字符串
                    $signString = $topic . $newline . $cid . $newline . $message['msgHandle'] . $newline . $date;
                    //计算签名
                    $sign = $util->calSignature($signString, $sk);
                    //构造签名标记
                    $signFlag = $this::$signature . ":" . $sign;
                    //构造密钥标记
                    $akFlag = $this::$ak . ":" . $ak;
                    //构造消费者组标记
                    $consumerFlag = $this::$consumerid . ":" . $cid;
                    //构造HTTP请求头部信息
                    $delheaders = array(
                        $signFlag,
                        $akFlag,
                        $consumerFlag,
                        $contentFlag,
                    );
                    //初始化网络通信模块
                    $ch = curl_init();
                    //填充HTTP请求头部信息
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $delheaders);
                    //设置HTTP请求URL信息
                    curl_setopt($ch, CURLOPT_URL, $delUrl);
                    //设置HTTP请求类型,此处为DELETE
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    //构造执行环境
                    ob_start();
                    //开始发送HTTP请求
                    curl_exec($ch);
                    //获取请求应答消息
                    $result = ob_get_contents();
                    //清理执行环境
                    ob_end_clean();
                    //打印应答消息
                    // var_dump($result);
                    //关闭连接
                    curl_close($ch);
                }
            } catch (Exception $e) {
                //打印异常信息
                echo $e->getMessage();
            }
    }

}
