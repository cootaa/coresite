<?php
declare (strict_types=1);

/**
 * 应用公共文件
 * @author:dr
 * @createTime:2023-03-12
 */

namespace app;

use app\lang\zhcn;
use app\lang\enus;


/**
 * 设置语言包
 *
 *
 * @param string $message 文本信息
 * @return string
 */
function setLang($message)
{

    $lang = \think\facade\Request::header('lang') ?? 'en-US';
    // 根据语言参数获取对应的文本信息
    if ($lang == 'zh-CN') {
        app()->lang->setLangSet('zh-cn');
        return lang($message);
    }

    if ($lang == 'en-US') {
        app()->lang->setLangSet('en-us');
        return lang($message);
    }

    // 如果语言参数不存在，则返回错误
    return 'Language not found';

}