<?php
namespace think\template\taglib;

use think\template\TagLib;
use app\Model\MenuModel;

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
        // 标签定义： attr 属性列表 close 是否闭合（0自闭合 或者1非自闭合（要有另一半） 默认1） alias 标签别名 level 嵌套层次
        'block'         => ['attr' => 'position', 'close' => 0],
        'plugin'        => ['attr' => 'position,action,object', 'close' => 0],
        'access'        => ['attr' => 'action', 'close' => 1],
        'url'           => ['attr' => 'action,menu_id,menu_id,append_query_string', 'close' => 0],
        'filter'        => ['attr' => 'input,class,function', 'close' => 0],
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
        $action     = !empty($tag['action']) ? $tag['action'] : 'index';

        $parseStr = '<?php ';
        if (empty($object))
        {
            $object = 'ContentModel';
        }
         
        $parseStr .= 'call_user_func_array("app\plugin\controller\PluginController::init", ["' . $position . '", "' . $action .'", $' . $object . ']);';
        
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
            $parseStr .= '';
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

    /**
     * 通过ACTION生成当前菜单对应的URL信息
     * @param    array                   $tag 传入标签
     * @param   $menuId 菜单ID
     * @param string $action 触发器（路由）
     * @param string $param 传入的参数，以 , 分隔的字符串，比如：'3,4,12'
     * @return   php code                        php语句
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-27T09:30:08+0800
     */
    public function tagUrl($tag) {

        // 获取参数
        $action     = !empty($tag['action']) ? $tag['action'] : 'index';       // 触发器
        $menuId     = !empty($tag['menu_id']) ?  $tag['menu_id'] : '';      // 菜单 ID
        $param      = !empty($tag['param']) ? $tag['param'] : '';           // 传入参数
        $appendQueryQtring = !empty($tag['append_query_string']) ? 'true' : 'false';     // 是否带入url中的query信息   

        // 接接开始
        $parseStr = '<?php ';

        // 调用Common::makeUrlByMenuIdActionParam方法
        $parseStr .= 'echo app\Common::makeUrlByMenuIdActionParam(';

        // 对menuId判空，给默认值
        if (!empty($menuId)) {
            $parseStr .= $menuId ;
        } else {
            $parseStr .= '0';
        }

        $parseStr .= ', "';
        $parseStr .= $action;
        $parseStr .= '", ';

        // 对param判空，给默认值
        if (!empty($param)) {
            $parseStr .= $param;
        } else {
            $parseStr .= '[]';
        }
        
        // 是否带入url中的query信息
        $parseStr .= ',' . $appendQueryQtring;
        $parseStr .= ');'; 

        // 接结结束
        $parseStr .= ' ?>';

        // return 
        return $parseStr;
    }

    /**
     * 过滤器标签
     * @param    array                   $tag
     * @return   [type]                        [description]
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-28T14:59:39+0800
     */
    public function tagfilter($tag)
    {

        // 获取参数
        $input     = !empty($tag['input']) ? $tag['input'] : null;          // 输入
        $class     = !empty($tag['class']) ? $tag['class'] : null;          // 类名
        $function  = !empty($tag['function']) ?  $tag['function'] : '';     // 方法名
        $param     = !empty($tag['param']) ? $tag['param'] : '';            // 传入参数

        $parseStr = '<?php ';
        $parseStr .= 'echo app\filter\\' . $class . '::' . $function . '(';

        // 按输入类型是否为 $ 打头的变量进行区分
        if ('$' === substr($input, 0, 1)) {
            $parseStr .= $input;
        } else {
            $parseStr .= '"' . $input . '"';
        }
        
        $parseStr .= ',';

        // 按输入类型是否为 $ 进行区分写入
        if ('$' === substr($input, 0, 1)) {
            $parseStr .= $param;
        } else {
            $parseStr .= '"' . $param . '"';
        }
        
        $parseStr .= ');';
        $parseStr .= ' ?>';
        return $parseStr;
    }
}
