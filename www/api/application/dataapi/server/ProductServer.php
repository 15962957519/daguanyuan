<?php

namespace app\dataapi\server;

use Carbon\Carbon;
use app\dataapi\model\BidOrder;
use app\dataapi\model\Goods;
use think\Config;
use think\Db;
use think\Cache;
use think\image\Exception;
use app\dataapi\server\BidOrderServer;
use think\Request;
use app\dataapi\server\AliyunOss;
use app\Home\Logic\UsersLogic;
use app\dataapi\model\GoodsImages;
use  app\dataapi\behavior\MemberLimit;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class ProductServer
{

//获取用户下面所有的产品
    public function getProductsByUserid(int $userid, int $limit = 0)
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($userid); // 获取用户信息
        //->where('is_hot',1)->where('is_on_sale',1)
        //$hot_goods =  Goods::all(['user_id'=>$userid]);
        //$hot_goods =  ( new Goods())->where('user_id',$userid)->order('goods_id DESC')->limit(20)->cache(true,TPSHOP_CACHE_TIME);

//        $imgdataobj = Db::name('GoodsImages');
//        $subQuery = $imgdataobj->field('min(img_id),img_id,goods_id,rescourse_id,image_url_remote,image_url_remote_expire,image_url_remote_nowater,enpiount')
//            ->whereNull('delete_time')
//            ->group('goods_id')
//            ->buildSql();


        $imgdataobj = Db::name('GoodsImages')
            ->alias('w');
        $subQuery = $imgdataobj->field('min(img_id)')
            ->where('a.goods_id = w.goods_id')
            ->whereNull('delete_time')
            ->limit(1)
            ->buildSql();
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->field('a.*,' . $subQuery . ' as img_id')
            ->where(['a.user_id' => $userid])
            ->whereNull('a.delete_time')
            ->order('a.goods_id', 'DESC');
        if ($limit > 0) {
            $hot_goods = $hot_goods->limit($limit)->select();
        } else {
            $hot_goods = $hot_goods->select();
        }
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();
        $imgdataobj = Db::name('GoodsImages');

        $aliyunobj = new AliyunOss(false);

        foreach ($hot_goods as &$v) {
            if ($v['goods_status'] == 1 && $v['endTime'] > 0 && $v['endTime'] < $_SERVER['REQUEST_TIME']) {
                $v['goods_status_desc'] = '流拍';
                $v['goods_status_f'] = '1';
            } elseif ($v['goods_status'] == 1 && $v['endTime'] >= $_SERVER['REQUEST_TIME']) {
                $v['goods_status_desc'] = '拍卖中';
                $v['goods_status_f'] = '2';
            } elseif ($v['goods_status'] == 2) {
                $v['goods_status_desc'] = '拍卖成功';
                $v['goods_status_f'] = '3';
            } elseif ($v['is_on_sale'] == 0) {
                $v['goods_status_desc'] = '下架了';
                $v['goods_status_f'] = '4';
            }
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            $temp = $imgdataobj->where('img_id', $v['img_id'])->find();
            if (isset($temp['image_url_remote']) && $temp['image_url_remote'] != '' && $temp['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                $v['img'] = $temp['image_url_remote'];
                $v['image_url_remote_nowater'] = $temp['image_url_remote_nowater'];
            } else {
                if (!empty($temp['rescourse_id'])) {
                    try {
                        $v['img'] = $aliyunobj->getCurlofimg(Config::get('aliyun.bucket'), $temp['rescourse_id'], 31536000);
                        $nowaterimg = $aliyunobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $temp['rescourse_id'], 31536000);
                        $v['image_url_remote_nowater'] = $nowaterimg;
                        $imgdataobj->where('img_id', $v['img_id'])
                            ->update([
                                'image_url_remote' => $v['img'],
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $v['img'] = '';
                    }
                } else {
                    $v['img'] = '';
                }
            }
            $v['level'] = $user_info['result']['level'];
        }
        return $hot_goods;
    }


//获取用户下面所有的产品
    public function getSendMessageProductsByUserid(int $userid, int $limit = 0)
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($userid); // 获取用户信息
        $imgdataobj = Db::name('GoodsImages')
            ->alias('w');
        $subQuery = $imgdataobj->field('min(img_id)')
            ->where('a.goods_id = w.goods_id')
            ->whereNull('delete_time')
            ->limit(1)
            ->buildSql();
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->field('a.*,' . $subQuery . ' as img_id')
            ->where(['a.user_id' => $userid])
            ->where(['a.goods_status' => 1])
            ->where('a.endTime', '>', $_SERVER['REQUEST_TIME'])
            ->whereNull('a.delete_time')
            ->order('a.goods_id', 'DESC');
        if ($limit > 0) {
            $hot_goods = $hot_goods->limit($limit)->select();
        } else {
            $hot_goods = $hot_goods->select();
        }
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();
        $imgdataobj = Db::name('GoodsImages');
        foreach ($hot_goods as &$v) {
            if ($v['goods_status'] == 1 && $v['endTime'] > 0 && $v['endTime'] < $_SERVER['REQUEST_TIME']) {
                $v['goods_status_desc'] = '流拍';
                $v['goods_status_f'] = '1';
            } elseif ($v['goods_status'] == 1 && $v['endTime'] >= $_SERVER['REQUEST_TIME']) {
                $v['goods_status_desc'] = '拍卖中';
                $v['goods_status_f'] = '2';
            } elseif ($v['goods_status'] == 2) {
                $v['goods_status_desc'] = '已经成交';
                $v['goods_status_f'] = '3';
            } elseif ($v['is_on_sale'] == 0) {
                $v['goods_status_desc'] = '下架了';
                $v['goods_status_f'] = '4';
            }
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';

            $temp = $imgdataobj->where('img_id', $v['img_id'])->find();

            if (isset($temp['image_url_remote']) && $temp['image_url_remote'] != '' && $temp['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                $v['img'] = $temp['image_url_remote'];
                $v['image_url_remote_nowater'] = $temp['image_url_remote_nowater'];
            } else {
                if (!empty($temp['rescourse_id'])) {
                    try {
                        $v['img'] = (new AliyunOss(false))->getCurlofimg(Config::get('aliyun.bucket'), $temp['rescourse_id'], 31536000);
                        $nowaterimg = (new AliyunOss(false))->nomatergetCurlofimg(Config::get('aliyun.bucket'), $temp['rescourse_id'], 31536000);
                        $v['image_url_remote_nowater'] = $nowaterimg;
                        $imgdataobj->where('img_id', $v['img_id'])
                            ->update([
                                'image_url_remote' => $v['img'],
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $v['img'] = '';
                    }
                } else {
                    $v['img'] = '';
                }
            }
            $v['level'] = $user_info['result']['level'];
        }
        return $hot_goods;
    }


    //获取用户下面所有的产品
    public function getProductListsById(array $good_id, $field = 'a.goods_id,a.goods_name,a.endTime,a.upload_time,w.*')
    {
        $imgdataobj = Db::name('GoodsImages');
        $subQuery = $imgdataobj->field('min(img_id),img_id,goods_id,rescourse_id,image_url_remote,image_url_remote_expire,image_url_remote_nowater,enpiount')
            ->whereIn('goods_id', $good_id)
            ->whereNull('delete_time')
            ->group('goods_id')
            ->buildSql();
        $datalists = [];
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');
        $expritetime = $datetime->getTimestamp();
        $updatelist = [];
        if (empty($good_id)) {
            return [];
        }
        $aliobg = new AliyunOss(false);
        $ids = implode(',', $good_id);
        $d = Db::name('Goods')
            ->alias('a')
            ->join($subQuery . ' w', 'a.goods_id = w.goods_id and  a.delete_time is NULL and a.goods_id in (' . $ids . ')')
            ->field($field)
            ->chunk(30, function ($products) use ($expritetime, &$datalists, $aliobg) {
                foreach ($products as &$v) {
                    $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
                    if (isset($v['goods_content'])) {
                        if (mb_strlen($v['goods_content'], 'utf8') > 50) {
                            $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8') . '....';
                        } else {
                            $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8');
                        }
                    }

                    $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
                    if ($v['image_url_remote'] != '' && $v['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                        $v['img'] = $v['image_url_remote'];
                    } else {
                        if (!empty($v['rescourse_id'])) {
                            try {
                                $v['img'] = $aliobg->getCurlofimg(Config::get('aliyun.bucket'), $v['rescourse_id'], 31536000);
                                $nowaterimg = $aliobg->nomatergetCurlofimg(Config::get('aliyun.bucket'), $v['rescourse_id'], 31536000);
                                $updatelist[] =
                                    [
                                        'img_id' => $v['img_id'],
                                        'image_url_remote' => $v['img'],
                                        'image_url_remote_expire' => ['exp', $expritetime],
                                        'image_url_remote_nowater' => $nowaterimg
                                    ];
                            } catch (Exception $e) {
                                $v['img'] = '';
                            }
                        } else {
                            $v['img'] = '';
                        }
                    }
                    $datalists[] = $v;
                }
                //图片一次性更新
                if (!empty($updatelist)) {
                    $GoodsImageso = new     GoodsImages();
                    $GoodsImageso->saveAll($updatelist);
                }
            }, 'a.goods_id');
        return $datalists;
    }


    //获取某个删除的信息
    public function getProductById(int $good_id)
    {
        $imgdataobj = Db::name('GoodsImages');
        $hot_goods = Db::name('Goods')
            ->where('goods_id', $good_id)
            ->whereNull('delete_time')
            ->order('goods_id DESC')
            ->limit(1)
            ->select();
        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');

        $expritetime = $datetime->getTimestamp();
        $imgdataobj = Db::name('GoodsImages');
        foreach ($hot_goods as &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            if (mb_strlen($v['goods_content'], 'utf8') > 50) {
                $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8') . '....';
            } else {
                $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8');
            }
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            $imagetempdata = $imgdataobj->field('min(img_id),img_id,goods_id,rescourse_id,image_url_remote,image_url_remote_expire,image_url_remote_nowater,enpiount')
                ->where('goods_id', $v['goods_id'])
                ->whereNull('delete_time')
                ->group('goods_id')
                ->limit(1)
                ->find();
            if ($imagetempdata['image_url_remote'] != '' && $imagetempdata['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                $v['img'] = $imagetempdata['image_url_remote'];
            } else {
                if (!empty($imagetempdata['rescourse_id'])) {
                    try {
                        $v['img'] = (new AliyunOss(false))->getCurlofimg(Config::get('aliyun.bucket'), $imagetempdata['rescourse_id'], 31536000);
                        $nowaterimg = (new AliyunOss(false))->nomatergetCurlofimg(Config::get('aliyun.bucket'), $imagetempdata['rescourse_id'], 31536000);
                        $imgdataobj->where('img_id', $imagetempdata['img_id'])
                            ->update([
                                'image_url_remote' => $v['img'],
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $v['img'] = '';
                    }
                } else {
                    $v['img'] = '';
                }
            }
        }
        return $hot_goods[0];
    }


    /*
     * 删除作品  1删除成功   2删除失败  3要删除数据不存在
     *
     */
    public function delate(array $good_ids, int $user_id): int
    {
        $Goods = new     Goods();
        $Goods = $Goods->whereIn('goods_id', $good_ids)->where('user_id', $user_id)->find();

        if (!is_null($Goods)) {
            $dd = $Goods->GoodsImages()->find();
            if (!is_null($dd)) {
                return ($dd->delete() && $Goods->delete()) ? 1 : 2;
            }
        }
        // 软删除
        return 3;
    }


//获取用户下面所有的产品
    public function getProducts(int $limit, int $cat_id, int $page = 1)
    {
        //先查询redis cache
        $hot_goods = cachecatchg($cat_id . '_' . $page, 'getProductds');
        $mycatdb = Db::connect('mycat104');
        $dbuser = Db::name('third_users');
        if (!empty($hot_goods)) {
            $BidOrderServerobj = new BidOrderServer();
            $bidorder = new BidOrder();
            foreach ($hot_goods['data'] as $key => &$v) {
                $v['bidlists'] = $BidOrderServerobj->listsarray($v['goods_id'], 4);
                $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => strtotime($v['upload_time'])])->max('bid_price');
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                    $v['lastnum'] = (string)$v['start_price'];
                } else {
                    $v['lastnum'] = (string)$bidorderdata;
                }
                $collectdata = getusercollectlistsdbobj($v['goods_id'], $mycatdb, $dbuser);
                if ($collectdata) {
                    $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                    $v['likecount'] = $collectdata['total'];
                } else {
                    $hot_goods['data'][$key]['collectdata'] = [];
                    $v['likecount'] = '0';
                }
            }
            return $hot_goods;
        }
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->where('a.is_toplatform', 1)
            ->where('q.is_authentication', 1)
            ->where('q.level', '>', 1)
            ->where('a.goods_status', 1)
            ->where('a.endTime', '>', $_SERVER['REQUEST_TIME'])
            ->whereNull('a.delete_time');
        if ($cat_id > 0) {
            $hot_goods = $hot_goods->where('a.cat_id', $cat_id);
        }
        $hot_goods = $hot_goods->field('a.*,q.reg_time,q.user_id,q.nickname,q.head_pic,q.mobile_validated,q.is_authentication,q.fictitious')
            ->group('a.user_id')
            ->order('a.goods_id desc')
            ->paginate($limit);
        $hot_goods = $hot_goods->toArray();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $BidOrderServerobj = new BidOrderServer();
        $bidorder = new BidOrder();
        foreach ($hot_goods['data'] as $key => &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['timelevel'] = MemberLimit::memberLevel($_SERVER['REQUEST_TIME'],$v['reg_time'],$v['fictitious']);
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            $v['bidlists'] = $BidOrderServerobj->listsarray($v['goods_id'], 4);
            $v['pagenow'] = 1;
            $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                $v['lastnum'] = (string)$v['start_price'];
            } else {
                $v['lastnum'] = (string)$bidorderdata;
            }
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
            }
            $collectdata = getusercollectlistsdbobj($v['goods_id'], $mycatdb, $dbuser);
            if ($collectdata) {
                $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $hot_goods['data'][$key]['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,rescourse_id,image_url_remote,image_url_remote_nowater,image_url_remote_expire')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $nomasterimg = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $nomasterimg];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $nomasterimg,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        cachecatchset($cat_id . '_' . $page, $hot_goods, 'getProductds', 10);
        return $hot_goods;
    }


//获取用户下面所有的产品
    public function getProductsOfcategory(int $limit, int $cat_id, int $page = 1)
    {
        //先查询redis cache
        $hot_goods = cachecatchg($cat_id . '_' . $page, 'getProductsOfcategory');
        if (!empty($hot_goods)) {
            foreach ($hot_goods['data'] as $key => &$v) {
                $v['likecount'] = getusercollectcount($v['goods_id']);
            }
            return $hot_goods;
        }


        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id')
            ->where('a.endTime', '>', $_SERVER['REQUEST_TIME'])
            ->whereNull('a.delete_time');
        if ($cat_id > 0) {
            $hot_goods = $hot_goods->where('a.cat_id', $cat_id);
        }
        $hot_goods = $hot_goods->field('a.goods_id,a.start_price,a.every_add_price,a.goods_name,q.reg_time,q.user_id,q.nickname,q.head_pic,q.mobile_validated')
            ->group('a.user_id')
            ->cache(true)
            ->order('a.goods_id desc')
            ->paginate($limit);
        $hot_goods = $hot_goods->toArray();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $BidOrderServerobj = new BidOrderServer();
        $bidorder = new BidOrder();
        foreach ($hot_goods['data'] as $key => &$v) {
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
            }
            $v['likecount'] = getusercollectcount($v['goods_id']);
            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('image_url_remote_expire,image_url_remote,image_url_remote_nowater,rescourse_id,img_id')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(9)
                ->select();
            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $nomasterimg = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $nomasterimg];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];

                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $nomasterimg,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        cachecatchset($cat_id . '_' . $page, $hot_goods, 'getProductsOfcategory', 60);
        return $hot_goods;
    }

//获取用户下面所有的产品
    public function getProductsOne(int $limit, int $goods_id, int $page = 1)
    {
        //先查询redis cache
        $hot_goods = cachecatchg($goods_id . '_' . $page, 'getProductsOne');
        $hot_goodsobj = Db::name('Goods');
        if (!empty($hot_goods)) {
            $BidOrderServerobj = new BidOrderServer();
            $bidorder = new BidOrder();
            foreach ($hot_goods['data'] as $key => &$v) {
                unset($v['password']);
                $v['bidlists'] = $BidOrderServerobj->listsarray($v['goods_id'], 4);
                $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => strtotime($v['upload_time'])])->max('bid_price');
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                    $v['lastnum'] = (string)$v['start_price'];
                } else {
                    $v['lastnum'] = (string)$bidorderdata;
                }
                $collectdata = getusercollectlists($v['goods_id']);
                if ($collectdata) {
                    $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                    $v['likecount'] = $collectdata['total'];
                } else {
                    $hot_goods['data'][$key]['collectdata'] = [];
                    $v['likecount'] = '0';
                }
                //   $hot_goodsobj->where('goods_id', $goods_id)->setInc('click_count');
                $hot_goodsobj->where('goods_id', $goods_id)->update(['click_count' => ['exp', 'click_count+1']]);
                $v['click_count'] = $hot_goodsobj->where('goods_id', $goods_id)->value('click_count');
            }
            return $hot_goods;
        }

        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id', 'left');
        if ($goods_id > 0) {
            $hot_goods = $hot_goods->where('a.goods_id', $goods_id);
        }

        $hot_goods = $hot_goods->field('a.*,q.*')
            ->whereNull('a.delete_time')
            ->group('a.user_id')
            ->order('a.goods_id desc')
            ->paginate($limit);
        $hot_goods = $hot_goods->toArray();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $BidOrderServerobj = new BidOrderServer();
        $bidorder = new BidOrder();
        foreach ($hot_goods['data'] as $key => &$v) {
            unset($v['password']);
            $hot_goodsobj->where('goods_id', $goods_id)->update(['click_count' => ['exp', 'click_count+1']]);
            $v['click_count'] = $hot_goodsobj->where('goods_id', $goods_id)->value('click_count');
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['timelevel'] = MemberLimit::memberLevel($_SERVER['REQUEST_TIME'], $v['reg_time']);
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            $v['bidlists'] = $BidOrderServerobj->listsarray($v['goods_id'], 4);
            $v['pagenow'] = 1;
            $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                $v['lastnum'] = (string)$v['start_price'];

            } else {
                $v['lastnum'] = (string)$bidorderdata;
            }
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
            }

            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = htmlspecialchars_decode($v['goods_content']);
            }

            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $hot_goods['data'][$key]['collectdata'] = [];
                $v['likecount'] = '0';
            }


            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('image_url_remote,image_url_remote_expire,image_url_remote_nowater,img_id,rescourse_id')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->cache(true)
                ->order('img_id')
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']];
                } else {
                    try {
                        $nomasterimg = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $nomasterimg];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];

                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $nomasterimg,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);

                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        cachecatchset($goods_id . '_' . $page, $hot_goods, 'getProductsOne', 60);
        return $hot_goods;
    }



