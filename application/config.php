<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => true,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'htmlspecialchars',
    // 是否启用控制器类后缀
    'controller_suffix'      => true,
    // 应用类库后缀
    'class_suffix'           => true,
    // token后缀
    'token_suffix'           => 'yunzhicms',
    
    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------
    
    // 默认模块
    'default_module'         => 'admin',
    // 操作方法后缀
    'action_suffix'          => 'Action',
    // 模型名后缀
    'model_suffix'           => 'Model',
    // 禁止访问模块
    'deny_module_list'       => ['common', 'block', 'field', 'model', 'plugin', 'theme'],

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    
    // 是否开启路由
    'url_route_on'           => true,

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => '5',
        'list_rows' => 20,
    ],

    // 缓存设置
    'cache'  => [
        'type'   => 'File',
        'path'   => CACHE_PATH,
        'prefix' => 'yunzhicms',
        'expire' => 1800,
    ],

    //默认重置密码
    'resetPassword' => 111111,

    //设置默认密码
    'defaultPassword' => 111111,


    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'console',
        'trace_tabs' =>  [
            'base'=>'基本',
            'file'=>'文件',
            'info'=>'流程',
            'error'=>'错误',
            'sql'=>'SQL',
            'debug'=>'调试',
            'component' => '组件',
            'block'     => '区块',
            'plugin'    => '插件',
     ]
    ],
];
