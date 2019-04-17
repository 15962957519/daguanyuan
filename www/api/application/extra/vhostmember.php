<?php
/**
 * Created by PhpStorm.
 * User: ericssonon
 * Date: 2017/6/9
 * Time: 13:00
 */
return [
    'membertime' => [
        'unixstamp' => 1497574800,
        'upgradestamp' => 2592000,
        'membername' => 'tp_users_20180317',
        'cacseuserid' =>4145
    ],
    'alimqconfig'=>[
        'Topic'=>'liulan01',
        'URL'=>'http://hangzhou-rest-internet.ons.aliyun.com',
        'Ak'=>'LTAIK8hGVmhUlOd5',
        'Sk'=>'XzNA8EBLMuH9SKQZqwdDa0dyDb3TOG',
        'ProducerID'=>'PID_liulanandchujia',
        'ConsumerID'=>'CID_liulan',
    ],
    'alimqconfigfensi'=>[
        'Topic'=>'fanserver',
        'URL'=>'http://hangzhou-rest-internet.ons.aliyun.com',
        'Ak'=>'LTAIK8hGVmhUlOd5',
        'Sk'=>'XzNA8EBLMuH9SKQZqwdDa0dyDb3TOG',
        'ProducerID'=>'PID_fanserver',
        'ConsumerID'=>'CID_fanserver',
    ],
    'alimqconfigbid'=>[
        'Topic'=>'bid',
        'URL'=>'http://hangzhou-rest-internet.ons.aliyun.com',
        'Ak'=>'LTAIK8hGVmhUlOd5',
        'Sk'=>'XzNA8EBLMuH9SKQZqwdDa0dyDb3TOG',
        'ProducerID'=>'PID_bidserver',
        'ConsumerID'=>'CID_bid',
    ],
    'alimqconfig48remind'=>[
        'Topic'=>'weixinremind',
        'URL'=>'http://hangzhou-rest-internet.ons.aliyun.com',
        'Ak'=>'LTAIK8hGVmhUlOd5',
        'Sk'=>'XzNA8EBLMuH9SKQZqwdDa0dyDb3TOG',
        'ProducerID'=>'PID_weixinremind',
        'ConsumerID'=>'CID_weixinremind',
    ],
    'consureurl'=>
    [
       'url'=>'http://api.w.datacdn.cn/',
    ],
    //出价用户的等级
    'bidmembergrder'=>[
        'start'=>5,
        'end'=>10,
    ]



];
//cacseuserid案例用户id