<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Notice extends Validate
{
    protected $rule =
        [
            'group_id' => 'require',
            'creator_id' => 'require',
            'content' => 'require|length:5,2000',
            'notice_id' => 'require'
        ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message =
            [
                'group_id' => setLang('GroupIdMust'),
                'creator_id' => setLang('CreatorIdMust'),
                'content.require' => setLang('NoticeContentMust'),
                'content.length' => setLang('NoticeContentLength'),
                'notice_id' => setLang('NoticeIdMust')
            ];
    }

    protected $scene =
        [
            'create' => ['group_id', 'creator_id', 'content'],
            'update' => ['group_id', 'creator_id,', 'content', 'notice_id'],
        ];

}