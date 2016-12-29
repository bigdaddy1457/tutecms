<?php
namespace app\model;

class ContentModel extends ModelModel
{
    private $ContentTypeModel       = null;             // 文章类型模型
    protected $preContentModel      = null;             // 上一篇文章
    protected $nextContentModel     = null;             // 下一篇文章
    private $FieldXXXXModels        = null;

    public function __construct($data = [], $getFieldXXXXModels = true) {
        parent::__construct($data);
        if ($getFieldXXXXModels) {
            $this->FieldXXXXModels();
        }
    }

    /**
     * 内容类型 n:1
     * @author panjie panjie@mengyunzhi.com
     * @DateTime 2016-09-02T13:54:47+0800
     */
    public function ContentTypeModel()
    {
        if (null === $this->ContentTypeModel) {
            $map = ['name' => $this->getData('content_type_name')];
            $this->ContentTypeModel = ContentTypeModel::get($map);
        }
        return $this->ContentTypeModel;
    }


    /**
     * 通过扩展字段的 字段名 来获取字段内容
     * @param    string                   $fieldName 字段名
     * @return   Object                              FieldDataXXXModel 
     * @author panjie panjie@mengyunzhi.com
     * @DateTime 2016-09-02T14:13:25+0800
     */
    public function FieldXXXXModel($name)
    {
        if (empty($name)) {
            throw new \Exception("the param can't  empty", 1);
        }

        // 获取对应的全部字段的信息
        $FieldModels = $this->ContentTypeModel()->FieldModels();

        // 遍历当前 内容类型 的扩展字段信息.
        foreach ($FieldModels as $FieldModel) {
            // 找到当字段，则返回当前字段对应的扩展字段对象
            if ($FieldModel->getData('name') === $name) {
                return $FieldModel->getFieldDataXXXModelByKeyId($this->getData('id'));
            }
        }

        throw new \Exception('not found fieldName:' . $name . ' of ContentModel:' . $this->getData('id'), 1);
    }

    /**
     * 内容对应的内段详情信息
     * @author panjie panjie@mengyunzhi.com
     * @DateTime 2016-09-19T08:40:37+0800
     */
    public function FieldXXXXModels()
    {
        if (null === $this->FieldXXXXModels) {
            $this->FieldXXXXModels = [];
            // 获取对应的全部字段的信息
            $FieldModels = $this->ContentTypeModel()->FieldModels();
            // 遍历当前 内容类型 的扩展字段信息.
            foreach ($FieldModels as $FieldModel) {
                array_push($this->FieldXXXXModels, $FieldModel->getFieldDataXXXModelByKeyId($this->getData('id')));
            } 
            
        }
        
        return $this->FieldXXXXModels;
    }

    /**
     * 获取 上一篇 文章
     * @return 文章 ContentModel
     */
    public function getPreContentModel($orderField = 'create_time', $order = 'desc')
    {
        if (null === $this->preContentModel) {
            $this->preContentModel = $this->getNeighborContentModel($orderField, $order, 'pre');
        }
        return $this->preContentModel;
    }

    /**
     * 获取 下一篇 文章
     * @return 文章 ContentModel
     */
    public function getNextContentModel($orderField = 'create_time', $order = 'desc')
    {
        if (null === $this->nextContentModel) {
            $this->nextContentModel = $this->getNeighborContentModel($orderField, $order, 'next');
        }
        return $this->nextContentModel;
    }
    
    public function getNeighborContentModel($orderField = 'create_time', $order = 'desc', $type = 'pre') {
        $map = [];
        if ('pre' === $type) {
            $map['id'] = ['>', $this->getData('id')];
            $order = ($order === 'desc') ? 'asc' : 'desc';
        } else {
            $map['id'] = ['<', $this->getData('id')];
        }
        $orderBy = $orderField . ' ' . $order;
        $map['is_delete'] = 0;
        $map['content_type_name'] = $this->getData('content_type_name');

        return $this->where($map)->order($orderBy)->find(); 
    }

    /**
     * 获取某个内容类型的所有的列表
     * 先转化为树状，先转化为列表，这样顺序输出后，就有了上下级的结构
     * @param  string $menuTypeName 菜单类型名 string pname $pname 父级菜单的id int $is_delete 是否删除标记
     * @return lists               
     * @author gaoliming
     */
    public function getListsByContentTypeNamePname($ContentTypeModel, $is_delete)
    {
        $map = array('content_type_name' => $ContentTypeModel,
                      'is_delete'       => $is_delete,
         );

        $ContentModels = $this->where($map)->order('weight desc')->select();
        return $ContentModels;
    }

    /**
     * 将冻结显示为是否
     * @param int $value 
     * @author gaoliming
     */
    public function getIsFreezedAttr($value)
    {
        $status = array('0' => '一',
                        '1' => '是',
         );

        if ($value === 0 || $value === 1) {
            return $status[$value];
        }
    }

    /**
     * 获取文章内容
     * @return string 文章字符串
     * @author chuhang 
     */
    public function getContent() {
        $KeyId = $this->getData('id');
        $map['key_id'] = $KeyId;
        $result = FieldDataBodyModel::get($map)->getData('value');
        return $result;
    }

    /**
     * 用于截取首页图文列表的长度
     * @author chuhang 
     */
    public function substrArticle($string) {
        $length = '60';
        $etc = '...';
     
        $result = '';
        $string = html_entity_decode ( trim ( strip_tags ( $string ) ), ENT_QUOTES, 'UTF-8' );
        $strlen = strlen ( $string );
        for($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
            if ($number = strpos ( str_pad ( decbin ( ord ( substr ( $string, $i, 1 ) ) ), 8, '0', STR_PAD_LEFT ), '0' )) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr ( $string, $i, $number );
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr ( $string, $i, 1 );
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars ( $result, ENT_QUOTES, 'UTF-8' );
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }
}