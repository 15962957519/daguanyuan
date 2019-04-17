<?php
namespace Admin\Controller;
use Think\AjaxPage;
use Think\Model;
use Think\Page;

class CheckWorkerController extends BaseController {

    public function index(){
        $this->display();
    }
    //员工信息详情页
    public function detail(){
        $id = I('id');   //msg_id
        $res = M('check_worker')->where(array('id'=>$id))->find();
        if(!$res){
            exit($this->error('不存在该员工'));
        }
        if(IS_POST){
            $data = I('post.');
            $data['phone'] = trim($data['phone']);
            $row =  M('check_worker')->save($data);
            if($row){
                $this->success('修改成功',U("CheckWorker/index"));
            }else{
                $this->error('修改失败');
            }
            exit;

        }

        $this->assign('question',$res);
        $this->display();
    }
   //员工信息添加
    public function add(){
        if(IS_POST){
            $data = I('post.');
            $data['phone'] = trim($data['phone']);
            $data['addtime'] = time();
            $row =  M('check_worker')->add($data);
            if($row){
                $this->success('添加成功',U("CheckWorker/index"));
            }else{
                $this->error('添加失败');
            }
            exit;

        }
        $this->display();
    }
    //删除操作
    public function del(){
        $id = I('get.id');
        $row = M('check_worker')->where(array('id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function ajaxindex(){
        $model = M('check_worker');
        $userName = I('username','','trim');
        $phone = I('phone');
        $workNumber = I('worknumber');
        //dump(I('post.'));exit;
        $where='1 = 1';
        if($userName){
            $where .= " AND username='$userName'";
        }
        if($phone){
            $where .= " AND phone like '%{$phone}%'";
        }
        if($workNumber){
            $where .= " AND worknumber like '%{$workNumber}%'";
        }
        $count = $model->where($where)->count();
        $Page  = new AjaxPage($count,10);
        $show = $Page->show();
        $worklist = $model->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('worklist',$worklist);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
}
