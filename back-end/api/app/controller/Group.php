<?php
/**
 * 组织接口相关
 *
 * @author:uuz
 * @createTime: 2024-03-04
 */

namespace app\controller;

use app\BaseController;
use app\model\User as UserModel;
use think\facade\Request;
use app\validate\Group as GroupValidate;
use app\model\Group as GroupModel;
use app\model\GroupUser as GroupUserModel;
use app\model\Project as ProjectModel;
use function app\setLang;

class Group extends BaseController
{
    /**
     *创建组织
     *
     * @param string $name //组织名称
     * @param string $icon //组织图标
     * @param int $creatorId //组织创建者id
     * @return array
     *
     */

    public function create()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $name = $param['name'];
        $icon = $param['icon'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(GroupValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $group = GroupModel::where(['name' => $name, 'creator_id' => $creatorId])->find();
        if ($group) {
            return $this->exception(setLang('TheGroupNameIsDuplicated'));
        }

        $data = GroupModel::create([
            'name' => $name,
            'icon' => $icon,
            'creator_id' => $creatorId
        ]);

        if (!$data) {
            return $this->exception(setLang('GroupCreateError'));
        }

        // 添加至组织成员列表
        GroupUserModel::create([
            'user_id' => $creatorId,
            'group_id' => $data['id']
        ]);

        $successMsg = setLang('GroupCreateSuccess'); // 获取成功消息

        return $this->success($data, $successMsg);
    }

    /**
     *查询组织列表
     *
     * @param string $userId //用户id
     * @param string $groupName //组织名称
     * @param int $page //页码
     * @param int $size //每页数量
     * @return  array
     *
     */
    public function list()
    {
        $header = Request::header();

        $token = $header['token'];
        $page = input('get.page') ?? '1';
        $size = input('get.size') ?? '10';
        $groupName = input('get.group_name') ?? '';
        $userId = input('get.user_id') ?? '';

        try {
            Common::checkByTokenUid($token, $userId);

        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $where = [];
        if ($groupName != '') {
            $where[] = ['g.name', 'like', '%' . $groupName . '%'];
        }
        if ($userId != '') {
            //创建匿名函数
            $where[] = function ($query) use ($userId) {
                //匿名函数内部传递外部变量
                $query->where('gu.user_id', '=', $userId);
            };
        }
        // 联表获取组织列表及创建人信息
        $joinedGroup = GroupUserModel::alias('gu')
            ->join('group g', 'gu.group_id = g.id')
            ->join('user u', 'g.creator_id = u.id')
            ->where($where)
            ->field('g.id as group_id, g.name as group_name, g.status, g.icon, g.creator_id, u.nickname as creator_nickname, u.avatar as creator_avatar')
            ->page($page, $size)
            ->select();

        $data = [];
        foreach ($joinedGroup as $item) {

            if ($item['status'] == 1 || $item['creator_id'] == $userId) {

                $members = GroupUserModel::where('group_id', $item['group_id'])->select();

                $memberData = [];
                foreach ($members as $member) {
                    $userList = UserModel::where('id', $member['user_id'])->find();

                    $memberData[] = [
                        'user_id' => $userList['id'],
                        'nickname' => $userList['nickname'],
                        'avatar' => $userList['avatar']
                    ];
                }

                $data[] = [
                    'group_id' => $item['group_id'],
                    'status' => $item['status'],
                    'name' => $item['group_name'],
                    'icon' => $item['icon'],
                    'creator_id' => $item['creator_id'],
                    'creator' => [
                        'nickname' => $item['creator_nickname'],
                        'avatar' => $item['creator_avatar']
                    ],
                    'members' => $memberData
                ];
            }
        }

        $count = count($data);
        return $this->success(['page' => $page, 'size' => $size, 'count' => $count, 'joined_group' => $data]);
    }

    /**
     *更新组织信息
     *
     * @param string $name 组织名称
     * @param int $id 组织ID
     * @param string $icon 组织标志路径
     * @param int $CreatorId 用户id
     * @return  array
     */
    public
    function update()
    {
        $param = Request::param();
        $header = Request::header();

        $creatorId = $param['creator_id'];
        $id = $param['id'];

        $group = GroupModel::where(['id' => $id])->find();
        if (!$group) {
            return $this->exception(setlang('GroupNotFound'));
        }

        if ($group['creator_id'] != $creatorId) {
            return $this->exception(setlang('NoPermission'));
        }

        $name = $param['name'] ?? $group['name'];
        $icon = $param['icon'] ?? $group['icon'];

        $token = $header['token'];
        $timestamp = time(); // 获取当前时间戳

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(GroupValidate::class)->scene('update')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $data = GroupModel::where(['id' => $id, 'creator_id' => $creatorId])->update(
            [
                'name' => $name,
                'icon' => $icon,
                'update_time' => $timestamp
            ]
        );

        $updatedGroup = GroupModel::where('id', $id)->find();

        if (!$data) {
            return $this->exception(setlang('GroupUpdateError'));
        }

        return $this->success($updatedGroup, setlang('GroupUpdateSuccess'));

    }

    /**
     * 添加成员到组织
     *
     * @param int $creatorId //创建者Id
     * @param string $memberEmail //添加成员Id
     * @param int $groupId //组织id
     * @return  array
     */
    public
    function add()
    {
        $param = Request::param();
        $header = Request::header();

        $creatorId = $param['creator_id'];
        $memberEmail = $param['username'];
        $groupId = $param['group_id'];
        $token = $header['token'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);

        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $user = UserModel::where('username', $memberEmail)->find();

        if ($user == null) {
            return $this->exception(setlang('UserDoesNotExist'));
        }

        if ($user['id'] == $creatorId) {
            return $this->exception(setlang('CannotAndYouSelfToGroup'));
        }

        $checkMember = GroupUserModel::where(['user_id' => $user['id'], 'group_id' => $groupId])->find();
        if ($checkMember) {
            return $this->exception(setlang('MemberAlreadyExistsInGroup'));
        }

        $groupMember = GroupUserModel::create(['user_id' => $user['id'], 'group_id' => $groupId]);
        if (!$groupMember) {
            return $this->exception(setlang('InvitingMemberError'));
        }

        return $this->success($groupMember, setlang('InvitingMemberSuccess'));
    }

    /**
     *组织成员列表
     *
     * @param int $page //页码
     * @param int $size //每页数量
     * @param string $nickName //用户昵称
     * @param int $groupId //组织id
     * @return  array
     *
     */
    public function memberList()
    {
        $page = input('get.page') ?? '1';
        $size = input('get.size') ?? '999';
        $groupId = input('get.group_id');
        $nickName = input('get.nickname') ?? '';

        $group = GroupModel::where('id', $groupId)->find();
        if (!$group) {
            return $this->exception(setlang('GroupNotFound'));
        }

        $where = [['group_id', '=', $groupId]]; //构建查询条件
        if ($nickName != '') {
            $userIdList = UserModel::where('nickname', 'like', '%' . $nickName . '%')->column('id');//返回结果user_id
            $where[] = ['user_id', 'in', $userIdList];//user_id在$userIdList记录保留
        }

        $groupUserList = GroupUserModel::where([$where])->field('group_id,user_id')->page($page, $size)->select();
        //通过返回的user_id 对user表进行查询
        foreach ($groupUserList as $k => $user) {
            $userInfo = UserModel::where('id', $user['user_id'])->find();
            $groupUserList[$k]['user'] =
                [
                    'nickname' => $userInfo['nickname'],
                    'avatar' => $userInfo['avatar'],
                    'bio' => $userInfo['bio']
                ];
        }

        $count = count($groupUserList);
        return $this->success(['count' => $count, 'page' => $page, 'size' => $size, 'group_list' => $groupUserList]);
    }

    /**
     *从组织剔除成员
     *
     * @param int $creatorId //创建人Id
     * @param int $memberId //成员Id
     * @param int $groupId //组织Id
     * @return  array
     */
    public
    function memberDel()
    {
        $param = Request::param();
        $header = Request::header();
        $token = $header['token'];

        $creatorId = $param['creator_id'];
        $memberIds = $param['member_ids'];
        $groupId = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $group = GroupModel::where(['creator_id' => $creatorId, 'id' => $groupId])->find();
        //遍历需要剔除的成员id数组
        foreach ($memberIds as $memberId) {

            $checkMember = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $memberId])->find();
            if (!$checkMember) {
                return $this->exception(setlang('MemberDoesNotExistOrHasBeenRemove'));
            }

            if ($group['creator_id'] == $checkMember['user_id']) {
                return $this->exception(setlang('UnableToExcludeOneSelf'));
            }

            $delMember = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $memberId])->delete();
            if (!$delMember) {
                return $this->exception(setlang('MemberRemoveError'));
            }
        }

        return $this->success(setlang('MemberRemoveSuccess'));
    }

    /***
     *
     * 开启或关闭组织和
     *
     * @param int $groupId // 组织Id
     * @param int $creatorId // 创建人Id
     * @return  array
     */
    public
    function dismiss()
    {
        $param = Request::param();
        $header = Request::header();

        $timestamp = time();
        $token = $header['token'];
        $groupId = $param['group_id'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);

        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $group = GroupModel::where('id', $groupId)->find();
        $project = ProjectModel::where('group_id', $groupId)->find();
        //三元表达式子根据$group['status']的取值
        $status = $group['status'] == 1 ? 0 : 1;
        $projectStatus = $project['status'] == 1 ? 0 : 1;
        $updateStatus = ['status' => $status];
        $projectStatusUpdate = ['status' => $projectStatus];
        if ($status == 0) {
            $updateStatus['close_time'] = $timestamp;
        }

        $groupClose = GroupModel::where('id', $groupId)
            ->update($updateStatus);
        ProjectModel::where('group_id', $groupId)
            ->update($projectStatusUpdate);

        if (!$groupClose) {
            return $this->exception($status == 0 ? setlang('GroupCloseError') : setlang('GroupOpenError'));
        }
        return $this->success($status == 0 ? setlang('GroupCloseSuccess') : setlang('GroupOpenSuccess'));
    }

}

