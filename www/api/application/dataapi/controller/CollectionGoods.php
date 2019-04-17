<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use app\dataapi\server\AliyunOss;
use app\dataapi\model\Goods;
use app\dataapi\model\Users;
use app\dataapi\model\GoodsImages;
use app\dataapi\model\GoodsPrepare;
use think\Image;
use think\Config;

define("TOKEN", "jknsadjknasdjkasjkas88");
//商品区
class CollectionGoods    extends BaseApi
{

    public function index(Request $request)
    {
        $objDateTime = new \DateTime();
        $objDateTime->modify('+31536000 second');
        $userinfoobj['image_url_remote_expire'] = $objDateTime->getTimestamp();
        $callback = $request->param('callback');

        try {
            $goods_name = $request->param('pre_goods_name');
            $pre_goods_remark = $request->param('pre_goods_remark');

            //获取采集商品区信息
            $good_id = (int)$request->param('id');
            $GoodsPrepare = new GoodsPrepare();

            $collect_good = $GoodsPrepare->where(['id' => $good_id])->find();

            if(!$collect_good){
                $data =json_encode(['status' => '4001', 'message' => "id:".$good_id."的采集商品区不存在"]);
                echo $callback . '(' . $data .')';
            }

            //获取所有图片
            $images = [];
            $images[] = $collect_good['original_img'];

            if(strlen($collect_good['imgs']) > 0){
                foreach(unserialize($collect_good['imgs']) as $v){
                    $images[] = $v;
                }
            }

            $img_path = 'http://guwanadmin.tianbaoweipai.com/Public/download/';//图片目录
            $currenttime = date('Y-m-d-H-i');
            $rescourseiddata = [];
            $pre_ ='collect/';
            $objDateTimet = new \DateTime();
            $pre_name = $pre_ . $objDateTimet->format('Y-m-d') . '/';

            $aliyunosobj= new AliyunOss();

            $aliyunosobj->bucket =Config::get('aliyun.bucket');

            foreach ($images as $v) {
                if (!empty($v)) {
                    $img_url = $img_path.$v;
                    $img_data = file_get_contents($img_url);
                    $local_url = $tmp = $pre_name . $currenttime . '_img_' . uniqid();
                    $flag = $aliyunosobj->upload(Config::get('aliyun.bucket'), $tmp, $img_data);

                    $rescourseiddata[] = $local_url;
                }
            }

            //随即选择一个用户
            $Users = new Users();

            $user_info = $Users->where(array('fictitious' => 1))->order('rand()')->limit(1)->find();

            $rescourse_id = '';
            //保存到数据 资源id

            // 启动事务
            Db::startTrans();

            //将采集表中该商品区修改状态
            $GoodsPrepare = new GoodsPrepare();
            $data_collect = [];
            $data_collect['status'] = 1;
            $collect_res = $GoodsPrepare->allowField(true)->save($data_collect, ['id' => $good_id]);

            if (!$collect_res) {
                $data =json_encode([ 'code' => '4001', 'message' => '作品上传' . $request->param('actionname') . '失败']);
                echo $callback . '(' . $data .')';exit;
            }

            $goodsobj = new Goods();
            $goods_id = 0;
            $data_param = [];
            $data_param['cat_id'] = $request->param('goods_type');
            $data_param['endTime'] = strtotime($request->param('endtime'));
            $data_param['user_id'] = $user_info['user_id'];
            $data_param['is_toplatform'] = 1;
            $data_param['is_gernerorder'] = 0;
            $data_param['upload_time'] = $_SERVER['REQUEST_TIME'];
            $data_param['on_time'] = $_SERVER['REQUEST_TIME'];
            $data_param['shop_price'] = $request->param('pre_start_price');
            $data_param['start_price'] = $request->param('pre_start_price');//暂时为这个数字
            $data_param['every_add_price'] = 0;//暂时默认为0

            if (!empty($goods_name)) {
                $data_param['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $goods_name);
                $data_param['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $goods_name);
                $data_param['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $goods_name);
            }
            if (!empty($pre_goods_remark)) {
                $data_param['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $pre_goods_remark);
                $data_param['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $pre_goods_remark);
                $data_param['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $pre_goods_remark);
                $data_param['goods_content'] = ihtmlspecialchars($pre_goods_remark);
            }

            if ($goodsobj->allowField(true)->save($data_param) > 0) {
                $online_goods_id = $goodsobj->goods_id;
            }

            if ($online_goods_id > 0) {
                $list = [];
                $GoodsImagesobj = new     GoodsImages();
                foreach ($rescourseiddata as $v) {
                    $list[] = ['goods_id' => intval($online_goods_id), 'rescourse_id' => $v];
                }
                $img_list_res = $GoodsImagesobj->saveAll($list);
                // 提交事务
                Db::commit();
            }
            //发送30分钟到期提醒队列
            $data =json_encode(['userid' => $user_info['user_id'], 'code' => '200', 'message' => '作品上传' . $request->param('actionname') . '成功']);
            echo $callback . '(' . $data .')';

        }catch(\Exception $e){
//            echo '<pre>';
//            var_dump($e);exit;
            $data =json_encode(['status' => '200', 'code' => '4006', 'message' => "图片不能为空"]);
            echo $callback . '(' . $data .')';
        }

    }
}
