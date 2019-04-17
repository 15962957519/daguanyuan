<?php
namespace app\dataapi\controller;

use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use think\Db;
use think\Request;
use think\Response;
use think\Hook;
use app\dataapi\model\BidOrder;
use  app\dataapi\controller\BaseApi;
use app\dataapi\server\UserServer;
use app\dataapi\controller\JobQeue;
use app\dataapi\server\AliyunOss;
use Hprose\Client;
use app\dataapi\server\BidOrderServer;
use app\dataapi\model\Goods;
use think\Config;
//rpc调用golang 程序
class LtteryShop extends BaseApi
{
    private $user_data;

    public function _initialize()
    {
        $result = Hook::exec('app\\dataapi\\behavior\\Jwt', 'appGetTokencanNull', $this->user_data);
        parent::_initialize();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function user()
    {
        $self_user_id = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($self_user_id);
        Response::create(['status' => '200', 'code' => '1', 'user' => $user_info, 'message' => '产品刘表成功'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function userrecord(Request $request)
    {
        $self_user_id = $this->user_data['user_id'];
        $userLogic = new UsersLogic();
        return $this->getMyOrder( $request);

    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function account_log(Request $request)
    {
        $userLogic = new UsersLogic();
        $data = $userLogic->get_account_log(998, $request->get('type'));
        $account_log = $data['result'];
        Response::create(['status' => '200', 'code' => '1', 'user' => $account_log, 'message' => '积分使用表成功'], 'json')->header($this->header)->send();
    }
    public function cprolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 1;
        $goods_id = (int)$request->param('goods_id');
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $Goods = new   Goods();
        //通过id再查
        $hot_goods = $Goods->where('goods_id', $goods_id)->whereNull('delete_time')->select();
        $imgdataobj = null;
        foreach ($hot_goods as $key => &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = $ProductServerobj->ihtmlspecialchars($v['goods_content']);

            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods[$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods[$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', 31536000],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods[$key]['img'] = [];
                        $hot_goods[$key]['nowaterimg'] = [];
                    }
                }
            }
            Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $hot_goods], 'json')->header($this->header)->send();
        }

        Response::create(['status' => '200', 'code' => '4000', 'message' => '产品刘表成功', 'plists' => []], 'json')->header($this->header)->send();
    }


    public function prolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 1;
        $goods_id = (int)$request->param('goods_id');
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $Goods = new   Goods();
        //通过id再查
        $hot_goods = $Goods->where('goods_id', $goods_id)->whereNull('delete_time')->select();
        $imgdataobj = null;
        foreach ($hot_goods as $key => &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = $ProductServerobj->ihtmlspecialchars($v['goods_content']);

            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods[$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods[$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', 31536000],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods[$key]['img'] = [];
                        $hot_goods[$key]['nowaterimg'] = [];
                    }
                }
            }
            Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $hot_goods], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => []], 'json')->header($this->header)->send();
    }

    /**
     * @return mixed
     */
    public function getMyOrder(Request $request)
    {
        $userid = $this->user_data['user_id'];
        $userid = 8466;
        $bidobj = new BidOrderServer();
        $display = (int)$request->param('display');
        $wherearray = [
            'field' => 'max(bid_price),goods_id,add_time,user_id',
            'limit' => 10,
            'map' => ['og.exchange_integral' => ['=', 0]],
            'endtime' => $_SERVER['REQUEST_TIME'],
            'page' => (int)$request->param('page'),
        ];
        if ($display == 1) {
            $wherearray['user_id'] = $userid;
        }
        $count = 0;
        $data = $bidobj->Orderlists($wherearray, $count, true);

// status  '1默认状态  2已付款  3已经发货 4已结收货成功 5已经评价 6订单自动取消 7 协议退款 8 协议退货 9 退货加退款'
        if (!empty($data)) {
            $bidorder = new BidOrder();
            $BidOrderServer = new BidOrderServer();
            $aliiamgobj = new AliyunOss(false);
            $ProductServerobj = new ProductServer();
            $datetime = new \DateTime('now');
            $currnet = $_SERVER['REQUEST_TIME'];
            $timev = 0.00;
            $expritetime = 0;
            $goods = new   Goods();
            $imgdataobj = null;
            $userLogic = new UsersLogic();
            foreach ($data as $key => &$v) {


                $user_info = $userLogic->get_info($v['user_id']); // 获取用户信息

                $v['nickname'] = $user_info['result']['nickname'];
                $v['head_pic'] = $user_info['result']['head_pic'];
                $v['mobile'] = $user_info['result']['mobile'];
                $v['statusdesc'] = '';
                $datagood = $goods->where('goods_id', $v['goods_id'])->field('goods_name,goods_status,goods_content,start_price,every_add_price,upload_time,endTime,reserveprice')->find();
                if ($datagood) {
                    $datagood = $datagood->toArray();
                    if (!empty($datagood['goods_name'])) {
                        $datagood['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $datagood['goods_name']);
                        $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $datagood['goods_name']);
                        $v['goods_name'] = mb_substr($v['goods_name'], 0, 8);
                    }
                }
                $v['add_time'] = $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '';
                $imgdataobj = Db::name('GoodsImages');
                $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                    ->where(['goods_id' => $v['goods_id']])
                    ->whereNull('delete_time')
                    ->order('img_id')
                    ->cache(true)
                    ->limit(1)
                    ->select();

                foreach ($imgdata as $vs) {
                    if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                        $v['img'][] = ['img' => $vs['image_url_remote']];
                        $v['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                    } else {
                        try {
                            $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                            $v['img'][] = ['img' => $img];
                            $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                            $v['nowaterimg'][] = ['img' => $nowaterimg];
                            $imgdataobj->where('img_id', $vs['img_id'])
                                ->update([
                                    'image_url_remote' => $img,
                                    'image_url_remote_expire' => ['exp', 31536000],
                                    'image_url_remote_nowater' => $nowaterimg
                                ]);
                        } catch (\Exception $e) {
                            $v['img'] = [];
                            $v['nowaterimg'] = [];
                        }
                    }
                }
            }

        }
        Response::create(['data' => $data, 'page' => ['total' => $count, 'current' => (int)$request->param('page'), 'limit' => 10], 'code' => '2000', 'message' => '获取订单信息'], 'json')->header($this->header)->send();
    }


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index()
    {
        $hot_goods = Db::name('goods')->where('exchange_integral>0')->limit(20)->cache(true, TPSHOP_CACHE_TIME)->field('goods_id,exchange_integral,goods_name,endTime')->select();

        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $BidOrderServerobj = new BidOrderServer();
        $bidorder = new BidOrder();
        foreach ($hot_goods as $key => &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }

            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('*')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->limit(9)
                ->select();
            $hot_goods[$key]['img'] = [];
            $hot_goods[$key]['nowaterimg'] = [];
            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods[$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods[$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $nomasterimg = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['img'][] = ['img' => $nomasterimg];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['nowaterimg'][] = ['img' => $nowaterimg];

                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $nomasterimg,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);

                    } catch (Exception $e) {
                        $hot_goods[$key]['img'] = [];
                        $hot_goods[$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        Response::create(['data' => $hot_goods, 'code' => '2000', 'message' => '获取信息成功'], 'json')->header($this->header)->send();
    }


//获取用户下面所有的产品
    public function getProductsByUserid(Request $request)
    {

        $userid = $request->param('userid');

        $hot_goods = Db::name('goods')->where('is_hot', 1)->where('is_on_sale', 1)->where('user_id', $userid)->order('goods_id DESC')->limit(20)->cache(true, TPSHOP_CACHE_TIME)->select();
        Response::create(['data' => $hot_goods, 'code' => '2000', 'message' => '获取信息成功'], 'json')->header($this->header)->send();

    }


    //获取用户下面所有的产品
    public function getProductsById(Request $request)
    {
        $id = $request->param('id');
        $hot_goods = Db::name('goods')->where('is_hot', 1)->where('is_on_sale', 1)->where('id', $id)->order('goods_id DESC')->limit(20)->cache(true, TPSHOP_CACHE_TIME)->select();
        Response::create(['data' => $hot_goods, 'code' => '2000', 'message' => '获取信息成功'], 'json')->header($this->header)->send();
    }

    public function indexprolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 4;
        $cat_id = $request->param('cat_id')??0;
        $page = $request->param('page') > 1 ? (int)$request->param('page') : 1;
        $selectdata = $ProductServerobj->getProducts($limit, (int)$cat_id, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }


    //获取出价列表
    public function getBondlists(Request $request)
    {
        $goods_id = (int)$request->param('goods_id');
        if ($goods_id) {
            $ProductServerobj = new ProductServer();
            $config['page'] = (int)$request->param('page');
            $bidlists = $ProductServerobj->getBondlists($goods_id, 4, false, $config);
            //判断是否到最后一页了
            $flag = '0';
            if (isset($bidlists['total'])) {
                $totalpage = ceil($bidlists['total'] / $bidlists['per_page']);


                if ($bidlists['current_page'] < $totalpage || $config['page'] == 1) {
                    //说明还有页数
                    $flag = '1';
                }
                if ($bidlists['total'] < $bidlists['per_page'] || $config['page'] == 1) {
                    $flag = '1';
                }
            }
            if ($config['page'] == 1) {
                $flag = '1';
            }

            Response::create(['status' => '200', 'code' => '1', 'message' => '产品列表成功', 'data' => ['surplus' => $flag, 'goods_id' => (string)$goods_id, 'plists' => $bidlists??[]]], 'json')->header($this->header)->send();
        }
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品列表成功', 'data' => ['surplus' => '0', 'goods_id' => '0', 'plists' => []]], 'json')->header($this->header)->send();
    }

    public function indexpaimaiprolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $configdata = Config::get('rpc');
        $golangurl = $configdata['golang']['url'];
        $client = Client::create($golangurl, false);


        var_dump($client->hello("world"));
        //var_dump($client->sum(1, 2, 3));
        $limit = 5;

        exit;

        $user_id = (int)$request->param('user_id');
        $goods_id = (int)$request->param('goods_id');
        $type = (int)$request->param('type');
        $page = (int)$request->param('page');
        $self_user_id = $this->user_data['user_id'];
        $selectdata = $ProductServerobj->getProductsForpaimaiquan($limit, $user_id, $type, $self_user_id, $goods_id, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }

    public function indexpaimaiprolistmessage(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 5;

        $user_id = (int)$request->param('user_id');
        $goods_id = (int)$request->param('goods_id');
        $type = (int)$request->param('type');
        $page = (int)$request->param('page');
        $self_user_id = $this->user_data['user_id'];
        $selectdata = $ProductServerobj->getProductsForpaimaiquanm($limit, $user_id, $type, $self_user_id, $goods_id, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }

    public function indexpaimaiprolistCase(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 5;
        $goods_id = (int)$request->param('goods_id');
        $selectdata = $ProductServerobj->getProductsForpaimaiquan($limit, 0, 0, 1, $goods_id, 1);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }


    /**
     *  商品区列表
     */
    public function ajaxGoodsList(Request $request)
    {

        $where = ' 1 = 1 '; // 搜索条件
        I('intro') && $where = "$where and " . I('intro') . " = 1";
        I('brand_id') && $where = "$where and brand_id = " . I('brand_id');
        (I('is_on_sale') !== '') && $where = "$where and is_on_sale = " . I('is_on_sale');
        $cat_id = I('cat_id');
        // 关键词搜索
        $key_word = I('key_word') ? trim(I('key_word')) : '';
        if ($key_word) {
            $where = "$where and (goods_name like '%$key_word%' or goods_sn like '%$key_word%')";
        }

        if ($cat_id > 0) {
            $grandson_ids = getCatGrandson($cat_id);
            $where .= " and cat_id in(" . implode(',', $grandson_ids) . ") "; // 初始化搜索条件
        }


        $model = M('Goods');
        $count = $model->where($where)->count();
        $Page = new AjaxPage($count, 10);
        /**  搜索条件下 分页赋值
         * foreach($condition as $key=>$val) {
         * $Page->parameter[$key]   =   urlencode($val);
         * }
         */
        $show = $Page->show();
        $order_str = "`{$_POST['orderby1']}` {$_POST['orderby2']}";
        $goodsList = $model->where($where)->order($order_str)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $catList = D('goods_category')->select();
        $catList = convert_arr_key($catList, 'id');
        $this->assign('catList', $catList);
        $this->assign('goodsList', $goodsList);
        $this->assign('page', $show);// 赋值分页输出
        return $this->fetch();
    }


    //获取用户下面所有的产品
    public function getProductById(Request $request)
    {
        $good_id = $request->param('good_id');
        $ProductServer = new     ProductServer();
        $data = $ProductServer->getProductById($good_id);
        if (!empty($data)) {
            Response::create(['data' => $data, 'code' => '2000', 'message' => '获取作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['data' => $data, 'code' => '4000', 'message' => '获取作品信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
    }


    //获取用户下面所有的产品
    public function getUserAll(Request $request)
    {
        $u_id = (int)$request->param('u_id');
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_infoAll($u_id); // 获取用户信息
        $userid = $this->user_data['user_id'];
        //判断改用户是否关注了此用户
        $UserServer = new UserServer();
        if ($userid > 0 && $u_id > 0) {
            $flag = $UserServer->queryUserFocus($userid, $request->except('token'));
            if ($flag === true) {
                //已经关注
                $flag = '1';
            } else {
                $flag = '0';
            }
            $ProductServer = new     ProductServer();
            $data = $ProductServer->getProductsByUserid($u_id, 4);

            if (isset($user_info['result']['result'])) {
                Response::create(['product' => $data, 'isfocus' => $flag, 'user_info' => $user_info['result']['result'], 'code' => '2000', 'message' => '获取用户官网信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
            } else {
                Response::create(['code' => '4000', 'message' => '获取用户官网信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
            }
        }

        Response::create(['code' => '4000', 'message' => '获取用户官网信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }

    //获取今日新增加作品
    public function uploadProduct(Request $request)
    {
        $ProductServer = new  ProductServer();
        $data = $ProductServer->getProductds();

        Response::create(['data' => $data, 'code' => '2000', 'message' => '获取用户官网信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
    }

    //粉丝注册时候分配触发
    public function fansAssign(Request $request)
    {
        try {
            $JobQeueobj = new  JobQeue();
            $user_id = $request->param('user_id');
            $JobQeueobj->autoForFansererVhostAll($user_id);
            $data = 'ok';
        } catch (\RuntimeException $e) {
            Log::record('日志信息', 'info' . $e);
            $data = 'fail';
        }
        Response::create(['data' => $data, 'code' => '2000', 'message' => '获取用户官网信息' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
    }

    //精品
    public function indexjinpinprolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 5;
        $user_id = (int)$request->param('user_id');
        $goods_id = (int)$request->param('goods_id');
        $type = (int)$request->param('type');
        $page = (int)$request->param('page');
        $self_user_id = $this->user_data['user_id'];
        $selectdata = $ProductServerobj->getProductsForJinpin($limit, $user_id, $type, $self_user_id, $goods_id, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }

    //精品详情页面
    public function indexprolistofjinpin(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 1;
        $goods_id = $request->param('goods_id')??0;
        $page = $request->param('page') > 1 ? (int)$request->param('page') : 1;
        $selectdata = $ProductServerobj->getProductsOne($limit, (int)$goods_id, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }


    //随机获取一个作品
    public function productrandone(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $page = $request->param('page') > 1 ? (int)$request->param('page') : 1;
        $userid = $this->user_data['user_id'];

        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($userid);
        if (isset($user_info['result']['level']) && $user_info['result']['level'] > 0) {
            $memberlevel = $user_info['result']['level'];
            $memberSharkedCount = Hook::exec('app\\dataapi\\behavior\\MemberLimit', 'sharkedMemberTimes', $memberlevel);
            $selectdata = $ProductServerobj->getProductRandOne(1, $page, $memberSharkedCount, $userid);
            Response::create(['status' => '200', 'code' => '1', 'message' => '产品获取成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
        } else {
            Response::create(['status' => '4000', 'code' => '1', 'message' => '用户没有获取成功', 'plists' => []], 'json')->header($this->header)->send();
        }


    }

    //获取推广的产品 默认获取条
    public function indexjExtensionprolist(Request $request)
    {
        $ProductServerobj = new ProductServer();
        $limit = 3;
        $type = (int)$request->param('type');
        $page = (int)$request->param('page');
        $selectdata = $ProductServerobj->getProductsForExtension($limit, $type, $page);
        Response::create(['status' => '200', 'code' => '1', 'message' => '产品刘表成功', 'plists' => $selectdata], 'json')->header($this->header)->send();
    }


}
