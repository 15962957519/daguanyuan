<?php

namespace app\dataapi\model;

use think\Model;
use traits\model\SoftDelete;

class GoodsShouc extends Model
{
    protected $table = 'tp_goods_collect01';
    protected $pk = 'collect_id';
    //插入collect用 ，过渡，后期修改插入性能
    use SoftDelete;
    protected $deleteTime = 'delete_time';

//
//    protected $connection = [
//        // 数据库类型
//        'type'        => 'mysql',
//        // 数据库连接DSN配置
//        'dsn'         => '',
//        // 服务器地址
//        'hostname'    => '101.37.175.6',
//        // 数据库名
//        'database'    => 'UTPSHOPHH',
//        // 数据库用户名
//        'username'    => 'user',
//        // 数据库密码
//        'password'    => 'goodGHco%lletuserk',
//        // 数据库连接端口
//        'hostport'    => '8066',
//        // 数据库连接参数
//        'params'      => [\PDO::ATTR_EMULATE_PREPARES  => true,\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  => false,\PDO::ATTR_STRINGIFY_FETCHES=>true],
//        // 数据库编码默认采用utf8
//        'charset'     => 'utf8',
//        // 数据库表前缀
//        'prefix'      => 'tp_',
//        // 连接dsn
//        'dsn' => '',
//        // 数据库连接参数
//        // 数据库编码默认采用utf8
//        'charset' => 'utf8',
//        // 数据库表前缀
//        'prefix' => 'tp_',
//        // 数据库调试模式
//        'debug' => false,
//        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
//        'deploy' => 0,
//        // 数据库读写是否分离 主从式有效
//        'rw_separate' => false,
//        // 读写分离后 主服务器数量
//        'master_num' => 1,
//        // 指定从服务器序号
//        'slave_no' => '',
//        // 是否严格检查字段是否存在
//        'fields_strict' => true,
//        // 数据集返回类型
//        'resultset_type' => 'array',
//        // 自动写入时间戳字段
//        'auto_timestamp' => false,
//        // 时间字段取出后的默认时间格式
//        'datetime_format' => 'Y-m-d H:i:s',
//        // 是否需要进行SQL性能分析
//        'sql_explain' => false,
//        // Builder类
//        'builder' => '',
//        // Query类
//        'query' => '\\think\\db\\Query',
//        'break_reconnect'=>true
//    ];

}
