<?php
namespace app\api\behavior;

use think\Request;
use think\Response;
use Carbon\Carbon;

/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class MemberLimit
{
    //会员时间等级
    public function memberTime(int $member_level):int
    {
        $count = 24;
        switch ($member_level) {
            case 1:
                $count = 24;
                break;
            case 2:
                $count = 36;
                break;
            case 3:
                $count = 48;
                break;
            case 4:
                $count = 60;
                break;
            case 5:
                $count = 72;
                break;
            default:
                return $count;
        }
        return $count;
    }


    //会员时间等级
    public function memberProductCount(int $member_level)
    {
        $count = 0;
        switch ($member_level) {
            case 1:
                $count = 3;
                break;
            case 2:
                $count = 30;
                break;
            case 3:
                $count = 80;
                break;
            case 4:
                $count = 300;
                break;
            case 5:
                $count = 600;
                break;
            default:
                return;
        }
        return $count;
    }



    //会员时间等级
    public function membeFansCount(int $member_level)
    {
        $count = 0;
        switch ($member_level) {
            case 1:
                $count = 80;
                break;
            case 2:
                $count = 300;
                break;
            case 3:
                $count = 600;
                break;
            case 4:
                $count = 1600;
                break;
            case 5:
                $count = 5000;
                break;
            default:
                return;
        }
        return $count;
    }


    //会员时间等级
    public function tmpPembeFansCount(int $member_level)
    {
        $count = 0;
        switch ($member_level) {
            case 1:
                $count = 80;
                break;
            case 2:
                $count = 100;
                break;
            case 3:
                $count = 150;
                break;
            case 4:
                $count = 1000;
                break;
            case 5:
                $count = 1200;
                break;
            default:
                return;
        }
        return $count;
    }


    //会员时间等级
    public function memberSendActiveCount(int $member_level)
    {
        $count = 1;
        switch ($member_level) {
            case 1:
                $count = 1;
                break;
            case 2:
                $count = 2;
                break;
            case 3:
                $count = 5;
                break;
            case 4:
                $count = 7;
                break;
            case 5:
                $count = 10;
                break;
            default:
                return $count;
        }
        return $count;
    }



    //会员时间等级
    public function lastPembeFansCount(int $member_level)
    {
        $count = 0;
        switch ($member_level) {
            case 1:
                $count = mt_rand(3,5);
                break;
            case 2:
                $count = mt_rand(3,8);
                break;
            case 3:
                $count = mt_rand(3,10);
                break;
            case 4:
                $count = mt_rand(4,11);
                break;
            case 5:
                $count = mt_rand(5,12);
                break;
            default:
                return mt_rand(3,5);
        }
        return $count;
    }



    //会员时间等级  $fictitious 1是虚拟用户 0是正常用户
    public static function memberLevel(int $current,int $unixstamp,int $fictitious=0):int
    {
        $member_level= 1;
        if($unixstamp>0){
            $stancetime = $current-$unixstamp;
            $timelevel = floor($stancetime/2592000);
            if($unixstamp <config('userregisterunixstamp.timestamp')){
                if ($timelevel <= 8) {
                    return $timelevel;
                }else{
                        //在指定时间之后的
                    $timelevel = 34+ $timelevel-8;
                }
            }
        }else{
            return $member_level;
        }
        $timelevel =intval($timelevel);
        if($timelevel<3){
            $member_level =1;
        }elseif($timelevel<6){
            $member_level =2;
        }elseif($timelevel<9){
            $member_level =3;
        }elseif($timelevel<13){
            $member_level =4;
        }elseif($timelevel<16){
            $member_level =5;
        }elseif($timelevel<19){
            $member_level =6;
        } elseif ($timelevel < 33) {
            $member_level = 7;
        } elseif ($timelevel < 42) {
            $member_level = 8;
        } elseif ($timelevel < 56) {
            $member_level = 9;
        } elseif ($timelevel >= 56) {
            $member_level = 10;
        }
        return $member_level;
    }


    //会员时间等级
    public function sharkedMemberTimes(int $memberlevel)
    {
        $count = 0;
        switch ($memberlevel) {
            case 1:
                $count = 0;
                break;
            case 2:
                $count = 2;
                break;
            case 3:
                $count =3;
                break;
            case 4:
                $count = 4;
                break;
            case 5:
                $count = 5;
                break;
            default:
                return 0;
        }
        return $count;
    }


}
