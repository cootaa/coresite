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
            'content' => 'require|length:5,5000'
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
                'content.length' => setLang('DiscussionContentMust5to5000'),
                'discussion_id' => setLang('DiscussionIdMust'),
            ];
    }

    protected $scene =
        [
            'create' => ['group_id', 'creator_id', 'title', 'content'],
            'update' => ['group_id', 'creator_id', 'title', 'content', 'discussion_id'],
        ];
}