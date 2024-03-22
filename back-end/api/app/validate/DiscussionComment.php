<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class DiscussionComment extends Validate
{
    protected $rule =
        [
            'discussion_id' => 'require',
            'user_id' => 'require',
            'comment' => 'require|length:2,5000',
            'comment_id' => 'require'
        ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message =
            [
                'discussion_id.require' => setLang('DiscussionIdMust'),
                'user_id.require' => setLang('UserIdMust'),
                'comment.require' => setLang('DiscussionContentMust'),
                'comment.length' => setLang('DiscussionCommentContentMust2to5000'),
                'comment_id' => setLang('DiscussionCommentIdMust'),
            ];

    }

    protected $scene =
        [
            'create' => ['discussion_id', 'user_id', 'comment'],
            'update' => ['discussion_id', 'user_id', 'comment', 'comment_id']
        ];

}