<?php

/**
 * 用户相关接口
 *
 * @author: dr
 * @createTime: 2024-03-01
 */

namespace app\controller;

use think\facade\Cache;
use think\facade\Env;
use think\facade\Request;
use think\exception\ValidateException;
use app\BaseController;
use app\validate\User as UserValidate;
use app\model\User as UserModel;
use app\model\ProjectUser as ProjectUserModel;
use app\model\GroupUser as GroupUserModel;
use function app\setLang;

class User extends BaseController
{

    /**
     * 登录
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @return array
     */
    public function login()
    {

        $param = Request::param();
        $redis = Cache::store('redis');

        try {
            validate(UserValidate::class)->scene('login')->check($param);
        } catch (ValidateException $e) {
            return $this->exception($e->getError());
        }

        $username = $param['username'];
        $password = $param['password'];

        $user = UserModel::where('username', $username)->find();
        if (!$user) {
            return $this->exception(setLang('userNotFound'));
        }

        if ($user['limited'] == 1) {
            return $this->exception(setlang('AccountFreeze'));
        }

        if ($user['password'] != md5($password)) {
            return $this->exception(setlang('passwordError'));
        }

        $redis->set(Common::createToken(), $user['id']);

        $settingParts = explode('/', $user['setting']);

        //判断用户个性化设置
        if (count($settingParts) < 4) {
            UserModel::where('id', $user['id'])->update(['setting' => 'en-US/light/Indent/0.35']);
        }

        if (count($settingParts) == 4) {
            $lang = $settingParts[0];
            $theme = $settingParts[1];
            $side = $settingParts[2];
            $frame = $settingParts[3];
        }

        return $this->success([
            'token' => Common::createToken(),
            'expire_time' => time() + 3600 * 24 * 7, // 7天有效期
            'nickname' => $user['nickname'],
            'user_id' => $user['id'],
            'avatar' => $user['avatar'],
            'setting' => [
                'lang' => $lang,
                'theme' => $theme,
                'side' => $side,
                'frame' => $frame
            ],
        ]);

    }

    /**
     * 注册
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     * @return array
     */
    public function register()
    {
        $param = Request::param();

        try {
            validate(UserValidate::class)->scene('register')->check($param);
        } catch (ValidateException $e) {
            return $this->exception($e->getError());
        }

        $username = $param['username'];
        $password = $param['password'];
        $nickname = $param['nickname'];

        $user = UserModel::where('username', $username)->find();
        if ($user) {
            return $this->exception(setlang('UserNameIsRepeat'));
        }

        // 生成随机的默认头像URL
        $avatarBaseUrl = Env::get('AVATAR_URL.AVATAR_BASE_URL');
        $avatarFormat = '.png';
        $avatarIndex = mt_rand(1, 30); // 生成1到30之间的随机数
        $avatarDefaultUrl = $avatarBaseUrl . $avatarIndex . $avatarFormat;
        $setting = Env::get('USER_SETTING');
        $data = UserModel::create([
            'username' => $username,
            'password' => md5($password),
            'nickname' => $nickname,
            'create_time' => time(),
            'setting' => $setting,
            'avatar' => $avatarDefaultUrl // 将生成的默认头像URL存入avatar字段
        ]);

        if (!$data) {
            return $this->exception(setlang('UserRegisterFailed'));
        }

        return $this->success(setlang('UserRegisterSuccess'));
    }


    /**
     *用户离开组织
     *
     * @param int $userId //用户id
     * @param int $groupId //组织id
     * @return array
     */
    public function groupLeave()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $groupId = $param['group_id'];
        $userId = $param['user_id'];

        try {
            Common::checkByTokenUid($token, $userId);

        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $data = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $userId])->delete();
        if (!$data) {
            return $this->exception(setlang('ExitedTheGroupError'));
        }
        return $this->success(setlang('ExitedTheGroupSuccess'));

    }

    /**
     *用户离开项目
     *
     * @param int $userId //用户id
     * @param int $projectId //项目id
     * @return  array
     */
    public function projectLeave()
    {
        $param = Request::param();
        $header = Request::header();

        $lang = $header['lang'];
        app()->lang->setLangSet($lang); // 设置当前语言

        $token = $header['token'];
        $projectId = $param['project_id'];
        $userId = $param['user_id'];

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $data = ProjectUserModel::where(['project_id' => $projectId, 'user_id' => $userId])->delete();
        if (!$data) {
            return $this->exception(setlang('ExitedTheProjectError'));
        }
        return $this->success(setlang('ExitedTheProjectSuccess'));
    }

    /**
     *用户登出
     *
     * @return array
     */
    public function logout()
    {
        $redis = Cache::store('redis');
        $header = Request::header();

        $lang = $header['lang'];
        app()->lang->setLangSet($lang); // 设置当前语言

        $token = $header['token'];
        $check = $redis->delete($token);

        if (!$check) {
            return $this->exception(setlang('UserLogoutError'));
        }
        return $this->success(setlang('UserLogoutSuccess'));
    }

    /**
     *用户详细信息
     *
     * @param int $userId //用户id
     * @return array
     */
    public function info()
    {
        $userId = input('get.user_id') ?? '';
        $header = Request::header();

        $token = $header['token'];

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $data = UserModel::where('id', $userId)->field('id,username,nickname,avatar,bio,create_time,setting')->find();

        $settingParts = explode('/', $data['setting']);
        $lang = $settingParts[0];
        $theme = $settingParts[1];
        $side = $settingParts[2];
        $frame = $settingParts[3];

        $newData = $data->toArray();
        $newData['setting'] =
            [
                'lang' => $lang,
                'theme' => $theme,
                'side' => $side,
                'frame' => $frame,
            ];

        return $this->success($newData);
    }

    /**
     *用户详细信息更新
     *
     * @param int $userId //用户id
     * @param string $nickName //用户昵称
     * @param string $avatar //用户头像
     * @param string $bio //用户简介
     * @return array
     */
    public function update()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $userId = $param['user_id'];

        try {
            Common::checkByTokenUid($token, $userId);
            validate(UserValidate::class)->scene('update')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $userMsg = UserModel::where('id', $userId)->find();

        $settingParts = explode('/', $userMsg['setting']);
        $oldLang = $settingParts[0];
        $oldTheme = $settingParts[1];
        $oldSide = $settingParts[2];
        $oldFrame = $settingParts[3];

        $avatar = $param['avatar'] ?? $userMsg['avatar'];
        $nickName = $param['nickname'] ?? $userMsg['nickname'];
        $bio = $param['bio'] ?? $userMsg['bio'];
        $lang = $param['lang'] ?? $oldLang;
        $theme = $param['theme'] ?? $oldTheme;
        $side = $param['side'] ?? $oldSide;
        $frame = $param['frame'] ?? $oldFrame;


        $data = UserModel::where('id', $userId)->update([
            'avatar' => $avatar,
            'nickname' => $nickName,
            'bio' => $bio,
            'setting' => $lang . '/' . $theme . '/' . $side . '/' . $frame,
        ]);

        $updateUser = UserModel::where('id', $userId)->field('id,username,nickname,avatar,bio,limited,create_time,setting')->find();

        return $this->success($updateUser, setLang('UserUpdateSuccess'));
    }
}