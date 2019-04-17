<?php
namespace app\dataapi\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Response;
use app\dataapi\server\ProductServer;
use app\Home\Logic\UsersLogic;
use  app\dataapi\controller\BaseApi;

class Article    extends BaseApi
{

    public function index(Request $request){

        $cate_id =(int)$request->param('cate_id');
        $limit= !empty($request->param('limit')) && $request->param('limit')>0 ?(int)$request->param('limit'):5;
        $article_cat_array = Db::name('article_cat')->where("show_in_nav  = 1")->field('cat_id,parent_id,cat_name')->order('sort_order asc')->select();
        $cat_id_array =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',86);

        if($cate_id>0){
            $cat_id_arraysub =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',$cate_id);
        }else{
            $cat_id_arraysub =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',86);
        }

        $cat_id_arrayid=array_column($cat_id_arraysub,'cat_id');




        $article_lists = Db::name('article')->whereIN('cat_id',$cat_id_arrayid)->order('article_id desc')->limit($limit)->select();
        foreach ($article_lists as &$v){
            $v['add_time'] = $this->time_tran($v['add_time']);
        }
        Response::create(['data'=>['hot_article_lists'=>$article_lists,'classification'=>$cat_id_array],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }

    //获取公告管理

    public function indexNotice(Request $request){
        $cate_id =(int)$request->param('cate_id');
        $limit= !empty($request->param('limit')) && $request->param('limit')>0 ?(int)$request->param('limit'):5;
        $article_cat_array = Db::name('article_cat')->where("cat_type  = ".$cate_id)->field('cat_id,parent_id,cat_name')->order('sort_order asc')->select();
        $cat_id_array =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',0);

        if($cate_id>0){
            $cat_id_arraysub =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',3);
        }else{
            $cat_id_arraysub =$this->getDataTree($article_cat_array,'cat_id','parent_id','child',0);
        }

        $cat_id_arrayid=array_column($cat_id_arraysub,'cat_id');
        $article_lists = Db::name('article')->whereIN('cat_id',$cat_id_arrayid)->order('article_id desc')->limit($limit)->select();
        foreach ($article_lists as &$v){
            $v['add_time'] = $this->time_tran($v['add_time']);
            $v['img'] = config('domain').'static/img/logo100_100.png';
        }
        Response::create(['data'=>['hot_article_lists'=>$article_lists,'classification'=>$cat_id_array],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }
   public  function getDataTree($rows, $id='id',$pid = 'parentid',$child = 'child',$root=0) {
        $tree = array(); // 树
        if(is_array($rows)){
            $array = array();
            foreach ($rows as $key=>$item){
                $array[$item[$id]] =& $rows[$key];
            }
            foreach($rows as $key=>$item){
                $parentId = $item[$pid];
                if($root == $parentId){
                    $tree[] =&$rows[$key];
                }else{
                    if(isset($array[$parentId])){
                        $parent =&$array[$parentId];
                        $parent[$child][]=&$rows[$key];
                    }
                }
            }
        }
        if(isset($array[$root])){
           array_push($tree,$array[$root]);
        }

        return $tree;
    }
    /**
     * 图片地址替换成压缩URL
     * @param string $content 内容
     * @param string $suffix 后缀
     */
    static  function  get_img_thumb_url($content="",$preffix="")
    {
        $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?([\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
        $callback = function($matches) use($preffix){
//'<img src='.$suffix.'"${1}" style="max-width:100%">'
                $url= $matches[1];
            if(stripos($url,'http://')===false || stripos($url,'https://')===false){
                $pre = mb_substr($url,0,1);
                if($pre =='.'){
                    $url = mb_substr($url,1);
                }
                $pre = mb_substr($url,0,1);
                if($pre =='/'){
                    $url = mb_substr($url,1);
                }
                $url = $preffix.$url;
            }
            return   '<img src='.$url.' style="max-width:100%">';

        };
        $content = preg_replace_callback ($pregRule,$callback,$content);
        return $content;
    }

    /**
     * 文章内容页
     */
    public function detail(Request $request){
        $article_id = $request->param('article_id');
        $parent=[];
        $article = Db::name('article')->where("article_id=$article_id")->find();
        if($article){
            $article['content'] =htmlspecialchars_decode($article['content']);
            //对没有http的添加域名
            $url = config('addomain');
            $article['content']=   self::get_img_thumb_url( $article['content'],$url);
            $article['title'] =mb_substr($article['title'],0,10);
            $article['add_time'] =$this->time_tran($article['add_time']);
            $parent = Db::name('article_cat')->where("cat_id=".$article['cat_id'])->find();
        }
        Response::create(['data'=>['article'=>$article,'parent'=>$parent],'code'=>2000,'message'=>'获取成功'], 'json')->header($this->header)->send();
    }


    /**
     * 文章内容页
     */
    public function addForwardCount(Request $request){
        $article_id = $request->param('article_id');
        $parent=[];
        $article = Db::name('article')->where("article_id=$article_id")->find();
        if($article){
            $parent = Db::name('article')->where("article_id=$article_id")->update(['forward'=>['exp','forward+1']]);
        }
        Response::create(['data'=>['article'=>$article],'code'=>2000,'message'=>'添加成功'], 'json')->header($this->header)->send();
    }




    public  function time_tran($the_time) {
        $now_time = date("Y-m-d H:i:s", time());
        //echo $now_time;
        $now_time = strtotime($now_time);
        $show_time =$the_time;
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return '';
        } else {
            if ($dur < 60) {
                return $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时前';
                    } else {
                        if ($dur < 259200) {//3天内
                            return floor($dur / 86400) . '天前';
                        } else {
                            return date("Y-m-d H:i:s", $the_time);
                        }
                    }
                }
            }
        }
    }

}
