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
 * 评论管理控制器
 * Date: 2015-10-20
 */

namespace Admin\Controller;


use Think\AjaxPage;
use Think\Model;
use Think\Page;

class FansController extends BaseController {


    public function index(){
        $this->display();
    }


    //删除操作
    public function del(){
        $id = I('get.id');
        $row = M('fans_collect')->where(array('msg_id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function ajaxindex(){
        $model = M('fans_collect');
        $user_id = I('user_id','','trim');
        $nickname = I('nickname','','trim');
        //var_dump($nickname);
        $where='1 = 1';
        if($user_id){
            $where .= " AND user_id='$user_id'";
        }
        if($nickname){
            $where .= " AND nickname like '%{$nickname}%'";
        }        
        $count = $model->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
                
        $fans_lists = $model->alias("F")->join("tp_users as U on F.user_id=U.user_id")->field("F.*,U.nickname")->where($where)->order('collect_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $fans_list=array();
        foreach($fans_lists as $k=>$v){
            $fans_id=$v['fans_id'];
            $val=M('users')->where(['user_id'=>$fans_id])->find();
            $v['nickname1']=$val['nickname'];
            $fans_list[]=$v;
        }

        $this->assign('fans_list',$fans_list);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
   
}
