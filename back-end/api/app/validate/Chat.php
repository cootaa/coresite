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
            'message' => 'require|length:1,5000',
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
            'message.require' => setLang('MessageLength')
        ];
    }
}