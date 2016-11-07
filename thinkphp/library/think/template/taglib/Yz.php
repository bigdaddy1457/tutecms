<?php
namespace think\template\taglib;

use think\template\TagLib;

/**
 * Yz标签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    panjie
 */
class Yz extends Taglib
{
    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'block'         => ['attr' => 'position', 'close' => 0],
        'plugin'        => ['attr' => 'position,object', 'close' => 0],
        'access'        => ['attr' => 'type,id,action', 'close' => 1],
    ];

    /**
     * block区块解析标签
     * 格式：
     * {block position="positionName" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagBlock($tag)
    {
        $position = !empty($tag['position']) ? $tag['position'] : '';

        $parseStr = '<?php ';
        $parseStr .= 'call_user_func("app\block\controller\BlockController::init", "' . $position . '");';
        $parseStr .= ' ?>';
        return $parseStr; 
    }

    /**
     * plugin插件解析标签
     * 格式：
     * {plugin position="positionName" object="object"/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPlugin($tag)
    {
        $position   = !empty($tag['position']) ? $tag['position'] : '';
        $object     = !empty($tag['object']) ? $tag['object'] : '';

        $parseStr = '<?php ';
        if (empty($object))
        {
            $parseStr .= 'call_user_func("app\plugin\controller\PluginController::init", "' . $position . '");';
        } else {
            $parseStr .= 'call_user_func_array("app\plugin\controller\PluginController::init", ["' . $position . '", $' . $object . ']);';
        }
        
        $parseStr .= ' ?>';
        return $parseStr; 
    }

    /**
     * 权限验证
     * @param    array                   $tag     
     * action 触发器
     * @param    string                   $content 原文本
     * @return   
     * @author panjie panjie@mengyunzhi.com
     * @DateTime 2016-09-14T20:57:29+0800
     */
    public function tagAccess($tag, $content)
    {
        // 获取参数
        $action     = !empty($tag['action']) ? $tag['action'] : null;
        $parseStr = '<?php';

        // 未传入参数 ，则返回空字符串
        if (!$action) {
            $parseStr = '';
        } else {

            // 对当前用户是否拥有权限进行判断
            $parseStr .= ' if (app\model\AccessUserGroupMenuModel::checkCurrentUserCurrentMenuIsAllowedByAction("';
            $parseStr .= $action;
            $parseStr .= '")) : ?>';
            $parseStr .= $content;
            $parseStr .= '<?php endif;';     
        }

        $parseStr .= '?>';
        return $parseStr;
    }
}
