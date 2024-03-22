<?php

namespace app\validate;

use think\Validate;
use function app\setLang;

class Folder extends Validate
{
    protected $rule =
        [
            'name' => 'require|length:2,20',
            'creator_id' => 'require',
            'project_id' => 'require',
            'parent_id' => 'requireIf:parent_id,1'
        ];

    protected $scene = [
        'create' => ['name', 'creator_id', 'project_id', 'parent_id'],
        'rename' => ['name', 'creator_id', 'project_id', 'parent_id']
    ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message =
            [
                'name.require' => setLang('FolderNameMust'),
                'name.length' => setLang('FolderLengthMust'),
                'creator_id' => setLang('CreatorIdMust'),
                'project_id' => setLang('ProjectIdMust'),
                'parent_id.requireIf' => setLang('PrentIdMust')
            ];
    }
}