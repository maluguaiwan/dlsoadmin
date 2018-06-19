<?php
namespace common\taglib;

use think\template\TagLib;

class AdminTagLib extends TagLib
{
    
    // 标签定义
    protected $tags = [
        'title' => [
            'attr' => '','close'=>0
        ],
        'load'=>[
            'attr'=>'file,href,type,value,basepath,version,media',
            'close' => 0, 
            'alias' => ['import,css,js', 'type']
        ]
    ];
    
    /**
     * menu 标签解析 左侧菜单
     * 格式：
     * {oye:title title=""  append="0|1"}
     * 标题
     * {/oye:title}
     *
     * @access public
     * @param array $tag
     *            标签属性
     * @param string $content
     *            标签内容
     * @return string|void
     */
    public function tagTitle($tag, $content)
    {
        $parseStr = '<title>';
        $title = config('site_name');
        $append=isset($tag['append'])&&$tag['append']==1?true:false;
        $subtitle=isset($tag['title'])?$tag['title']:'';
        if ($subtitle&&$append) {
            $parseStr .= $title.'-' . $subtitle;
        } elseif ($subtitle) {
            $parseStr .= $subtitle;
        } else {
            $parseStr .= $title;
        }
        $parseStr .= '</title>';
        return $parseStr;
    }
    
    /**
     * load 标签解析 {load file="/static/js/base.js" /}
     * 格式：{load file="/static/css/base.css" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagLoad($tag, $content)
    {
        $file     = isset($tag['file']) ? $tag['file'] : $tag['href'];
        $type     = isset($tag['type']) ? strtolower($tag['type']) : '';
        $parseStr = '';
        $endStr   = '';
        // 判断是否存在加载条件 允许使用函数判断(默认为isset)
        if (isset($tag['value'])) {
            $name = $tag['value'];
            $name = $this->autoBuildVar($name);
            $name = 'isset(' . $name . ')';
            $parseStr .= '<?php if(' . $name . '): ?>';
            $endStr = '<?php endif; ?>';
        }
        $basepath=isset($tag['basepath'])?$tag['basepath']:'';
        $version=isset($tag['version'])?$tag['version']:'';
        $version=$version==='false'?false:$version?$version:config('version');
        $media=isset($tag['media'])?'media="'.$tag['media'].'"':'';
        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $type = strtolower(substr(strrchr($val, '.'), 1));
            $val=$basepath?$basepath.'/'.$val:$val;
            $val=$version?$val.'?v='.$version:$val;
            switch ($type) {
                case 'js':
                    $parseStr .= '<script type="text/javascript" src="' . $val . '"></script>';
                    break;
                case 'css':
                    $parseStr .= '<link rel="stylesheet" type="text/css" href="' . $val . '" '.$media.'/>';
                    break;
                case 'php':
                    $parseStr .= '<?php include "' . $val . '"; ?>';
                    break;
            }
        }
        return $parseStr . $endStr;
    }
}

