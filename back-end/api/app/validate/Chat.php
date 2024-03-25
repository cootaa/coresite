<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Chat extends Validate
{

    protected $rule =
        [
            'project_id' => 'require',
            'user_id' => 'require',
            'message' => 'require|checkContent',
        ];

    protected $scene =
        [
            'cave' => ['project_id', 'user_id', 'message']
        ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message = [
            'project_id.require' => setLang('ProjectIdMust'),
            'user_id.require' => setLang('UserIdMust'),
            'message.checkContent' => setLang('MessageLength')
        ];
    }

    protected function checkContent($value, $rule)
    {
        $filteredContent = strip_tags($value);

        // 使用正则表达式匹配并去除图片标签和路径
        $filteredContent = preg_replace('/<img[^>]+>/i', '', $filteredContent);
        $filteredContent = preg_replace('/<img.*?src=["\'](.*?)["\'].*?>/i', '', $filteredContent);

        // 计算剩余文本的长度
        $textLength = mb_strlen($filteredContent, 'utf-8');

        // 检查过滤后的内容长度
        if ($textLength < 1 || $textLength > 5000) {
            return false;
        }

        return true;
    }
}