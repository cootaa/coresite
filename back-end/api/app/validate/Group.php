<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Group extends Validate
{
    protected $rule =
        [
            'name' => 'require|length:2,20',
            'creator_id' => 'require',
            'icon' => 'requireIf:icon,1'

        ];

    protected $scene = [
        'create' => ['name', 'creator_id', 'icon'],
        'update' => ['name', 'creator_id', 'icon']
    ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message =
            [
                'name.require' => setLang('GroupNameMust'),
                'name.length' => setLang('GroupNameLength'),
                'creator_id' => setLang('CreatorIdMust'),
                'icon.requireIf' => setLang('IconMust'),
            ];
    }
}
