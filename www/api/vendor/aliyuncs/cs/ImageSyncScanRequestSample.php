<?php

class ImageSyncScanRequestSample{



    /**
     * User: hyliu
     * Time: 17:46
     * 同步图片检测样例，调用会实时返回检测结果
     */
//header("Content-type: text/html; charset=gb2312");
header("Content-type: text/html; charset=utf-8");
include_once 'aliyuncs/aliyun-php-sdk-core/Config.php';
    use Green\Request\V20170112 as Green;
date_default_timezone_set("PRC");

$ak = parse_ini_file("aliyun.ak.ini");
//请替换成你自己的accessKeyId、accessKeySecret(在aliyun.ad.ini文件中)
$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $ak["accessKeyId"], $ak["accessKeySecret"]); // TODO
DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "Green", "green.cn-shanghai.aliyuncs.com");
$client = new DefaultAcsClient($iClientProfile);

$request = new Green\ImageSyncScanRequest();
$request->setMethod("POST");
$request->setAcceptFormat("JSON");

$task1 = array('dataId' =>  uniqid(),
    'url' => 'http://i01.pic.sogou.com/399de114c08bbf1d',//图片地址（外网地址）
    'time' => round(microtime(true)*1000)
);
$request->setContent(json_encode(array("tasks" => array($task1),
                              "scenes" => array("ad","porn","ocr"))));

try {
    $response = $client->getAcsResponse($request);
	echo '<pre>';var_dump($response);exit;
    if(200 == $response->code){
        $taskResults = $response->data;
        foreach ($taskResults as $taskResult) {
            if(200 == $taskResult->code){
                $sceneResults = $taskResult->results;
                foreach ($sceneResults as $sceneResult) {
                    $scene = $sceneResult->scene;
                    $suggestion = $sceneResult->suggestion;
					$ocrData = $sceneResult->ocrData;
                    //根据scene和suggetion做相关的处理
                    //$scene鉴定类型（ad广告；porn黄图）
                    //$suggestion（鉴定结果: pass:图片正常，review：需要人工审核，block：图片违规，可以直接删除或者做限制处理）
                    print_r($scene);
                    print_r($suggestion);
					print_r($ocrData);
                }
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




}