//获取用户下面所有的产品
    /*
     * $type=1 获取当前用户下面的作品   $type=2 获取关注的人或者关注我的人
     *
     */
    public function getProductsForpaimaiquan(int $limit, int $user_id = 0, int $type = 1, int $self_user_id = 0, int $goods_id, int $page = 1)
    {
        if ($page > 0) {
            $start = ($page - 1) * $limit;
        } else {
            $start = 0;
        }
        $limit = 'limit ' . $start . ',' . $limit;
        $where = 'where g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'];
        $order = ' order by u.level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';
        if ($goods_id > 0) {
            $where = 'where g.goods_id=' . $goods_id;
            $order = '';
            //显示用户官网数据
            $hot_goodst = Db::query("SELECT u.is_authentication,u.head_pic,u.user_id,u.reg_time,u.nickname,g.click_count,g.goods_status,g.endTime,g.start_price,g.goods_id,g.upload_time,g.every_add_price,g.reserveprice,g.goods_name,g.goods_content FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order} {$limit}");
        } else {
            if ($user_id > 0) {
                $where = 'where  g.user_id=' . $user_id . ' and g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                $order = ' order by g.upload_time DESC,u.level DESC,u.weight_sale DESC,g.cat_id DESC';
                //显示用户官网数据
                $hot_goodst = Db::query("SELECT u.is_authentication,u.head_pic,u.user_id,u.reg_time,u.nickname,g.click_count,g.goods_status,g.endTime,g.start_price,g.goods_id,g.upload_time,g.every_add_price,g.reserveprice,g.goods_name,g.goods_content  FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
            } else {
                if ($self_user_id > 0) {
                    $user_id = $self_user_id;
                    $where = 'where u.delete_time is null and g.delete_time is null';
                    $order = ' order by g.on_time DESC,u.user_level desc,ee.add_time DESC';
                    $hot_goodst = Db::query('SELECT  distinct(g.goods_id),u.is_authentication,u.head_pic,u.user_id,u.reg_time,u.nickname,g.click_count,g.goods_status,g.endTime,g.start_price,g.upload_time,g.every_add_price,g.reserveprice,g.goods_name,g.goods_content FROM tp_users u INNER JOIN `tp_goods` `g` ON  g.endTime>' . $_SERVER['REQUEST_TIME'] . " and `g`.`user_id`=`u`.`user_id`  INNER JOIN  (SELECT fans_id as user_id,add_time FROM `tp_fans_collect` WHERE `user_id` = {$user_id}  and delete_time is Null  UNION SELECT user_id,add_time FROM tp_fans_collect where fans_id={$user_id}  and delete_time is Null  union select {$user_id} as user_id ,44555 as add_time ) as ee on ee.user_id= g.user_id {$where}  {$order} {$limit}");
                } else {
                    $where = 'where  g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                    //显示用户官网数据
                    $hot_goodst = Db::query("SELECT u.is_authentication,u.head_pic,u.user_id,u.reg_time,u.nickname,g.click_count,g.goods_status,g.endTime,g.start_price,g.goods_id,g.upload_time,g.every_add_price,g.reserveprice,g.goods_name,g.goods_content FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
                }
            }
        }
        $hot_goods = &$hot_goodst;
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $datetime->modify('+31536000 second');
        $expritetime = $datetime->getTimestamp();
        $timev = 0.00;
        $bidorderdata = 0.00;
        //   $expritetime = 0;
        $imgdataobj = null;
        $imgdataobj = Db::name('GoodsImages');
        $goodso = Db::name('goods');
        $GoodsImageso = new  GoodsImages();
        foreach ($hot_goods as $key => $v) {
            $crv = &$hot_goods[$key];
            $tv = cachecatchg($v['goods_id'], 'getproductsforpaimaiquangoodsimagesyyyyy');
            if (empty($tv)) {
                $crv['timelevel'] =MemberLimit::memberLevel($currnet, $v['reg_time']);
                $crv['endTimeflag'] = $currnet >= $v['endTime'] ? '1' : '0';
                $crv['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
                $crv['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
                $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
                //获取click_count
                $crv['click_count'] = $goodso->where('goods_id', $v['goods_id'])->value('click_count');
                $crv['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                    $crv['lastnum'] = (string)$v['start_price'];
                } else {
                    $crv['lastnum'] = (string)$bidorderdata;
                }
                $crv['bidlists'] = $BidOrderServer->listsarray($v['goods_id'], 4);
                $crv['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
                $crv['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
                $collectdata = getusercollectlists($v['goods_id']);
                if ($collectdata) {
                    $crv['collectdata'] = $collectdata['data'];
                    $crv['likecount'] = $collectdata['total'];
                } else {
                    $crv['collectdata'] = [];
                    $crv['likecount'] = '0';
                }
                if (!empty($v['nickname'])) {
                    $crv['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                    $crv['nickname'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['nickname']);
                }
                if (!empty($v['goods_name'])) {
                    $crv['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                    $crv['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                    $crv['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
                }
                if (!empty($v['goods_content'])) {
                    $crv['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                    $crv['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                    $crv['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                    $crv['goods_content'] = $this->ihtmlspecialchars($v['goods_content']);
                }
                $imgdata = Db::name('GoodsImages')->field('goods_id,rescourse_id')
                    ->where(['goods_id' => $v['goods_id']])
                    ->whereNull('delete_time')
                    ->order('img_id')
                    ->cache(true)
                    ->limit(9)
                    ->select();
                foreach ($imgdata as $vs) {
                    $crv['img'][]=    ['img' => $vs['rescourse_id']];
                }
                cachecatchset($v['goods_id'], $hot_goods[$key], 'getproductsforpaimaiquangoodsimagesyyyyy', 100);
            } else {
//                $crv['timelevel'] = self::memberLevel($currnet, $v['reg_time']);
//                $tv['endTimeflag'] = ($currnet >= $v['endTime']) ? '1' : '0';
//                $tv['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
//                $tv['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
                $bidorderdata = $bidorder->where(['goods_id' => $tv['goods_id'], 'upload_time' => $tv['upload_time']])->max('bid_price');
                $tv['click_count'] = $goodso->where('goods_id', $tv['goods_id'])->value('click_count');
                $tv['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $tv['start_price']) {
                    $tv['lastnum'] = (string)$tv['start_price'];
                } else {
                    $tv['lastnum'] = (string)$bidorderdata;
                }
                $tv['bidlists'] = $BidOrderServer->listsarray($v['goods_id'], 4);
                $tv['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
                $tv['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
                $collectdata = getusercollectlists($tv['goods_id']);
                if ($collectdata) {
                    $tv['collectdata'] = $collectdata['data'];
                    $tv['likecount'] = $collectdata['total'];
                } else {
                    $tv['collectdata'] = [];
                    $tv['likecount'] = '0';
                }
                $hot_goods[$key] = $tv;
            }
        }
        return ['data' => $hot_goods];
    }

//获取用户下面所有的产品
    /*
     * $type=1 获取当前用户下面的作品   $type=2 获取关注的人或者关注我的人
     *
     */
    public function getProductsForpaimaiquanm(int $limit, int $user_id = 0, int $type = 1, int $self_user_id = 0, int $goods_id, int $page = 1)
    {
        $start = ($page - 1) * $limit;
        $limit = 'limit ' . $start . ',' . $limit;
        $where = 'where g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'];
        $order = ' order by u.user_level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';
        if ($goods_id > 0) {
            $where = 'where g.goods_id=' . $goods_id;
            $order = '';
            //显示用户官网数据
            $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
        } else {
            if ($user_id > 0) {
                $where = 'where  g.user_id=' . $user_id . ' and g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                $order = ' order by g.upload_time DESC,u.user_level DESC,u.weight_sale DESC,g.cat_id DESC';
                //显示用户官网数据
                $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
            } else {
                if ($self_user_id > 0) {
                    $user_id = $self_user_id;
                    $where = 'where g.endTime>' . $_SERVER['REQUEST_TIME'] . '  and u.delete_time is null and g.delete_time is null';
                    $order = ' group by g.goods_id  order by g.upload_time DESC ,ee.add_time DESC';
                    $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  INNER JOIN  (SELECT fans_id as user_id,add_time FROM `tp_fans_collect` WHERE `user_id` = {$user_id}  and delete_time is Null  UNION SELECT user_id,add_time FROM tp_fans_collect where fans_id={$user_id}  and delete_time is Null  union select {$user_id} as user_id ,44555 as add_time ) as ee on ee.user_id= g.user_id {$where}  {$order} {$limit}");
                } else {
                    $order = 'group by u.user_id  order by g.upload_time DESC';
                    $where = 'where g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                    //显示用户官网数据
                    $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
                }
            }
        }


        $hot_goods['data'] = &$hot_goodst;
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $Goodsobj = new   Goods();
        foreach ($hot_goods['data'] as $key => &$v) {
            //通过id再查
            $Goods = $Goodsobj->where('user_id', $v['user_id'])->limit(5)->select();
            if (!empty($Goods)) {
                foreach ($Goods as $skey => $sv) {
                    if ($sv['goods_id'] != $v['goods_id']) {
                        $hot_goods['data'][$key]['plists'][] = $this->commprocressproduct($Goods, $skey, $sv, $aliiamgobj);
                    }
                }
            }

            unset($v['password']);
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = $this->ihtmlspecialchars($v['goods_content']);
                $v['goods_content'] = mb_substr($v['goods_content'], 0, 19);
            }
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(1)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', 31536000],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        return $hot_goods;
    }


    function commprocressproduct($subdata, $key, $v, $aliiamgobj)
    {
        if (!empty($v['goods_name'])) {
            $newtemp['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
            $newtemp['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $newtemp['goods_name']);
        }
        if (!empty($v['goods_content'])) {
            $newtemp['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
            $newtemp['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $newtemp['goods_content']);
            $newtemp['goods_content'] = $this->ihtmlspecialchars($newtemp['goods_content']);
            $newtemp['goods_content'] = mb_substr($newtemp['goods_content'], 0, 10);
        }
        $newtemp['goods_id'] = $v['goods_id'];
        $imgdataobj = Db::name('GoodsImages');
        $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
            ->where(['goods_id' => $v['goods_id']])
            ->whereNull('delete_time')
            ->cache(true)
            ->order('img_id')
            ->limit(1)
            ->select();

        foreach ($imgdata as $vs) {
            if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                $newtemp['img'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote'])];
                $newtemp['nowaterimg'][] = ['img' => str_replace(Config::get('aliyun.aliyundomain'), Config::get('aliyun.cdntianbao'), $vs['image_url_remote_nowater'])]??'';
            } else {
                try {
                    $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                    $newtemp['img'][] = ['img' => $img];
                    $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                    $newtemp['nowaterimg'][] = ['img' => $nowaterimg];
                    $imgdataobj->where('img_id', $vs['img_id'])
                        ->update([
                            'image_url_remote' => $img,
                            'image_url_remote_expire' => ['exp', 31536000],
                            'image_url_remote_nowater' => $nowaterimg
                        ]);
                } catch (Exception $e) {
                    $newtemp['img'] = [];
                    $newtemp['nowaterimg'] = [];
                }
            }
        }
        return $newtemp;

    }


    function ihtmlspecialchars($string)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->ihtmlspecialchars($val);
            }
        } else {
            $string = preg_replace('/&amp;((#(d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&1',
                str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
        }
        return $string;
    }

//获取用户下面所有的产品
    public function getProductsOneByProductId(int $productid, int $userid)
    {
        if ($productid === 0 || $userid === 0) {
            return null;
        }
        $hot_goods = Db::name('Goods')->where('goods_id', $productid)->field('goods_id,endTime,upload_time,start_price,every_add_price,reserveprice,goods_name,goods_content,cat_id,contact_wx,contact_mobile')->limit(1)->select();
        $aliiamgobj = new AliyunOss(false);
        $currnet = $_SERVER['REQUEST_TIME'];
        $imgdataobj = Db::name('GoodsImages');
        foreach ($hot_goods as $key => &$v) {
            $v['endTimeunixtime'] = $v['endTime'];
            $v['endTime'] = $v['endTime'] ? date('m月d日 H', $v['endTime']) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            $imgdata = $imgdataobj->field('img_id,image_url_remote_expire,image_url_remote,rescourse_id,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->select();
            $datetime = new \DateTime('now');
            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $updatelist = [];
            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods[$key]['img'][] = ['img' => $vs['image_url_remote'], 'rescourse_id' => $vs['rescourse_id']];
                    $hot_goods[$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater'], 'rescourse_id' => $vs['rescourse_id']];
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['img'][] = ['img' => $img, 'rescourse_id' => $vs['rescourse_id']];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['nowaterimg'][] = ['img' => $nowaterimg, 'rescourse_id' => $vs['rescourse_id']];

                        $updatelist[] =
                            [
                                'img_id' => $vs['img_id'],
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ];
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
            //图片一次性更新
            if (!empty($updatelist)) {
                $GoodsImageso = new     GoodsImages();
                $GoodsImageso->saveAll($updatelist);
            }
        }
        return current($hot_goods);
    }


    //获取用户作品id
    public function getUserProductds(int $user_id)
    {
        $Goods = new     Goods();
        $Goodsdata = $Goods->where('user_id', $user_id)->column('goods_id');
        return $Goodsdata;
    }


    public function getOne(int $good_ids)
    {
        $Goods = new  Goods();
        $Goodsdata = $Goods->where('goods_id', $good_ids)->column('click_count');
        return $Goodsdata;
    }

    //获取一个作品
    public function getOneProduct(int $good_id, $filed = '*')
    {
        $Goods = new  Goods();
        $Goodsdata = $Goods->where('goods_id', $good_id)->field($filed)->find();
        return $Goodsdata;
    }

//获取某个产品下面信息
    public function bidGetProductById(int $good_id, bool $is_final_payment = false, int $userid = 0, int $order_id = 0, string $order_sn = '')
    {

        $imgdataobj = Db::name('GoodsImages');
        $hot_goods = Db::name('Goods')
            ->where('goods_id', $good_id)
            ->whereNull('delete_time')
            ->order('goods_id DESC')
            ->limit(1)
            ->select();

        $datetime = new \DateTime('now');
        $datetime->modify('+31536000 second');
        $expritetime = $datetime->getTimestamp();
        $imgdataobj = Db::name('GoodsImages');
        $bidorder = new BidOrder();


        foreach ($hot_goods as &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            if (mb_strlen($v['goods_content'], 'utf8') > 50) {
                $v['goods_content'] = htmlspecialchars_decode($v['goods_content']);

                $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8') . '....';
            } else {
                $v['goods_content'] = mb_substr($v['goods_content'], 0, 50, 'utf8');
            }
            $bid_price = 0.00;
            $v['order_sn'] = '';


            if ($is_final_payment === true && $userid > 0) {
                $bidorderdata = Db::name('order')->where(['order_sn' => $order_sn, 'user_id' => $userid])->field('order_amount,order_sn,user_id')->find();
                if (is_null($bidorderdata)) {
                    return [];
                }
                if ($userid == $bidorderdata['user_id']) {
                    //说明是本人现在是最高价
                    $v['bid_price'] = $bidorderdata['order_amount'];
                    $v['order_sn'] = $bidorderdata['order_sn'];
                } else {
                    return [];
                }

            } else {
                $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                    $v['bid_price'] = bcadd($v['start_price'], $v['every_add_price'], 2);
                } else {
                    $v['bid_price'] = bcadd($bidorderdata, $v['every_add_price'], 2);
                }
            }
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            $tempimagedata = $imgdataobj->field('min(img_id),img_id,goods_id,rescourse_id,image_url_remote,image_url_remote_expire,image_url_remote_nowater,enpiount')
                ->where('goods_id', $v['goods_id'])
                ->whereNull('delete_time')
                ->group('goods_id')
                ->limit(1)
                ->find();


            if ($tempimagedata['image_url_remote'] != '' && $tempimagedata['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                $v['img'] = $tempimagedata['image_url_remote'];
                $v['image_url_remote_nowater'] = $tempimagedata['image_url_remote_nowater'];
            } else {
                if (!empty($tempimagedata['rescourse_id'])) {
                    try {
                        $v['img'] = (new AliyunOss(false))->getCurlofimg(Config::get('aliyun.bucket'), $tempimagedata['rescourse_id'], 31536000);
                        $nowaterimg = (new AliyunOss(false))->nomatergetCurlofimg(Config::get('aliyun.bucket'), $tempimagedata['rescourse_id'], 31536000);
                        $imgdataobj->where('img_id', $tempimagedata['img_id'])
                            ->update([
                                'image_url_remote' => $v['img'],
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $v['img'] = '';
                    }
                } else {
                    $v['img'] = '';
                }
            }
        }
        return isset($hot_goods[0]) ? $hot_goods[0] : [];
    }



//获取用户下面所有的产品
    /*
     * $type=1 获取当前用户下面的作品   $type=2 获取关注的人或者关注我的人
     *
     */
    public function getStaticProductsForpaimaiquan(int $limit, int $user_id = 0, int $type = 1, int $self_user_id = 0, int $goods_id, string $nickname, string $mobile, int $page = 1)
    {
        $start = ($page - 1) * $limit;
        $limit = 'limit ' . $start . ',' . $limit;
        $where = 'where g.delete_time is null and g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'];
        $order = ' order by u.user_level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';
        $where = 'where 1=1 ';
        if ($mobile != '') {
            $where .= ' and mobile like ' . "'%" . $mobile . "%'";
        }
        if ($nickname != '') {
            $where .= ' and nickname like ' . "'%" . $nickname . "%'";
        }
        if ($where != 'where 1=1 ') {
            $uer_data = Db::query("SELECT user_id FROM tp_users  {$where} limit 1");
        }
        if (isset($uer_data[0]) && $user_id == 0) {
            $user_id = $uer_data[0]['user_id'];
        } elseif ($user_id == 0) {
            $user_id = 0;
        }
        if ($goods_id > 0) {
            $order = '';
            //显示用户官网数据
            $hot_goods = Db::query("SELECT u.user_id,u.nickname,u.user_name,u.head_pic,u.is_authentication,u.nickname,u.user_level,u.fictitious,u.reg_time,g.* FROM tp_users u left JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
        } else {
            if ($user_id > 0 && $self_user_id == 0) {
                $where = 'where g.delete_time is null and  g.user_id=' . $user_id;
                $order = ' order by g.upload_time DESC,u.user_level DESC,u.weight_sale DESC,g.cat_id DESC';
                //显示用户官网数据
                $hot_goods = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");

            } else {
                //查看用户拍卖圈
                if ($self_user_id > 0 && $user_id > 0) {
                    $user_id = $user_id;
                    $where = 'where 1=1  and g.delete_time is null ';
                    $order = ' order by g.upload_time DESC ,ee.add_time DESC';
                    $hot_goods = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  INNER JOIN  (SELECT fans_id as user_id,add_time FROM `tp_fans_collect` WHERE `user_id` = {$user_id}  UNION SELECT user_id,add_time FROM tp_fans_collect where fans_id={$user_id} union select {$user_id} as user_id ,44555 as add_time ) as ee on ee.user_id= g.user_id {$where}  {$order} {$limit}");
                } else {

                    return null;
                }
            }
        }


        $hot_goods['data'] = $hot_goods;
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        foreach ($hot_goods['data'] as $key => &$v) {
            unset($v['password']);
            $v['timelevel'] = MemberLimit::memberLevel($currnet, $v['reg_time']);
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';

            $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                $v['lastnum'] = (string)$v['start_price'];

            } else {
                $v['lastnum'] = (string)$bidorderdata;
            }

            $v['bidlists'] = $BidOrderServer->listsarray($v['goods_id'], 15);
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';

            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                $v['nickname'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['nickname']);
            }

            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = $this->ihtmlspecialchars($v['goods_content']);
            }


            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $hot_goods['data'][$key]['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->cache(true)
                ->order('img_id')
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        return $hot_goods;
    }


//获取用户下面所有的产品
    /*
     * $type=1 获取当前用户下面的作品   $type=2 获取关注的人或者关注我的人
     *
     */
    public function getProductsForpaimaiquanCase(int $limit, int $user_id = 0, int $type = 1, int $self_user_id = 0, int $goods_id, int $page = 1, Request $request)
    {

        $caseserver = new CaseServer();
        //直接出价
        $caseserver->CaseUserByEndtime($request);
        $start = ($page - 1) * $limit;
        $limit = 'limit ' . $start . ',' . $limit;
        $where = 'where 1=1';
        $order = ' order by u.level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';

        if ($goods_id > 0) {
            $where = 'where g.goods_id=' . $goods_id;
            $order = '';
            //显示用户官网数据
            $hot_goods = Db::query("SELECT u.user_id,u.nickname,u.user_name,u.head_pic,u.is_authentication,u.nickname,u.level,u.fictitious,u.reg_time,g.* FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
        } else {
            if ($user_id > 0) {
                $where = 'where g.delete_time is null and g.user_id=' . $user_id . ' and g.endTime>' . $_SERVER['REQUEST_TIME'];
                $order = ' order by g.upload_time DESC,u.level DESC,u.weight_sale DESC,g.cat_id DESC';
                //显示用户官网数据
                $hot_goods = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
            } else {
                if ($self_user_id > 0) {
                    $user_id = $self_user_id;
                    $where = 'where 1=1 and g.delete_time is null  and g.endTime>' . $_SERVER['REQUEST_TIME'];
                    $order = ' group by g.goods_id  order by g.upload_time DESC ,ee.add_time DESC';
                    $hot_goods = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  INNER JOIN  (SELECT fans_id as user_id,add_time FROM `tp_fans_collect` WHERE `user_id` = {$user_id}  UNION SELECT user_id,add_time FROM tp_fans_collect where fans_id={$user_id} union select {$user_id} as user_id ,44555 as add_time ) as ee on ee.user_id= g.user_id {$where}  {$order} {$limit}");
                } else {
                    $where = 'where g.delete_time is null and  g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'];
                    //显示用户官网数据
                    $hot_goods = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
                }
            }
        }
        $hot_goods['data'] = $hot_goods;
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        foreach ($hot_goods['data'] as $key => &$v) {
            unset($v['password']);
            $v['timelevel'] = MemberLimit::memberLevel($currnet, $v['reg_time']);
            //  $v['click_count'] =   $v['click_count'] +300;
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';

            $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                $v['lastnum'] = (string)$v['start_price'];

            } else {
                $v['lastnum'] = (string)$bidorderdata;
            }

            $v['bidlists'] = $BidOrderServer->listsarray($v['goods_id'], 15);
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';

            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                $v['nickname'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['nickname']);
            }

            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = mb_ereg_replace('([壹贰叁肆伍陆柒捌玖一二三四五六七八九十\d]{5,})', '****', $v['goods_content']);
                $v['goods_content'] = $this->ihtmlspecialchars($v['goods_content']);
            }


            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $hot_goods['data'][$key]['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $hot_goods['data'][$key]['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->cache(true)
                ->order('img_id')
                ->limit(9)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        return $hot_goods;
    }


    public function getBondlists($goods_id, int $limit, $flag = false, array $config = ['page' => 1])
    {
        $BidOrderServerobj = new BidOrderServer();
        $bidlists = $BidOrderServerobj->listsarrays($goods_id, $limit, $flag, $config);
        return $bidlists;
    }

    public function getBondlistsbyuserid($user_id, int $limit, $flag = false, array $config = ['page' => 1])
    {
        $BidOrderServerobj = new BidOrderServer();
        $bidlists = $BidOrderServerobj->listsarraysbyuserid($user_id, $limit, $flag, $config);
        return $bidlists;
    }



    //获取用户作品id
    public function getProductds(): int
    {
        $Goods = new  Goods();
        $today = Carbon::today();
        $base_goods = cachecatchg('getProductds', $today);
        if ($base_goods === false || (int)$base_goods === 0) {
            $base_goods = mt_rand(3000, 5000);
            cachecatchset($today, $base_goods, 'getProductds');
        }
        $unixstamp = strtotime($today);
        $Goodsdata = $Goods->where('upload_time', '>', $unixstamp)->count('goods_id');
        return intval($Goodsdata + $base_goods);
    }


//获取用户下面所有的产品
    /*
     * $type=1 获取当前用户下面的作品   $type=2 获取关注的人或者关注我的人
     *
     */
    public function getProductsForJinpin(int $limit, int $user_id = 0, int $type = 1, int $self_user_id = 0, int $goods_id, int $page = 1)
    {


        $start = ($page - 1) * $limit;
        $limit = 'limit ' . $start . ',' . $limit;
        $where = 'where g.is_toplatform =0 and g.endTime>' . $_SERVER['REQUEST_TIME'];
        $order = ' order by u.level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';
        if ($goods_id > 0) {
            $where = 'where g.goods_id=' . $goods_id;
            $order = '';
            //显示用户官网数据
            $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
        } else {
            if ($user_id > 0) {
                $where = 'where  g.user_id=' . $user_id . ' and g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                $order = ' order by g.upload_time DESC,u.level DESC,u.weight_sale DESC,g.cat_id DESC';
                //显示用户官网数据
                $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
            } else {
                if ($self_user_id > 0) {
                    $user_id = $self_user_id;
                    $where = 'where g.endTime>' . $_SERVER['REQUEST_TIME'] . '  and u.delete_time is null and g.delete_time is null';
                    $order = ' group by g.goods_id  order by g.upload_time DESC ,ee.add_time DESC';
                    $hot_goodst = Db::query("SELECT * FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  INNER JOIN  (SELECT fans_id as user_id,add_time FROM `tp_fans_collect` WHERE `user_id` = {$user_id}  and delete_time is Null  UNION SELECT user_id,add_time FROM tp_fans_collect where fans_id={$user_id}  and delete_time is Null  union select {$user_id} as user_id ,44555 as add_time ) as ee on ee.user_id= g.user_id {$where}  {$order} {$limit}");
                } else {
                    $where = 'where  g.is_goods =1 and g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
                    //显示用户官网数据
                    $hot_goodst = Db::query("SELECT u.reg_time,g.endTime,g.start_price,g.upload_time,u.nickname,g.goods_name,g.goods_content,g.goods_id,g.click_count FROM tp_users u INNER JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");
                }
            }
        }


        $hot_goods['data'] = &$hot_goodst;
        $bidorder = new BidOrder();
        $BidOrderServer = new BidOrderServer();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $timev = 0.00;
        $expritetime = 0;
        $imgdataobj = null;
        foreach ($hot_goods['data'] as $key => &$v) {
            $v['timelevel'] = MemberLimit::memberLevel($currnet, $v['reg_time']);
            $v['endTimeflag'] = $currnet >= $v['endTime'] ? '1' : '0';
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                $v['nickname'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['nickname']);
            }
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = htmlspecialchars_decode($v['goods_content']);
            }

            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $v['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $v['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(false)
                ->limit(1)
                ->select();
            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', 31536000],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        return $hot_goods;
    }

    public static function randone(int $min, int $max)
    {
        $goods_id = mt_rand($min, $max);
        $goods_flag = Db::name('goods')->where('goods_id', $goods_id)->where('goods_status', 1)->whereNull('delete_time')->value('goods_id');
        if (!$goods_flag) {
            return self::randone($min, $max);
        }
        return $goods_flag;
    }


    //随机获取一件作品
    public function getProductRandOne(int $limit, int $page = 1, $memberSharkedCount, $userid)
    {
        //取出作品id
        $mingoods_id = Db::name('goods')->cache(true)->min('goods_id');
        $maxgoods_id = Db::name('goods')->cache(true)->max('goods_id');
        $goods_id = self::randone($mingoods_id, $maxgoods_id);
        //先查询redis cache
        $hot_goods = cachecatchg($goods_id . '_' . $page, 'getProductRandOne');
        if (!empty($hot_goods)) {
            $BidOrderServerobj = new BidOrderServer();
            $bidorder = new BidOrder();
            foreach ($hot_goods as $key => &$v) {
                $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => strtotime($v['upload_time'])])->max('bid_price');
                if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                    $v['lastnum'] = (string)$v['start_price'];
                } else {
                    $v['lastnum'] = (string)$bidorderdata;
                }
                $collectdata = getusercollectlists($v['goods_id']);
                if ($collectdata) {
                    $v['collectdata'] = $collectdata['data'];
                    $v['likecount'] = $collectdata['total'];
                } else {
                    $v['collectdata'] = [];
                    $v['likecount'] = '0';
                }
            }
            return $hot_goods;
        }
        $hot_goods = Db::name('Goods')
            ->alias('a')
            ->join('users q', 'a.user_id=q.user_id and a.goods_id=' . $goods_id)
            ->whereNull('a.delete_time');
        $hot_goods = $hot_goods->field('distinct(a.goods_id),a.endTime,a.start_price,a.every_add_price,a.reserveprice,a.goods_id,a.upload_time,a.goods_name,a.goods_content,q.nickname,q.reg_time,q.user_id,q.head_pic')
            ->order('a.goods_id desc')
            ->limit($limit)
            ->select();
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $BidOrderServerobj = new BidOrderServer();
        $bidorder = new BidOrder();
        foreach ($hot_goods as $key => &$v) {
            $v['endTime'] = $v['endTime'] ? date('Y-m-d H:i:s', $v['endTime']) : '';
            $v['timelevel'] = MemberLimit::memberLevel($_SERVER['REQUEST_TIME'], $v['reg_time']);
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['every_add_price'] = $v['every_add_price'] ? number_format($v['every_add_price'], 2) : '';
            $v['reserveprice'] = $v['reserveprice'] ? number_format($v['reserveprice'], 2) : '';
            $v['pagenow'] = 1;
            $bidorderdata = $bidorder->where(['goods_id' => $v['goods_id'], 'upload_time' => $v['upload_time']])->max('bid_price');
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (empty($bidorderdata) || $bidorderdata == 0 || $bidorderdata < $v['start_price']) {
                $v['lastnum'] = (string)$v['start_price'];
            } else {
                $v['lastnum'] = (string)$bidorderdata;
            }
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = mb_substr($v['goods_name'], 0, 10);
            }
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = htmlspecialchars_decode($v['goods_content']);
            }
            //剩余次数
            $today = Carbon::today();
            $ssskey = $today->toDateTimeString();


            $v['remaintimes'] = cachecatchsdec('productrandone' . $userid . $ssskey, $memberSharkedCount, 86410);
            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $v['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $v['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $datetime->modify('+31536000 second');
            $expritetime = $datetime->getTimestamp();
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('*')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->limit(1)
                ->select();

            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods[$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods[$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']];
                } else {
                    try {
                        $nomasterimg = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['img'][] = ['img' => $nomasterimg];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods[$key]['nowaterimg'][] = ['img' => $nowaterimg];

                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $nomasterimg,
                                'image_url_remote_expire' => ['exp', $expritetime],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);

                    } catch (Exception $e) {
                        $hot_goods[$key]['img'] = [];
                        $hot_goods[$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        cachecatchset($goods_id . '_' . $page, $hot_goods, 'getProductRandOne', 60);
        return $hot_goods;
    }



    //会员时间等级
    public static function memberLevel(int $current,int $unixstamp,int $fictitious=0):int
    {
        return   MemberLimit::memberLevel($current,$unixstamp,$fictitious);
    }



//获取推广的商品区
    /*

     */
    public function getProductsForExtension(int $limit, int $type = 1, int $page = 1)
    {
        $start = ($page > 1) ? ($page - 1) * $limit : 0;
        $limit = 'limit ' . $start . ',' . $limit;
        $order = ' order by u.level DESC,u.weight_sale DESC,g.cat_id DESC,g.upload_time DESC';
        $where = 'where is_recommend =1 and   g.endTime>' . $_SERVER['REQUEST_TIME'] . ' and g.delete_time is null';
        //g.is_goods =1 and
        //显示用户官网数据
        $hot_goodst = Db::query("SELECT u.reg_time,g.endTime,g.start_price,g.upload_time,u.nickname,g.goods_name,g.goods_content,g.goods_id,g.click_count FROM tp_users u right JOIN `tp_goods` `g` ON `g`.`user_id`=`u`.`user_id`  {$where} {$order}  {$limit}");


        $hot_goods['data'] = &$hot_goodst;
        $aliiamgobj = new AliyunOss(false);
        $datetime = new \DateTime('now');
        $currnet = $_SERVER['REQUEST_TIME'];
        $timev = 0.00;
        $expritetime = 0;
        $imgdataobj = null;
        $dt = Carbon::now();
        $today = $dt->toDateString();
        $dt->addDay(1);
        $tomorrow = $dt->toDateString();
        $dt->addDay(1);
        $aftertomorrow = $dt->toDateString();
        foreach ($hot_goods['data'] as $key => &$v) {
            $v['endTimeflag'] = $currnet >= $v['endTime'] ? '1' : '0';
            $dt->timestamp = $v['endTime'];
            $tmps = $v['endTime'];
            $v['endTime'] = $tmps ? date('Y-m-d', $tmps) : '';
            $v['endTimeH'] = $tmps ? date('H', $tmps) : '';
            $endtime = $dt->toDateString();//结束时间

            //计算时间格式   今天 明天  后天
            if ($endtime === $today) {
                $v['endTimestr'] = '今' . $v['endTimeH'] . '点止';
            } elseif ($endtime === $tomorrow) {
                $v['endTimestr'] = '明天' . $v['endTimeH'] . '点止';
            } elseif ($aftertomorrow === $endtime) {
                $v['endTimestr'] = '后天' . $v['endTimeH'] . '点止';
            } else {
                $v['endTimestr'] = '大后天' . $v['endTimeH'] . '点止';
            }
            $v['start_price'] = $v['start_price'] ? number_format($v['start_price'], 2) : '';
            $v['upload_time'] = $v['upload_time'] ? date('Y-m-d H:i:s', $v['upload_time']) : '';
            if (!empty($v['nickname'])) {
                $v['nickname'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['nickname']);
                $v['nickname'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['nickname']);
            }
            if (!empty($v['goods_name'])) {
                $v['goods_name'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_name']);
                $v['goods_name'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_name']);
                $v['goods_name'] = mb_substr($v['goods_name'], 0, 10);
            }
            if (!empty($v['goods_content'])) {
                $v['goods_content'] = preg_replace('#(\d{3})\d{5}(\d{3})#', '${1}*****${2}', $v['goods_content']);
                $v['goods_content'] = preg_replace('/([a-zA-Z\d_]{5,})/', '****', $v['goods_content']);
                $v['goods_content'] = htmlspecialchars_decode($v['goods_content']);
            }

            $collectdata = getusercollectlists($v['goods_id']);
            if ($collectdata) {
                $v['collectdata'] = $collectdata['data'];
                $v['likecount'] = $collectdata['total'];
            } else {
                $v['collectdata'] = [];
                $v['likecount'] = '0';
            }
            $imgdataobj = Db::name('GoodsImages');
            $imgdata = $imgdataobj->field('img_id,goods_id,image_url,delete_time,rescourse_id,enpiount,image_url_remote,image_url_remote_expire,image_url_remote_nowater')
                ->where(['goods_id' => $v['goods_id']])
                ->whereNull('delete_time')
                ->order('img_id')
                ->cache(true)
                ->limit(1)
                ->select();
            foreach ($imgdata as $vs) {
                if ($vs['image_url_remote'] != '' && $vs['image_url_remote_expire'] > $_SERVER['REQUEST_TIME']) {
                    $hot_goods['data'][$key]['img'][] = ['img' => $vs['image_url_remote']];
                    $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $vs['image_url_remote_nowater']]??'';
                } else {
                    try {
                        $img = $aliiamgobj->getCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['img'][] = ['img' => $img];
                        $nowaterimg = $aliiamgobj->nomatergetCurlofimg(Config::get('aliyun.bucket'), $vs['rescourse_id'], 31536000);
                        $hot_goods['data'][$key]['nowaterimg'][] = ['img' => $nowaterimg];
                        $imgdataobj->where('img_id', $vs['img_id'])
                            ->update([
                                'image_url_remote' => $img,
                                'image_url_remote_expire' => ['exp', 31536000],
                                'image_url_remote_nowater' => $nowaterimg
                            ]);
                    } catch (Exception $e) {
                        $hot_goods['data'][$key]['img'] = [];
                        $hot_goods['data'][$key]['nowaterimg'] = [];
                    }
                }
            }
        }
        return $hot_goods;
    }

    //添加浏览
    public  function  addclick(int $goods_id){
        return   Db::name('goods')->where('goods_id' ,$goods_id)->update(['click_count' => ['exp', 'click_count+1']]);
    }
}