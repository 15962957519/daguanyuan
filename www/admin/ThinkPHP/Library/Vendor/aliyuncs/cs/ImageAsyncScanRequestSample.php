<?php
/**
 * Created by PhpStorm.
 * User: hyliu
 * Date: 2017/4/21
 * Time: 09:57
 */

include_once 'aliyuncs/aliyun-php-sdk-core/Config.php';
use Green\Request\V20170112 as Green;
date_default_timezone_set("PRC");

$ak = parse_ini_file("aliyun.ak.ini");
//请替换成你自己的accessKeyId、accessKeySecret
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $ak["accessKeyId"], $ak["accessKeySecret"]); // TODO
DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "Green", "green.cn-shanghai.aliyuncs.com");
$client = new DefaultAcsClient($iClientProfile);

$request = new Green\ImageAsyncScanRequest();
$request->setMethod("POST");
$request->setAcceptFormat("JSON");

$task1 = array('dataId' =>  uniqid(),
    'url' => 'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=2672143417,2386537397&fm=27&gp=0.jpg',
    'time' => round(microtime(true)*1000)
);
$request->setContent(json_encode(array("tasks" => array($task1),
    "scenes" => array("ad","porn"))));

try {
    $response = $client->getAcsResponse($request);
	echo '<pre>';
    print_r($response);exit;
    if(200 == $response->code){
        $taskResults = $response->data;
        foreach ($taskResults as $taskResult) {
            if(200 == $taskResult->code){
                $taskId = $taskResult->taskId;
				echo '<pre>';
                print_r($taskId);
                // 将taskId 保存下来，间隔一段时间来轮询结果, 参照ImageAsyncScanResultsRequest
            }else{
                print_r("task process fail:" + $response->code);
            }
        }
    }else{
        print_r("detect not success. code:" + $response->code);
    }
} catch (Exception $e) {
    print_r($e);
}