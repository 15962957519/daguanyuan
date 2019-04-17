<?php
use think\Cache;
use think\Db;
use think\Request;
use app\api\server\AliyunSdkMsg;

/**
 * 获取商品一二三级分类
 * @return type
 */
    function get_goods_category_tree(){
        $tree = $arr = $result = array();
        $cat_list = Db::name('goods_category')->cache(true,60)->where(['is_show' => 1])->order('sort_order')->select();//所有分类
        if($cat_list){
            foreach ($cat_list as $val){
                $val['image'] = 'http://admin.jiuxintangwenhua.com'.$val['image'];
                if($val['level'] == 2){
                    $arr[$val['parent_id']][] = $val;
                }
                if($val['level'] == 3){
                    $crr[$val['parent_id']][] = $val;
                }
                if($val['level'] == 1){
                    $tree[] = $val;
                }
            }

            foreach ($arr as $k=>$v){
                foreach ($v as $kk=>$vv){
                    $arr[$k][$kk]['sub_menu'] = empty($crr[$vv['id']]) ? array() : $crr[$vv['id']];
                }
            }

            foreach ($tree as $val){
                $val['tmenu'] = empty($arr[$val['id']]) ? array() : $arr[$val['id']];
                $result[$val['id']] = $val;
            }
        }
        return $result;
    }

    //卖家客服图片
    function kefu_images($files){
        // 获取表单上传文件
        $arr=array();
        try{
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $files->move(ROOT_PATH . 'public/upload' . DS . 'kefu_images');
            if($info){
                $arr = 'upload'.DS .'kefu_images/'.$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                echo $files->getError();
            }
        }catch (\ErrorException  $e){
            echo $e->getMessage();
        }
        return $arr;
    }


    //发布时间状态
    function time_tran($the_time) {
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


