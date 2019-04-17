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
 * Date: 2015-09-09
 */

namespace Admin\Controller;

use Think\AjaxPage;
use Think\Page;
use Admin\Logic\UsersLogic;
use Admin\Logic\Tixian;
use Admin\Logic\AliyunOss;

class UserController extends BaseController {

    public function index(){
        $store_level = M('store_level')->order('store_level_id')->select();
        $this->assign('store_level',$store_level);

        $this->display();
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        I('mobile') ? $condition['mobile'] = I('mobile') : false;
        I('nickname') ? $condition['nickname'] = I('nickname') : false;
        I('store_level') ? $condition['store_level'] = I('store_level') : false;
        if(I('is_authentication') == 1){
            $condition['is_authentication'] = 1;  //已认证
        }else if(I('is_authentication') == 2){
            $condition['is_authentication'] = 0;  //未认证
        }
        if(I('fictitious') == 1){
            $condition['fictitious'] = 0;  //正常
        }else if(I('fictitious') == 2){
            $condition['fictitious'] = 1;  //虚拟
        }

        I('is_goodstore') ? $condition['is_goodstore'] = I('is_goodstore') : false;

        if(I('reg_time')){
            $start = strtotime( I('reg_time') ); //月初时间戳
            //$start_time = date( 'Y-m-1 00:00:00', $timestamp );
            $mdays = date( 't', $start );
            $end_time = date( 'Y-m-' . $mdays . ' 23:59:59', $start );
            $end_time = strtotime($end_time);  //月末时间戳

            $condition['reg_time'] = ['between',"$start,$end_time"];
        }

        I('user_id') ? $condition['user_id'] = I('user_id') : false;
        $sort_order = I('order_by','user_id').' '.I('sort','desc');
        $condition['delete_time'] = array('exp', 'IS NULL');
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,20);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        if($condition['nickname'] || $condition['mobile']){
            $userList = $model->where($condition)->order('user_id asc')->limit(1)->select();
        }else{
            $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();
        }

        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('user_level',M('user_level')->getField('level_id,level_name'));   //会员等级
        $this->assign('store_level',M('store_level')->getField('store_level_id,store_name'));   //会员店铺等级

        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /**
     * 会员详细信息查看
     */
    public function detail(){
        $uid = I('get.id');
        $user = D('users')->where(array('user_id'=>$uid))->find();
        if(!$user)
            exit($this->error('会员不存在'));
        if(IS_POST){
            //  会员信息编辑
            $password = I('post.password');
            $password2 = I('post.password2');
            if($password != '' && $password != $password2){
                exit($this->error('两次输入密码不同'));
            }
            if($password == '' && $password2 == ''){
                unset($_POST['password']);
            }else{
                $_POST['password'] = encrypt($_POST['password']);
            }
            if($user['store_level'] != $_POST['store_level']){    //当修改店铺等级时 更改is_give_freefans_over = 0
                $_POST['is_give_freefans_over'] = 0;
            }

            $row = M('users')->where(array('user_id'=>$uid))->save($_POST);
            if($row)
                exit($this->success('修改成功'));
            exit($this->error('未作内容修改或修改失败'));
        }
        
        $user['first_lower'] = M('users')->where("first_leader = {$user['user_id']}")->count();

        $store_level=M('store_level')->field('store_level_id,store_name')->select(); //查询店铺等级
        $user_level=M('user_level')->field('level_id,level_name')->select(); //查询会员等级

        $this->assign('store_level',$store_level);
        $this->assign('user_level',$user_level);
        $this->assign('user',$user);
        $this->display();
    }
    
    public function add_user(){
    	if(IS_POST){
    		$data = I('post.');
			$user_obj = new UsersLogic();
			$res = $user_obj->addUser($data);
			if($res['status'] == 1){
				$this->success('添加成功',U('User/index'));exit;
			}else{
				$this->error('添加失败,'.$res['msg'],U('User/index'));
			}
    	}
    	$this->display();
    }

    /**
     * 用户收货地址查看
     */
    public function address(){
        $uid = I('get.id');
        $lists = D('user_address')->where(array('user_id'=>$uid))->select();
        $regionList = M('Region')->getField('id,name');
        $this->assign('regionList',$regionList);
        $this->assign('lists',$lists);
        $this->display();
    }

