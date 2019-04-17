<?php
namespace app\api\behavior;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use think\Request;
use think\Response;
use RuntimeException;
/**
 * @title   资产加统计管理系统
 * @link    http://www.51zichanjia.com
 * @info    Copyright by 上海锐私信息科技有限公司
 * @version 0.1
 */
class Jwt
{
    public function appInit(&$params)
    {
        // instead of using "?:" on constructors.

        $token = (new Builder())->setIssuer('http://w.tianbaoweipai.com') // Configures the issuer (iss claim)
        ->setAudience('http://w.tianbaoweipai.com') // Configures the audience (aud claim)
        ->setId('4f1g23fsfaaaa12aa', true) // Configures the id (jti claim), replicating as a header item
        ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
        ->setNotBefore(time() ) // Configures the time that the token can be used (nbf claim)
        ->setExpiration(time() + 86400) // Configures the expiration time of the token (nbf claim)
        ->set('user_id', isset($params['user']['user_id'])?$params['user']['user_id']:'0') // Configures a new claim, called "uid"
        ->set('subscribe', isset($params['userong']['subscribe'])?$params['userong']['subscribe']:'0') // Configures a new claim, called "uid"
        ->set('head_pic', isset($params['userong']['head_pic'])?$params['userong']['head_pic']:'/static/img/nohead.png') // Configures a new claim, called "uid"
        ->set('nickname', isset($params['userong']['nickname'])?$params['userong']['nickname']:'') // Configures a new claim, called "uid"
        ->set('openid', isset($params['openid'])?$params['openid']:'') // Configures a new claim, called "uid"
        ->getToken(); // Retrieves the generated token


        $token->getHeaders(); // Retrieves the token header
        $token->getClaims(); // Retrieves the token claims
        $params['token'] =(string)$token;
        $params['token']  =trim( $params['token'] );
    }


    /**
     * Parse the token from the request.
     *
     * @param string $query
     * @return \JWTAuth
     * @static
     */
    public  function parseToken($method = 'bearer', $header = 'authorization', $query = 'token'){
        if (! $token = $this->parseAuthHeader($header, $method)) {
            if (! $token = $this->requestQuery($query)) {
                return null;
            }
        }
//        $DD =file_get_contents("php://input");
//        $header = Request::instance()->header($header);
//        file_put_contents(RUNTIME_PATH.'post',var_export($_POST,true));
//        file_put_contents(RUNTIME_PATH.'header',var_export($header,true));
//        file_put_contents(RUNTIME_PATH.'input',var_export($DD,true));
        return $token;
    }

    public function requestQuery($query){

        $querystr = Request::instance()->param($query);

        if($querystr!=''){
            return  $querystr;
        }
          $temp =file_get_contents("php://input");
          $temp =json_decode($temp,true);
           if(isset($temp[$query])){
               return  trim($temp[$query]);
           }
           return false;
    }


    /**
     * Parse token from the authorization header.
     *
     * @param string $header
     * @param string $method
     *
     * @return false|string
     */
    protected function parseAuthHeader($header = 'authorization', $method = 'bearer')
    {
        $header = Request::instance()->header($header);




        if (! $this->startsWith(strtolower($header), $method)) {
            return false;
        }


        return trim(str_ireplace($method, '', $header));
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public  function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    public function appEnd(&$params)
    {
       $token_str = $this->parseToken();
        $token_str =trim($token_str);
        $params['user_id']='';
        $params['openid']='';
       // $params['token']=$token_str;
        $header = ['Access-Control-Allow-Origin' => '*','Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type,Accept,authorization',  'Access-Control-Allow-Credentials' => true, 'Access-Control-Allow-Methods' => 'GET, PUT, POST,DELETE,OPTIONS'];
        if(empty($token_str)){
            Response::create(['data' => $params, 'code' => '4000', 'message' => 'token解析失败'], 'json')->header($header)->send();
            exit;
        }
        try{
            $params['token'] =$token_str;
            $token = (new Parser())->parse((string) $params['token']); // Parses from a string
            $token->getHeaders(); // Retrieves the token header
            $token->getClaims(); // Retrieves the token claims

            $params['user_id'] = $token->getClaim('user_id');
            $params['openid'] = $token->getClaim('openid');
            $params['nickname'] = $token->getClaim('nickname');

        }catch (\InvalidArgumentException $e){
            Response::create(['data' => $params, 'code' => '4000', 'message' => $e->getMessage()], 'json')->header($header)->send();
        }catch(\RuntimeException  $e){
            Response::create(['data' => $params, 'code' => '4000', 'message' => $e->getMessage()], 'json')->header($header)->send();
        }
         return  $params;
    }

    public function appGetTokencanNull(&$params)
    {
        $token_str = $this->parseToken();
        $params['user_id']='';
        $params['openid']='';
        // $params['token']=$token_str;
        if(empty($token_str)){
            $params['token'] =$token_str;
            $params['user_id'] =0;
            $params['openid'] = '';
            return ;
        }else{
            $params['token'] =$token_str;
            try{
                $token = (new Parser())->parse((string) $params['token']); // Parses from a string

                $token->getHeaders(); // Retrieves the token header
                $token->getClaims(); // Retrieves the token claims
            }catch(\InvalidArgumentException $e){
                $params['token'] =$token_str;
                $params['user_id'] =0;
                $params['openid'] = '';
                return ;
            }
            $params['user_id'] = $token->getClaim('user_id');
            $params['openid'] = $token->getClaim('openid');
            return ;
        }
    }
}