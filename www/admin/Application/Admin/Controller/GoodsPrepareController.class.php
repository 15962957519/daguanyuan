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
 */
namespace Admin\Controller;
use Admin\Logic\GoodsLogic;
use Think\AjaxPage;
use Think\Page;

class GoodsPrepareController extends BaseController {

    /**
     * 首页商品区信息抓取
     */
    public function goodsCollect(){
        if (IS_POST) {
            //echo WWWROOT ;exit;
            $main_url = trim($_POST['url']);
            $type = trim($_POST['type']);
            $start_page = trim($_POST['start_page']);
            $end_page = trim($_POST['end_page']);
            set_time_limit(0);
            Vendor('phpQuery.phpQuery');
            $data = $child = [];

            //循环分页采集所有列表（标题名+详情页链接）
            for($i=$start_page;$i<=$end_page;$i++){
                \phpQuery::newDocumentFile($main_url.'&page='.$i);
                $artlist = pq(".pmp-item-list li.item");

                foreach($artlist as $li){
                    $child['url'] = pq($li)->find('a.img-wrap')->attr('href');
                    $child['url'] = 'https://item-paimai.taobao.com/pmp_item/'.basename($child['url']);

                    $child['goods_name'] = pq($li)->find('p.title')->text();
                    $child['goods_name'] = mb_convert_encoding($child['goods_name'],'ISO-8859-1','utf-8');
                    $child['goods_name'] = mb_convert_encoding($child['goods_name'],'utf-8','GBK');
                    $data[] = $child;
                }
            }

            //输出结果
//            echo '<pre>';
//            var_dump($data);exit;

            //循环结果，处理详情页
            $http_type="https:";
            if(count($data) > 0) {
                foreach($data as &$vo){
                    $imgs = [];
                    \phpQuery::newDocumentFile($vo['url']);

                    //原始图片
                    $original_img = pq("#multi")->attr('data-ks-imagezoom');
                    $vo['original_img'] = $http_type.$original_img;

                    //起拍价
                    $start_price = pq(".pm-attachment ul:first")->find('li.line1 span:last')->text();
                    $vo['start_price'] = $start_price;

                    //每次加价
                    $every_add_price = pq(".pm-attachment ul:first")->find('li.line2 span:last')->text();
                    $vo['every_add_price'] = $every_add_price;

                    //简单描述
                    $goods_remark = pq(".basic-info li")->text();
                    $vo['goods_remark'] = $goods_remark;

                    //图片组图
                    $p_list = pq("#J_UlThumb li");
                    foreach($p_list as $v){
                        $image = pq($v)->find('img')->attr('data-ks-imagezoom');
                        $imgs[] = $http_type.$image;

                    }
                    $vo['imgs'] = $imgs;
                }
            }

            /*
             *写入数据库
             */

            //循环写入数据
            $count_ok = $count_err = 0;
            foreach($data as $key=>&$item){
                $item['original_img'] = $this->GrabImage($item['original_img'], "");

                //序列化组图
                if(count($item['imgs']) > 0){
                    for($i=0;$i<count($item['imgs']);$i++){
                        $item['imgs'][$i] = $this->GrabImage($item['imgs'][$i], "");
                    }
                    $item['imgs'] = serialize($item['imgs']);
                }else{
                    $item['imgs'] = '';
                }
                $sql_data = [];
                $sql_data['goods_name'] = $item['goods_name'];
                $sql_data['start_price'] = $item['start_price'];
                $sql_data['every_add_price'] = $item['every_add_price'];
                $sql_data['goods_remark'] = $item['goods_remark'];
                $sql_data['original_img'] = $item['original_img'];
                $sql_data['imgs'] = $item['imgs'];
                $sql_data['type'] = $type;
                $sql_data['addtime'] = time();

                $result = M('goods_prepare')->add($sql_data);

                if($result){
                    $count_ok++;
                }else{
                    $count_err++;
                }
                $this->addLog($msg."\r\n");//写入Log
            }
            $this->success('共计：'.count($data).'条，成功抓取：'.$count_ok.'条，'.$count_err.'条抓取失败！', '/Admin/GoodsPrepare/goodsList');
        }else{
            $this->display();
        }
    }