    /**
     * 删除会员
     */
    public function delete(){
        $uid = I('get.id');
        $data['delete_time'] = time();
        $row = M('users')->where(array('user_id'=>$uid))->save(['delete_time'=>time()]);
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 账户资金记录
     */
    public function account_log(){
        $user_id = I('get.id');
        //获取类型
        $type = I('get.type');
        //获取记录总数
        $count = M('account_log')->where(array('user_id'=>$user_id))->count();
        $page = new Page($count);
        $lists  = M('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('user_id',$user_id);
        $this->assign('page',$page->show());
        $this->assign('lists',$lists);
        $this->display();
    }

    /**
     * 账户资金调节
     */
    public function account_edit(){
        $user_id = I('get.id');
        if(!$user_id > 0)
            $this->error("参数有误");
        if(IS_POST){
            //获取操作类型
            $m_op_type = I('post.money_act_type');
            $user_money = I('post.user_money');
            $user_money =  $m_op_type ? $user_money : 0-$user_money;

            $p_op_type = I('post.point_act_type');
            $pay_points = I('post.pay_points');
            $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;

            $f_op_type = I('post.frozen_act_type');
            $frozen_money = I('post.frozen_money');
            $frozen_money =  $f_op_type ? $frozen_money : 0-$frozen_money;

            $desc = I('post.desc');
            if(!$desc)
                $this->error("请填写操作说明");
            if(accountLog($user_id,$user_money,$pay_points,$desc)){
                $this->success("操作成功",U("Admin/User/account_log",array('id'=>$user_id)));
            }else{
                $this->error("操作失败");
            }
            exit;
        }
        $this->assign('user_id',$user_id);
        $this->display();
    }
    //充值记录（只查询已成功的）
    public function recharge(){
    	$timegap = I('timegap');
    	$nickname = I('nickname');
    	$status = I('status');
    	$map = array();
    	if($timegap){
    		$gap = explode(' - ', $timegap);
    		$begin = $gap[0];
    		$end = $gap[1];
    		$map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
    	}
    	if($nickname){
    		$map['nickname'] = array('like',"%$nickname%");
    	}
    	if($status){
            $map['status'] = $status;   //支付类别  1：充值；2：认证押金；3：店铺升级
        }
    	$count = M('recharge')->where($map)->where(['pay_status'=>1])->count();
    	$page = new Page($count);
    	$lists  = M('recharge')->where($map)->where(['pay_status'=>1])->order('ctime desc')->limit($page->firstRow.','.$page->listRows)->select();
    	$this->assign('page',$page->show());
    	$this->assign('lists',$lists);
    	$this->display();
    }
    
    public function level(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$level_id = I('GET.level_id');
    	$level_info = array();
    	if($level_id){
    		$level_info = D('user_level')->where('level_id='.$level_id)->find();
    		$this->assign('info',$level_info);
    	}
    	$this->display();
    }
    
    public function levelList(){
    	$Ad =  M('user_level');
    	$res = $Ad->where('1=1')->order('level_id')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
    	$count = $Ad->where('1=1')->count();
    	$Page = new \Think\Page($count,10);
    	$show = $Page->show();
    	$this->assign('page',$show);
    	$this->display();
    }
    
    public function levelHandle(){
    	$data = I('post.');
    	if($data['act'] == 'add'){
    		$r = D('user_level')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('user_level')->where('level_id='.$data['level_id'])->save($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = D('user_level')->where('level_id='.$data['level_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r){
    		$this->success("操作成功",U('Admin/User/levelList'));
    	}else{
    		$this->error("操作失败",U('Admin/User/levelList'));
    	}
    }

    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(I('search_key'));        
        if(strstr($search_key,'@'))    
        {
            $list = M('users')->where(" email like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }                        
        }
        else
        {
            $list = M('users')->where(" mobile like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }            
        } 
        exit;
    }
    
    /**
     * 分销树状关系
     */
    public function ajax_distribut_tree()
    {
          $list = M('users')->where("first_leader = 1")->select();
          $this->display();
    }

    /**
     *
     * @time 2016/08/31
     * @author dyr
     * 发送站内信
     */
    public function sendMessage()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $users = M('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
        }
        $this->assign('users',$users);
        $this->display();
    }

    /**
     * 发送系统消息
     * @author dyr
     * @time  2016/09/01
     */
    public function doSendMessage()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $type = I('post.type', 0);//个体or全体 默认个体0
        $admin_id = session('admin_id');    //后台登陆者id
        $users = I('post.user');//个体id    //获取到的用户id
        $message = array(
            'admin_id' => $admin_id,
            'message' => $message,
            'category' => 0,
            'send_time' => time()
        );
        if ($type == 1) {
            //全体用户系统消息
            $message['type'] = 1;
            M('Message')->data($message)->add();
        } else {
            //个体消息
            $message['type'] = 0;
            if (!empty($users)) {
                $create_message_id = M('Message')->data($message)->add();
                foreach ($users as $key) {
                    M('user_message')->data(array('user_id' => $key, 'message_id' => $create_message_id, 'status' => 0, 'category' => 0))->add();
                }
            }
        }
        echo "<script>parent.{$call_back}(1);</script>";
        exit();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 发送邮件
     */
    public function sendMail()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array),
                'email' => array('neq', '')
            );
            $users = M('users')->field('user_id,nickname,email')->where($user_where)->select();
        }
        $this->assign('smtp', tpCache('smtp'));
        $this->assign('users', $users);
        $this->display();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 身份认证
     */
    public function personIdentify($user_id='')
    {
        $personCard=M('user_verifty')->where("user_id=$user_id")->find();
        if(empty($personCard)){
            echo '未上传身份身份相关信息！';exit;
        }
        if($personCard){
            $oss = new AliyunOss(true);
            if($personCard['verifyidcodefront'] != ''){
                $res_id1 = $personCard['verifyidcodefront'];
                $personCard['verifyidcodefront'] = $oss->getnomatergetCurlofimgverfity(C('aliyun.bucket'), $res_id1, 31536000);
            }

            if($personCard['verifyidcodeback'] != ''){
                $res_id2 = $personCard['verifyidcodeback'];
                $personCard['verifyidcodeback'] = $oss->getnomatergetCurlofimgverfity(C('aliyun.bucket'), $res_id2, 31536000);
            }

        }else{
            $personCard = '';
        }
        $this->assign('personCard', $personCard);
        $this->display();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 通过
     */
    public function identify_pass()
    {   
        $id = $_POST['id'];

        $re=M('user_verifty')->where(array('id'=>$id))->save(array('status'=>2));  //更新通过状态
        $currentmsg = M('user_verifty')->where(array('id'=>$id))->find();
        M('users')->where(['user_id'=>$currentmsg['user_id']])->save(['is_authentication'=>1]);   //通过时更改uesr表里的认证通过状态，店铺等级
        if($re){
            echo $id;
        }
        
                
    }
    //不通过
    public function identify_nopass()
    {   
        $id = $_POST['id'];
        $re=M('user_verifty')->where(array('id'=>$id))->save(array('status'=>3)); //更新不通过状态
        if($re){
            echo $id;
        }
    }
    //取消
    public function identify_cancel(){
        $id = $_POST['id'];
        $usermsg = M('user_verifty')->where(['id'=>$id])->find();
        M('users')->where(['user_id'=>$usermsg['user_id']])->save(['store_level'=>1]);
        $re=M('user_verifty')->where(['id'=>$id])->save(['is_pay'=>0,'status'=>1]);
        if($re){
            echo $id;
        }
    }
    //押金是否已交
    public function identify_is_pay(){
        $id = I('id');
        if(I('is_pay') == 1){
             $re=M('user_verifty')->where(['id'=>$id])->save(['is_pay'=>0]);     //变更成 押金未交
        }else{
            $re=M('user_verifty')->where(['id'=>$id])->save(['is_pay'=>1]);     //变更成 押金已交
        }
        if($re){
            echo $id;
        }
    }

    //店铺升级
    public function setstore($user_id=''){
        $setstore = M('users')->field('user_id,store_level,store_identify,store_update_time,store_update_content,store_update_money')->where(['user_id'=>$user_id])->find();
        $store_level = M('store_level')->field('store_level_id,store_name')->select();
        $this->assign('setstore', $setstore);
        $this->assign('store_level', $store_level);
        $this->display();
    }
    //店铺升级 执行
    public function dosetstore(){
        $user_id = I('user_id');
        $store_level = I('store_level');
        $store_update_content = I('store_update_content');   //更新内容
        $store_update_money = I('store_update_money');      //更新金额
        $store_identify = I('store_identify');      //更新备注
        $re = M('users')->where(['user_id'=>$user_id])
            ->save([
                'is_give_freefans_over' => 0,
                'store_level'=>$store_level,
                'store_update_content'=>$store_update_content,
                'store_update_money'=>$store_update_money,
                'store_identify'=>$store_identify
            ]);
        if($re){
            echo $user_id;
        }

    }

    /**
     * 发送邮箱
     * @author dyr
     * @time  2016/09/03
     */
    public function doSendMail()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $title = I('post.title');//标题
        $users = I('post.user');
        if (!empty($users)) {
            $user_id_array = implode(',', $users);
            $users = M('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
            $to = array();
            foreach ($users as $user) {
                if (check_email($user['email'])) {
                    $to[] = $user['email'];
                }
            }
            $res = send_email($to, $title, $message);
            echo "<script>parent.{$call_back}({$res});</script>";
            exit();
        }
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 匹配粉丝
     */
    public function pipeiFans()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array)
            );
            $users = M('users')->field('user_id,nickname')->where($user_where)->select();
        }
        $this->assign('users', $users);
        $this->display();
    }

