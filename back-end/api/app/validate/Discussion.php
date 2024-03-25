<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Discussion extends Validate
{
    protected $rule =
        [
            'group_id' => 'require',
            'creator_id' => 'require',
            'discussion_id' => 'require',
            'title' => 'require|length:5,80',
            'content' => 'require|checkContent'
        ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message =
            [
                'group_id' => setLang('GroupNotFound'),
                'creator_id' => setLang('CreatorIdMust'),
                'title.require' => setLang('DiscussionTitleMust'),
                'title.length' => setLang('DiscussionLength'),
                'content.require' => setLang('DiscussionContentMust'),
                'content.checkContent' => setLang('DiscussionContentMust5to5000'),
                'discussion_id' => setLang('DiscussionIdMust'),
            ];
    }

    protected $scene =
        [
            'create' => ['group_id', 'creator_id', 'title', 'content'],
            'update' => ['group_id', 'creator_id', 'title', 'content', 'discussion_id'],
        ];

    protected function checkContent($value, $rule)
    {
        $filteredContent = strip_tags($value);

        // 使用正则表达式匹配并去除图片标签和路径
        $filteredContent = preg_replace('/<img[^>]+>/i', '', $filteredContent);
        $filteredContent = preg_replace('/<img.*?src=["\'](.*?)["\'].*?>/i', '', $filteredContent);

        // 计算剩余文本的长度
        $textLength = mb_strlen($filteredContent, 'utf-8');

        // 检查过滤后的内容长度
        if ($textLength < 5 || $textLength > 5000) {
            return false;
        }

        return true;
    }

}