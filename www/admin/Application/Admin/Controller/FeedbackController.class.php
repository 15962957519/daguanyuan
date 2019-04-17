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

class FeedbackController extends BaseController {


    public function index(){
        $this->display();
    }

    //问题反馈详情页
    public function detail(){
        $id = I('get.id');   //msg_id
        $res = M('feedback')->where(array('msg_id'=>$id))->find();
        if(!$res){
            exit($this->error('不存在该问题'));
        }
        if(IS_POST){
            $add['msg_id'] = $id;
            $add['msg_content'] = I('post.msg_content');
            $add['user_name'] = I('post.user_name');
            $add['msg_status'] = I('post.msg_status');

            $row =  M('feedback')->save($add);
            if($row){
                $this->success('修改成功',U("Feedback/index"));
            }else{
                $this->error('修改失败');
            }
            exit;

        }

        $this->assign('question',$res);
        $this->display();
    }

    //问题反馈删除操作
    public function del(){
        $id = I('get.id');
        $row = M('feedback')->where(array('msg_id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function ajaxindex(){
        $model = M('feedback');
        $user_name = I('user_name','','trim');
        $msg_content = I('msg_content','','trim');
        $where='1 = 1';
        if($user_name){
            $where .= " AND user_name='$user_name'";
        }
        if($msg_content){
            $where .= " AND msg_content like '%{$msg_content}%'";
        }        
        $count = $model->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
                
        $feedback_list = $model->where($where)->order('msg_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('feedback_list',$feedback_list);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    










    
    public function ask_list(){
        $this->display();
    }
    
    public function ajax_ask_list(){
        $model = M('goods_consult');
        $username = I('nickname','','trim');
        $content = I('content','','trim');
        $where=' parent_id = 0';
        if($username){
            $where .= " AND username='$username'";
        }
        if($content){
            $where .= " AND content like '%{$content}%'";
        }
        $count = $model->where($where)->count();        
        $Page  = new AjaxPage($count,16);
        $show = $Page->show();              
        
        $comment_list = $model->where($where)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select(); 
        if(!empty($comment_list))
        {
            $goods_id_arr = get_arr_column($comment_list, 'goods_id');
            $goods_list = M('Goods')->where("goods_id in (".  implode(',', $goods_id_arr).")")->getField("goods_id,goods_name");
        }
        $consult_type = array(0=>'默认咨询',1=>'商品区咨询',2=>'支付咨询',3=>'配送',4=>'售后');
        $this->assign('consult_type',$consult_type);
        $this->assign('goods_list',$goods_list);
        $this->assign('comment_list',$comment_list);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    
    public function consult_info(){
        $id = I('get.id');
        $res = M('goods_consult')->where(array('id'=>$id))->find();
        if(!$res){
            exit($this->error('不存在该咨询'));
        }
        if(IS_POST){
            $add['parent_id'] = $id;
            $add['content'] = I('post.content');
            $add['goods_id'] = $res['goods_id'];
                $add['consult_type'] = $res['consult_type'];
            $add['add_time'] = time();          
            $add['is_show'] = 1;    
            $row =  M('goods_consult')->add($add);
            if($row){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
            exit;       
        }
        $reply = M('goods_consult')->where(array('parent_id'=>$id))->select(); // 咨询回复列表     
        $this->assign('comment',$res);
        $this->assign('reply',$reply);
        $this->display();
    }
    public function ask_handle(){
        $type = I('post.type');
        $selected_id = I('post.selected');        
        if(!in_array($type,array('del','show','hide')) || !$selected_id)
            $this->error('操作完成');
    
        $selected_id = implode(',',$selected_id);
        if($type == 'del'){
            //删除咨询
            $where .= "( id IN ({$selected_id}) OR parent_id IN ({$selected_id})) ";
            $row = M('goods_consult')->where($where)->delete();
        }
        if($type == 'show'){
            $row = M('goods_consult')->where("id IN ({$selected_id})")->save(array('is_show'=>1));
        }
        if($type == 'hide'){
            $row = M('goods_consult')->where("id IN ({$selected_id})")->save(array('is_show'=>0));
        }           
        $this->success('操作完成');
    }
}