<?php
namespace app\dataapi\lib;

use think\Config;
use think\Db;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16 0016
 * Time: 18:54
 */
class Image
{
    public function flagimage(int $level = 0)
    {
        $configdata = Config::get('vhostmember');
        $unixstamp = $configdata['membertime']['unixstamp'];
        if ($level > 0) {
            $datauser = Db::name('users')->where(['fictitious' => 1, 'level' => $level])->where('reg_time', '>', $unixstamp)->whereNull('delete_time')->field('user_id,head_pic')->order('rand()')->find();
            //return $datauser;
        } else {
            $maxid =    Db::table( $configdata['membertime']['membername'])->max('id');
            $ttmaxid =  mt_rand(1,$maxid);
            $datauser = Db::table( $configdata['membertime']['membername'])->where('id','>',$ttmaxid)->field('user_id,head_pic')->limit(1)->find();
            $datauser['store_level'] = Db::name('users')->where(['user_id' => $datauser['user_id']])->value('store_level');
            return $datauser;
        }
        $flag = $this->imagetest($datauser['head_pic']);
        if (!$flag or !isset($flag['width']) or !isset($flag['height']) or ($flag['width'] == 0 or $flag['height'] == 0)) {
            return $this->flagimage();
        }
        return $datauser;
    }


    public function imagetest($url)
    {
        if (empty($url)) {
            return false;
        }
        $FastImageSize = new \FastImageSize\FastImageSize();
        $imageSize = $FastImageSize->getImageSize($url);

        unset($FastImageSize);
        return $imageSize;
    }

}