<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use app\dataapi\server\UserServer;
use think\Request;
use think\Response;
use app\dataapi\server\BidOrderServer;
use app\dataapi\controller\BaseApi;
use app\dataapi\server\ProductServer;
use app\dataapi\server\GoodCollectServer;
use app\dataapi\model\Users;
use think\Log;
use BaconQrCode\Renderer\Color\Rgb;
use app\dataapi\controller\JobQeue;


class UppCase    extends BaseApi
{

    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {
        $UserServerobj = new UserServer();

        $headimgurl =$request->param('headimgurl');
        $nickname =$request->param('nickname');

//|| (stripos($headimgurl,'.jpg')===false ||stripos($headimgurl,'.png')===false )


        if(empty($headimgurl) ||empty($nickname) || (stripos($headimgurl,'.jpg')===false  && stripos($headimgurl,'.png')===false && stripos($headimgurl,'.gif')===false)){

            return '参数错误';

        }



        $userinfo=[
            'headimgurl'=>$request->param('headimgurl'),
            'nickname'=>$request->param('nickname'),
            'subscribe'=>0,
            'country'=>0,
            'province'=>0,
            'city'=>0,
        ];

        $dd =$UserServerobj->vhostaddThirdUserinfo('', $request,$userinfo,1);
        if(!is_object($dd) &&  $dd==-1){
            return '名称重复';
        }
        if(!is_object($dd)  && $dd==-2){
            return '图片重复';
        }
        if(is_object($dd) ){
        return ['data'=>$dd,'code'=>200,'message'=>'生成成功'];
        }

        return '参数错误';

    }




    //竞拍成交
    public function IntendedUser(Request $request)
    {
        $mobile= $request->param('mobile');
        $nickname =$request->param('nickname');
        $Usersobj =new   Users();
        $data =  Db::name('users')->alias('a')->join('goods g','g.user_id=a.user_id');
        if($mobile!=''){
            $data =  $data->where('a.mobile','like','%'.$mobile.'%');
        }
        if($nickname!=''){
            $data =  $data->whereOr('a.nickname','like','%'.$nickname.'%');
        }
        $data =$data->field('a.user_id,a.mobile,a.nickname,a.head_pic,g.goods_id,g.endTime')->where('g.goods_status',1)->whereNull('g.delete_time')->where('g.endTime','<',$_SERVER['REQUEST_TIME'])->order('g.endTime DESC')->limit(1)->select();
        if(!empty($data)){
            foreach ($data as $v){
                echo "用户id ". $v['user_id']."<a target=\"_blank\" href=\"http://w.tianbaoweipai.com/statistics?user_id=".$v['user_id']."\">点击查看用户个人官网</a>". '<br>';
                echo "用户手机号码 ".$v['mobile'].'<br>';
                echo "用户微信昵称  ". $v['nickname'].'<br>';
                echo "用户头像<a target=\"_blank\" href=\"".$v['head_pic']."\">点击查看</a>". '<br>';
                echo "作品id  ". $v['goods_id'].'<br>';
                echo "过期时间 ". date('Y-m-d H:i:s',$v['endTime']).'<br>';
                $renderer = new \BaconQrCode\Renderer\Image\Png();

                $color = new Rgb(255,255,0);
                $renderer->setBackgroundColor($color);
                $color = new Rgb(255,0,255);
                $renderer->setForegroundColor($color);
                $renderer->setHeight(256);
                $renderer->setWidth(256);
                $renderer->setMargin(3);

                $writer = new \BaconQrCode\Writer($renderer);
              $ddd =   $writer->writeString("http://w.tianbaoweipai.com/cacse/".$v['goods_id']);

                $ddd = base64_encode($ddd);

                echo "case". "<a target=\"_blank\" href=\"http://w.tianbaoweipai.com/cacse/".$v['goods_id']."\">案例</a>".'<br>';
                echo "<img src=\"data:image/png;base64,".$ddd."\" /><br>";
                echo "--------------------------------------------------------------------------------------------------------------------<br>";
            }
        }
    }

    public function flagimage(){
        $datauser =  Db::name('users')->where('fictitious',1)->order('rand()')->find();
        $flag = $this->imagetest($datauser['head_pic']);
        if(!$flag or !isset($flag['width']) or   !isset($flag['height']) or ($flag['width']==0 or   $flag['height']==0)){
           return  $this->flagimage();
        }
        return $datauser;

    }

    public function imagetest($url){
        if(empty($url)){
            return false;
        }
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);
        return  $imageSize;
    }


    //竞拍成交 案例
    public function  CaseUser(Request $request)
    {
        $goods_id =$request->param('goods_id');
       $total = $num =  (int)$request->param('num')>0?(int)$request->param('num'):1;
        $page =$page??1;
        $bid_price = floatval($request->param('bid_price'));


        if($goods_id<=0){
            Response::create(['code' => '4008', 'message' => '出价失败作品不id不存在'], 'json')->header($this->header)->send();
        }

        //判断是否已经拍卖尽速
        $goods =new   Goods();
        $datagood =  $goods->where('goods_id',$goods_id)->field('start_price,every_add_price,upload_time,endTime')->find();
        if(!empty($datagood) ){
            $datagood =$datagood->toArray();
        }else{
            return 0;
        }

        if($datagood['endTime'] <$_SERVER['REQUEST_TIME']){
            Response::create(['code' => '4008', 'message' => '拍卖结束'], 'json')->header($this->header)->send();
        }

       // $dd =     Db::query("SELECT user_id,fictitious  FROM `tp_users`  where delete_time is null and fictitious=1  ORDER BY RAND() LIMIT ".$num);

        $BidOrderServer = new BidOrderServer();
        $UserServer = new UserServer();
            Db::startTrans();
            try{
              while($num>=1){
                    $ddd= $this->flagimage();
                    $num--;
                  $flag = $UserServer->userUpdateLike($ddd['user_id'],['goods_id'=>$goods_id]);
                  if( $total>1){
                      $data = $BidOrderServer->vhostAdd($ddd['user_id'], $goods_id, $request);

                  }
                  //成交
                  $data = $BidOrderServer->vhostAddOrderOver($ddd['user_id'], $goods_id, $request);
                }
                $nggum =mt_rand(16,26);
                while($nggum>=1){
                    $ddd= $this->flagimage();
                    $num--;
                    $flag = $UserServer->userUpdateLike($ddd['user_id'],['goods_id'=>$goods_id]);
                }


                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }




        //跳转
    }


    public function testQuene(){

        $datauser = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->whereNull('a.delete_time')
            ->field('a.*,q.*')
            ->group('a.user_id')
            ->order('rand()')
            ->find();
print_r($datauser);
exit;

        try{
            $JobQeueobj =  new  JobQeue();
            $JobQeueobj->autoGiveFanUser(4145);
        }catch(\RuntimeException $e){
            Log::record('日志信息','info'.$e);
        }

    }



}
