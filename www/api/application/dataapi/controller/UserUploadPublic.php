<?php
namespace app\dataapi\controller;


use app\dataapi\server\Weixin;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuRequest;
use think\Config;
use think\Db;
use think\Request;
use think\Response;

class UserUploadPublic extends BaseApi
{


    //提供给微拍卖的首页数据 前期thinkphp写 后期go语言开发
    public function index(Request $request)
    {

        //获取分类信息

        $goods_category = Db::name('goods_category')->where(['is_show' => 1, 'parent_id' => 844])->field('id,name,mobile_name')->order('sort_order ASC')->cache(true, TPSHOP_CACHE_TIME)->select();


        $dt = Carbon::today();

        $cdow = Carbon::now();
        $toady = $cdow->format('m月d日');


        $dt = $dt->addHour(12);
        $timedata[0][12] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(5);
        $timedata[0][17] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(3);
        $timedata[0][20] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(1);
        $timedata[0][21] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(1);
        $timedata[0][22] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(1);
        $timedata[0][23] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];
        $dt = $dt->addHour(1);
        $timedata[0][24] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];


        foreach ($timedata[0] as $k => &$v) {
            if ($cdow->hour >= $k) {
                unset($timedata[0][$k]);
            }
        }


        if ($cdow->hour > 12) {
            $dt = $dt->addHour(13);
            $timedata[1][] = ['unixtime' => $dt->timestamp, 'string' => $dt->format('H:i')];

        } else {
            $timedata[1] = [];
        }
        $tomorrow = $cdow->tomorrow()->format('m月d日');

        if (!empty($goods_category)) {

            $data = ['name' => $goods_category, 'astime' => $timedata, 'toady' => $toady, 'tomorrow' => $tomorrow];
            Response::create(['data' => $data, 'code' => '2000', 'message' => '获取作品上传信息分类等' . $request->param('actionname') . '成功'], 'json')->header($this->header)->send();
        }
        Response::create(['code' => '4000', 'message' => '获取作品上传信息' . $request->param('actionname') . '失败'], 'json')->header($this->header)->send();
    }


    public function downWeixinImage($media_data)
    {

        //下载微信图片接口
        $url = Config::get('weixin_api.get_down_weixinuploadimg_url');

        $ACCESS_TOKEN = (new Weixin())->getAccessToeknCode();
        $url = str_replace('MEDIA_ID', $media_data, $url);
        $url = str_replace('ACCESS_TOKEN', $ACCESS_TOKEN, $url);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => '',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $GuRequest = new GuRequest('get', $url);
        $response = $client->send($GuRequest, ['timeout' => 2, 'verify' => false]);
        $body = $response->getBody();
        $remainingBytes = $body->getContents();

        return $remainingBytes;
    }


}