    /**
     * 首页预备商品区列表
     */
    public function goodsList(){
        //接收帅选参数
        $cat_id     = I('get.cat_id','');//类型
        $status     = I('get.status',0);//是否推送
        $goods_name = I('get.goods_name');//商品区名称

        //初始化条件
        $condition = $params = [];

        //默认条件
        $condition['del'] = 0;

        if ($cat_id > 0){
            $condition['type'] = $cat_id;
            $params['cat_id']  = $cat_id;
        }

        $condition['status'] = $status;
        $params['status'] = $status;

        if (strlen($goods_name) > 0){
            $condition['goods_name'] = array('like',"%".trim($goods_name)."%");
            $params['goods_name']   = $goods_name;
        }

        $this->params = $params;

        $this->categoryList = getGoodsPrepareType();

        $goods_prepare = M('goods_prepare');
        $count      = $goods_prepare->where($condition)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $this->page = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->goodsList = $goods_prepare->where($condition)->order('addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        //获取线上所有商品区类型
        $this->goodsTypeList =  M("GoodsCategory")->where(array('parent_id' => '0', 'is_show' => 1))->order("id desc")->select();

        $this->display();        
    }

    /**
     * 商品区编辑
     */
    public function goodsEdit(){
        $GoodsPrepare = D('GoodsPrepare');
        if (IS_POST) {
            $id = I('post.id',0);
            $data = $GoodsPrepare->create();

            if($id > 0){
                $result = $GoodsPrepare->where(array('id' => $id))->save($data);
            }else{
                $result = $GoodsPrepare->add($data);
            }

            if($result){
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('操作成功', '/Admin/GoodsPrepare/goodsList');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('操作失败');
            }
        }else{
            $id = I('get.id',0);
            $info = [];
            if($id > 0){
                //查询出详情
                $info = $GoodsPrepare->where(array('id' => $id))->find();
            }

            $this->assign('info',$info);
            $this->display();
        }
    }

    /**
     *  根据id获取采集商品区信息
     */
    public function getInfoById()
    {
        $goods_id = (int)$_GET['id'];

        //获取该商品区信息
        $GoodsPrepare =D('GoodsPrepare');

        $res = $GoodsPrepare->where(array('id' => $goods_id))->find();//采集商品区表
        if($res){
            $return_arr = array('status' => 200,'msg' => '获取成功','data'  =>$res,);
            echo json_encode($return_arr);
        }else{
            $return_arr = array('status' => 4001,'msg' => '删除失败','data'  =>'',);
            echo json_encode($return_arr);
        }
    }

    /**
     * 删除商品区
     */
    public function goodsDel()
    {
        $goods_id = (int)$_GET['id'];
        $error = '';
        //echo $goods_id;exit;
        // 删除此商品区
        $GoodsPrepare =M('GoodsPrepare');
        $data['del'] = 1;
        $GoodsPrepare->where('id ='.$goods_id)->save($data);//商品区表
        if($GoodsPrepare){
            $return_arr = array('status' => 200,'msg' => '操作成功','data'  =>'',);
            echo json_encode($return_arr);
        }else{
            $return_arr = array('status' => 4001,'msg' => '删除失败','data'  =>'',);
            echo json_encode($return_arr);
        }
    }

    public function goodsUpload(){

        $typeArr = array("jpg", "png", "gif");//允许上传文件格式
        $path = "E:/img_upload/";//上传路径

        if (IS_POST) {
            echo '<pre>';
            var_dump($_POST);exit;
            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error"=>"您还未选择图片"));
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error"=>"清上传jpg,png或gif类型的图片！"));
                exit;
            }
            if ($size > (500 * 1024)) {
                echo json_encode(array("error"=>"图片大小已超过500KB！"));
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type;//图片名称
            $pic_url = $path . $pic_name;//上传后图片路径+名称
            if (move_uploaded_file($name_tmp, $pic_url)) { //临时文件转移到目标文件夹
                echo json_encode(array("error"=>"0","pic"=>$pic_url,"name"=>$pic_name));
            } else {
                echo json_encode(array("error"=>"上传有误，清检查服务器配置！"));
            }
        }else{
            $GoodsLogic = new GoodsLogic();
            $cat_list = $GoodsLogic->goods_cat_list();
            $this->assign('cat_list',$cat_list);
            $this->display();
        }
    }

    //获取远程图片下载到本地并重命名...
    public function GrabImage($url, $filename = "") {
        if ($url == ""):return false;
        endif;
        //如果$url地址为空，直接退出
        if ($filename == "") {
            //如果没有指定新的文件名
            $ext = strrchr($url, ".");
            //得到$url的图片格式
            if ($ext != ".gif" && $ext != ".jpg"):return false;
            endif;
            //如果图片格式不为.gif或者.jpg，直接退出
            $name = 'xianyu/'.date('Y-m-d',time()).'/'.date("dMYHis"). '_'.$this->get_rand_name().$ext;
            $filename = WWWROOT.'/Public/download/'.$name;
            //用天月面时分秒来命名新的文件名
        }

        //判断是否存在改目录，不存在则创建
        if(!file_exists(WWWROOT.'/Public/download/xianyu/'.date('Y-m-d',time()))){
            mkdir(WWWROOT.'/Public/download/xianyu/'.date('Y-m-d',time()),0777, true);
        }

        ob_start();//打开输出
        readfile($url);//输出图片文件
        $img = ob_get_contents();//得到浏览器输出
        ob_end_clean();//清除输出并关闭
        $size = strlen($img);//得到图片大小
        $fp2 = @fopen($filename, "a");
        fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
        fclose($fp2);
        return $name;//返回新的文件名
    }

    //随机名称
    public function get_rand_name(){
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    //写入log
    public function addLog($contents){
        $file_name = WWWROOT.'/Public/download/xianyu/xianyu_log/'.date('Y-m-d',time()).'_log.txt';
        file_put_contents($file_name, $contents,FILE_APPEND);
    }
}