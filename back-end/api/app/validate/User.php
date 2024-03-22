<?php

/**
 * 用户验证器
 *
 * @author: dr
 * @createTime: 2024/03/01
 */

namespace app\validate;

use think\Validate;
use function app\setLang;

class User extends Validate
{
    protected $rule = [
        'username' => 'require|email',
        'password' => 'require|length:6,30',
        'nickname' => 'chsAlphaNum|length:2,20',
        'avatar' => 'max:255',
        'bio' => 'max:255'
    ];

    public function __construct(array $message = [])
    {
        parent::__construct($message);

        $this->message = [
            'username.require' => setLang('UserNameMust'),
            'username.email' => setLang('MustEmailFormat'),
            'password.require' => setLang('PasswordMust'),
            'password.length' => setLang('UserPasswordMust6To30'),
            'nickname.require' => setLang('NickNameMust'),
            'nickname.chsAlphaNum' => setLang('NickNameFormat'),
            'nickname.length' => setLang('NicknameLength'),
            'avatar.url' => setLang('AvatarError'),
            'bio.max' => setLang('BioMax')
        ];
    }

    protected $scene = [
        'login' => ['username', 'password'],
        'register' => ['username', 'password', 'nickname'],
        'update' => ['nickname', 'avatar', 'bio']
    ];

}