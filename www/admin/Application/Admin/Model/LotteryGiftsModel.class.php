<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace Admin\Model;
use Think\Model;
class LotteryGiftsModel extends Model {
    protected $patchValidate = true; // 系统支持数据的批量验证功能，
    /**
     *     
        self::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
        self::MUST_VALIDATE 或者1 必须验证
        self::VALUE_VALIDATE或者2 值不为空的时候验证
     * 
     * 
        self::MODEL_INSERT或者1新增数据时候验证
        self::MODEL_UPDATE或者2编辑数据时候验证
        self::MODEL_BOTH或者3全部情况下验证（默认）       
     */
    protected $_validate = array(
        array('lottery_name','require','奖品名称必须填写！',1 ,'',3),
        array('lottery_pr','require','中奖概率必须填写！',1 ,'',3),
        array('lottery_type','0','奖品分类必须填写。',1,'notequal',3)
//        array('shop_price','/\d{1,10}(\.\d{1,2})?$/','本店售价格式不对。',2,'regex'),
//        array('member_price','/\d{1,10}(\.\d{1,2})?$/','会员价格式不对。',2,'regex'),
//        array('market_price','/\d{1,10}(\.\d{1,2})?$/','市场价格式不对。',2,'regex'), // currency
//        array('weight','/\d{1,10}(\.\d{1,2})?$/','重量格式不对。',2,'regex'),
//        array('exchange_integral','checkExchangeIntegral','积分抵扣金额不能超过商品区总额',0,'callback'),
     );

    protected $_auto = array (
        array('addtime','time',1,'function'),
        array('updatetime','time',2,'function') // 对update_time字段在更新的时候写入当前时间戳
    );

}
