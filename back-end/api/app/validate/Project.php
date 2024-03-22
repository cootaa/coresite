<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Project extends Validate
{
    protected $rule =
        [
            'name' => 'require|length:2,25',
            'creator_id' => 'require',
            'icon' => 'requireIf:icon,1',
            'group_id' => 'require'
        ];

    protected $scene =
        [
            'create' => ['name', 'creator_id', 'icon', 'group_id'],
            'update' => ['name', 'creator_id', 'icon', 'group_id']
        ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message = [
            'name.require' => setLang('ProjectNameMust'),
            'name.length' => setLang('ProjectNameLength'),
            'creator_id' => setLang('CreatorIdMust'),
            'icon.requireIf' => setLang('IconMust'),
            'group_id' => setLang('GroupIdMust'),
        ];
    }
}