    /**
     * 粉丝匹配
     * @author dyr
     * @time  2016/09/03
     */
    public function setFans()
    {
        $call_back = I('call_back');//回调方法
        $nums = I('post.nums');//粉丝数量
        $users = I('post.user');
        foreach($users as $k=>$val){
            $mycare = M('fans_collect')->field('user_id')->where(['fans_id'=>$val])->select();  //我的关注
            $mycares = array_column($mycare,'user_id');
            $fans_id = M('users')->field('user_id')->where('user_id','not in',$mycares)->where('user_id','<>',$val)->limit($nums)->order('user_level desc')->select();
            if(count($fans_id) < $nums){
                return '粉丝数量不足！';
            }
            foreach($fans_id as $kk=>$vv){
                M('fans_collect')->data(array('user_id' => $val, 'fans_id' => $vv, 'add_time' => time()))->add();
            }
            echo "<script>parent.{$call_back}(1);</script>";
            exit();
        }
    }
    /**
     * 提现申请记录
     */
    public function withdrawals()
    {
        $model = M("withdrawals");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);

        $status = I('status');
        $user_id = I('user_id');
        $account_bank = I('account_bank');
        $account_name = I('account_name');
        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where = " create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";

        if($status === '0' || $status > 0)
            $where .= " and status = $status ";
        $user_id && $where .= " and user_id = $user_id ";
        $account_bank && $where .= " and account_bank like '%$account_bank%' ";
        $account_name && $where .= " and account_name like '%$account_name%' ";

