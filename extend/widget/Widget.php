<?php
namespace widget;

use think\View;

/**
 * ThinkPHP Widget类 抽象类
 *
 * @category Think
 * @package Think
 * @subpackage Core
 * @author liu21st <liu21st@gmail.com>
 */
abstract class Widget
{
    
    // 使用的模板引擎 每个Widget可以单独配置不受系统影响
    protected $template = '';
    
    /**
     * 渲染输出 render方法是Widget唯一的接口
     * 使用字符串返回 不能有任何输出
     *
     * @access public
     * @param mixed $data
     *            要渲染的数据
     * @return string
     */
    abstract public function render($data);
    
    /**
     * 渲染模板输出 供render方法内部调用
     *
     * @access public
     * @param string $templateFile
     *            模板文件
     * @param mixed $var
     *            模板变量
     * @return string
     */
    protected function renderFile($templateFile = '', $var = [])
    {
        if (! is_file($templateFile)) {
            // 自动定位模板文件
            $name = str_replace('widget\\', '', get_class($this));
            $filename = empty($templateFile) ? $name : $templateFile;
            $templateFile = __DIR__ . '/view/' . $filename .'.'. config('template.view_suffix');
            if (! is_file($templateFile))
                exception(lang('_TEMPLATE_NOT_EXIST_') . '[' . $templateFile . ']');
        }
        foreach (['type','help','readonly'] as $key){
            if(!isset($var[$key])){
                $var[$key]='';
            }
        }
        $view=new View();
        $config=[
            'tpl_cache'=>false,
            'taglib_pre_load'=>'common\taglib\AdminTagLib'
        ];
        $content =$view->fetch($templateFile,$var,[],$config);
        return $content;
    }
}