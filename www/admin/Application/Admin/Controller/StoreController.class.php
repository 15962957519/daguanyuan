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
 * Author: 当燃
 * Date: 2015-10-09
 */

namespace Admin\Controller;
use Admin\Logic\GoodsLogic;
class StoreController extends BaseController{

    /*
     * 配置入口
     */
    public function index()
    {
        /*配置列表*/
        $group_list = M('store_level')->order('store_level_id')->select();
        $this->assign('group_list',$group_list);

        $store_level_id=I('get.store_level_id');
        $store_level_id=isset($store_level_id)?$store_level_id:1;

        $this->assign('current_id',$store_level_id);

        $this->res_message=M('store_level')->find($store_level_id);

        $this->display();
    }

    /*
     * 新增修改配置
     */
    public function handle($store_level_id)
    {
        $param = I('post.');
        if( $_FILES['store_img']['tmp_name'] != ''){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =      './Public/upload/'; // 设置附件上传根目录
            // 上传单个文件
            $info   =   $upload->uploadOne($_FILES['store_img']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功 获取上传文件信息
                $param['store_img'] = '/upload/'.$info['savepath'].$info['savename'];
                $store_img = M('store_level')->where(['store_level_id'=>$store_level_id])->getField('store_img');
                $store_img = __PUBLIC__ . $store_img;
                if(file_exists($store_img)){
                    @unlink($store_img);
                }
            }
        }
        unset($param['inc_type']);
        if($param['store_img'] == ''){
            unset($param['store_img']);
        }

        $re=M('store_level')->where(['store_level_id'=>$store_level_id])->save($param);
        if($re){
            $this->success("操作成功");
        }else{
            $this->success("操作失败");
        }

    }


}