        $count = $model->where($where)->count();
        $Page  = new Page($count,16);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('create_time',$create_time);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        C('TOKEN_ON',false);
        $this->display();
    }
    /**
     * 删除申请记录
     */
    public function delWithdrawals()
    {
        $model = M("withdrawals");
        $model->where('id ='.$_GET['id'])->delete();
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn(json_encode($return_arr));
    }

    /**
     * 修改编辑 申请提现
     */
    public  function editWithdrawals(){
        $id = I('id');
        $model = M("withdrawals");
        $withdrawals = $model->find($id);
        $user = M('users')->where("user_id = {$withdrawals[user_id]}")->find();

        if(IS_POST)
        {
            $model->create();

            // 如果是已经给用户转账 则生成转账流水记录
            if($model->status == 1 && $withdrawals['status'] != 1)
            {
                if($user['user_money'] < $withdrawals['money'])
                {
                    $this->error("用户余额不足{$withdrawals['money']}，不够提现");
                    exit;
                }
                //========================提现账户变动=================================
                $third_users = M('third_users')->where("user_id = {$withdrawals[user_id]}")->field('openid')->find();
                $openid = $third_users['openid'];  //用户openid
                $money = $withdrawals['money'];    //提现金额
                $tixian = new Tixian();
                $tixian_result = $tixian->actionAct_tixian($openid,$money);
                if($tixian_result->return_code=='SUCCESS' && $tixian_result->result_code=='SUCCESS'){
                    accountLog($withdrawals['user_id'], ($withdrawals['money'] * -1), 0,"平台提现");
                    $remittance = array(
                        'user_id' => $withdrawals['user_id'],
                        'bank_name' => $withdrawals['bank_name'],
                        'account_bank' => $withdrawals['account_bank'],
                        'account_name' => $withdrawals['account_name'],
                        'money' => $withdrawals['money'],
                        'status' => 1,
                        'create_time' => time(),
                        'admin_id' => session('admin_id'),
                        'withdrawals_id' => $withdrawals['id'],
                        'remark'=>$model->remark,
                    );
                    M('remittance')->add($remittance);
                }else{
                    $this->error("用户提现操作失败！！");
                }

                //===========================================================
            }
            $model->save();
            $this->success("操作成功!",U('Admin/User/remittance'),3);
            exit;
        }



        if($user['nickname'])
            $withdrawals['user_name'] = $user['nickname'];
        elseif($user['email'])
            $withdrawals['user_name'] = $user['email'];
        elseif($user['mobile'])
            $withdrawals['user_name'] = $user['mobile'];

        $this->assign('user',$user);
        $this->assign('data',$withdrawals);
        $this->display();
    }
    /**
     *  转账汇款记录
     */
    public function remittance(){
        $model = M("remittance");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);

        $status = I('status');
        $user_id = I('user_id');
        $account_bank = I('account_bank');
        $account_name = I('account_name');

        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where = " create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";
        $user_id && $where .= " and user_id = $user_id ";
        $account_bank && $where .= " and account_bank like '%$account_bank%' ";
        $account_name && $where .= " and account_name like '%$account_name%' ";

        $count = $model->where($where)->count();
        $Page  = new Page($count,16);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('create_time',$create_time);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        C('TOKEN_ON',false);
        $this->display();
    }
}