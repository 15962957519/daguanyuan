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

class LotteryController extends BaseController {

    /**
     * 抽奖奖品列表
     */
    public function goodsList(){
        $lottery_type = I('get.lottery_type','');
        $lottery_name = trim(I('get.lottery_name','','htmlspecialchars'));

        $LotteryGifts = D('LotteryGifts');
        $condition = $params = [];
        if(!empty($lottery_type)){
            $condition['lottery_type'] = $lottery_type;
            $params['lottery_type'] = $lottery_type;
        }

        if(!empty($lottery_name)){
            $condition['lottery_name'] = array('like', '%'.$lottery_name.'%');
            $params['lottery_name'] = $lottery_name;
        }

        $count      = $LotteryGifts->where($condition)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $goodsList = $LotteryGifts->where($condition)->order('ifshow asc,addtime desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        //获取奖品类型
        $lotteryTypes = M('lottery_types');
        $categoryList = $lotteryTypes->where([])->order('addtime desc')->select();

        //获取每日抽奖消耗积分
        $jifen = M('lottery_set')->where(array('title' => 'spend_jifen'))->find();

        $this->assign('categoryList',$categoryList);
        $this->assign('params',$params);
        $this->assign('jifen',$jifen['content']);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('goodsList',$goodsList);
        $this->display();        
    }

    /**
     * 添加/编辑奖品
     */
    public function goodsAdd(){
        $LotteryGifts = D('LotteryGifts');
        if (IS_POST) {
            $lottery_id = I('post.lottery_id',0);
            $data = $LotteryGifts->create();

            if($lottery_id > 0){
                $result = $LotteryGifts->where(array('lottery_id' => $lottery_id))->save($data);
            }else{
                $result = $LotteryGifts->add($data);
            }

            if($result){
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('操作成功', '/Admin/Lottery/goodsList');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('操作失败');
            }
        }else{
            $lottery_id = I('get.lottery_id',0);
            $lottery_info = [];
            if($lottery_id > 0){
                //查询出奖品详情
                $lottery_info = $LotteryGifts->where(array('lottery_id' => $lottery_id))->find();
            }

            //获取奖品类型
            $lotteryTypes = M('lottery_types');
            $categoryList = $lotteryTypes->where([])->order('addtime desc')->select();

            $this->assign('categoryList',$categoryList);
            $this->assign('info',$lottery_info);
            $this->display();
        }
    }

    /**
     * 抽经记录
     */
    public function lotteryLog(){
        $nickname      = I('get.nickname',''); //中奖用户昵称
        $mobile       = I('get.mobile',''); //中奖用户手机
        $lottery_id   = I('get.lottery_id',''); //奖品ID
        $day   = I('get.day','');  //中奖日期

        $user_name    = trim(I('get.user_name','','htmlspecialchars'));
        $lottery_name = trim(I('get.lottery_name','','htmlspecialchars'));

        $LotteryGifts = D('LotteryGifts');
        $LotteryLogs  = M('lottery_logs');
        $lotteryTypes = M('lottery_types');

        $condition = $params = [];
        if(!empty($nickname)){
            $condition['U.nickname'] = $nickname;
            $params['nickname'] = $nickname;
        }
        if(!empty($mobile)){
            $condition['U.mobile'] = $mobile;
            $params['mobile'] = $mobile;
        }
        if(!empty($lottery_id)){
            $condition['lottery_id'] = $lottery_id;
            $params['lottery_id'] = $lottery_id;
        }
        if(!empty($day)){
            $condition['day'] = $day;
            $params['day'] = $day;
        }

        $count = $LotteryLogs
            ->alias('L')
            ->join('__USERS__ U on L.user_id = U.user_id')
            ->where($condition)
            ->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        if(!empty($user_id)){
            $condition['L.user_id'] = $user_id;
        }
        $logList = $LotteryLogs
            ->alias('L')
            ->join('__USERS__ U on L.user_id = U.user_id')
            ->field('L.*,U.nickname,U.mobile')
            ->where($condition)
            ->order('L.lottery_log_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        //获取奖品类型
        $categoryList = $lotteryTypes->where([])->order('addtime desc')->select();

        //获取每日抽奖消耗积分
        $jifen = M('lottery_set')->where(array('title' => 'spend_jifen'))->find();

        $this->assign('categoryList',$categoryList);
        $this->assign('jifen',$jifen['content']);
        $this->assign('params',$params);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('logList',$logList);
        $this->display();
    }

    public function updJifen(){
        $jifen = I('get.jifen','');
        if($jifen < 10){
            $return_arr = array(
                'status' => 4001,
                'msg'   => '积分不能小于10',
            );
            $this->ajaxReturn(json_encode($return_arr));
        }
        $model = M('lottery_set');

        $result = $model->where(array('title' => 'spend_jifen'))->save(array('content' => $jifen));
        if($result){
            $return_arr = array(
                'status' => 200,
                'msg'   => '操作成功',
            );
        }else{
            $return_arr = array(
                'status' => 4002,
                'msg'   => '操作失败',
            );
        }
        echo json_encode($return_arr);
    }

}