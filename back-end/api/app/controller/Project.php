<?php
/**
 * 项目接口相关
 *
 * @author:uuz
 * @createTime: 2024-03-05
 */

namespace app\controller;

use app\BaseController;
use think\facade\Request;
use app\controller\Common;
use app\model\Project as ProjectModel;
use app\validate\Project as ProjectValidate;
use app\model\Group as GroupModel;
use app\model\User as UserModel;
use app\model\ProjectUser as ProjectUserModel;
use function app\setLang;
use app\model\Folder as FolderModel;

class Project extends BaseController
{
    /**
     *创建项目
     *
     * @param int $groupId //所属组织id
     * @param string $name //项目名称
     * @param string $icon // 项目标志
     * @param string $creatorId //项目创建者id
     * @return array
     */
    public function create()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $name = $param['name'];
        $icon = $param['icon'];
        $creatorId = $param['creator_id'];
        $groupId = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(ProjectValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $project = ProjectModel::where(['creator_id' => $creatorId, 'group_id' => $groupId, 'name' => $name])->find();
        if ($project) {
            return $this->exception(setLang('TheProjectNameIsDuplicated'));
        }

        $group = GroupModel::where('id', $groupId)->find();
        if (!$group) {
            return $this->exception(setLang('GroupNotFound'));
        }

        $data = ProjectModel::create(
            [
                'name' => $name,
                'icon' => $icon,
                'creator_id' => $creatorId,
                'group_id' => $groupId
            ]);
        //添加创建人到项目成员表
        ProjectUserModel::create(
            [
                'user_id' => $creatorId,
                'group_id' => $data['group_id'],
                'project_id' => $data['id']
            ]
        );
        //创建根目录
        $createFolder = FolderModel::create
        (
            [
                'name' => '/',
                'project_id' => $data['id'],
                'creator_id' => $creatorId,


            ]
        );
        //创建聊天文件夹
        FolderModel::create
        (
            [
                'name' => 'chat',
                'project_id' => $data['id'],
                'creator_id' => $creatorId,
                'parent_id' => $createFolder['id']
            ]);


        if (!$data) {
            return $this->exception(setLang('ProjectCreateError'));
        }
        return $this->success($data, setLang('ProjectCreateSuccess'));
    }

    /**
     *项目列表
     *
     * @param int $groupId //所属组织id
     * @param string $Name //项目名称
     * @param int $page //页码
     * @param int $size //每页数量
     * @param int $userId // 用户id
     * @return array
     */
    public function list()
    {
        $page = input('get.page') ?? '1';
        $size = input('get.size') ?? '999';
        $name = input('get.name') ?? '';
        $userId = input('get.user_id') ?? '';
        $groupId = input('get.group_id') ?? '';

        $where = [];
        if ($name != '') {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        if ($groupId != '') {
            $where[] = ['pr.group_id', '=', $groupId];
        }

        if ($userId != '') {
            $where[] = ['pr.user_id', '=', $userId];
        }

        $group = GroupModel::where('id', $groupId)->find();
        if ($group['status'] == 0) {
            return $this->exception(setLang('GroupIsClosed'));
        }

        $joinedProject = ProjectUserModel::alias('pr')
            ->join('project p', 'pr.project_id = p.id')
            ->join('group g', 'pr.group_id = g.id')
            ->join('user u', 'pr.user_id = u.id')
            ->field('p.status as status,pr.user_id as user_id ,p.id as project_id, p.name as project_name, p.icon as project_icon, g.id as group_id, u.nickname as nickname, u.avatar as avatar')
            ->where($where)
            ->page($page, $size)
            ->select();

        $data = [];
        foreach ($joinedProject as $item) {
            $data[] = [
                'group_id' => $item['group_id'],
                'project_id' => $item['project_id'],
                'user_id' => $item['user_id'],
                'status'=>$item['status'],
                'project_info' => [
                    'name' => $item['project_name'],
                    'icon' => $item['project_icon'],
                    'creator_info' => [
                        'nickname' => $item['nickname'],
                        'avatar' => $item['avatar']
                    ]
                ]
            ];
        }
        return $this->success($data);
    }


    /**
     * 项目信息更新
     *
     * @param int $creatorId //项目创建人Id
     * @param int $projectId //项目id
     * @param int $groupId // 组织id
     * @param string $name //项目名称
     * @param string $icon // 项目标志
     * @return  array
     */
    public function update()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $projectId = $param['project_id'];
        $group_id = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByProject($projectId, $creatorId);
            validate(ProjectValidate::class)->scene('update')->check($param);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $projectList = ProjectModel::where(
            ['creator_id' => $creatorId, 'id' => $projectId, 'group_id' => $group_id])->find();
        $name = $param['name'] ?? $projectList['name'];
        $icon = $param['icon'] ?? $projectList['icon'];
        $timestamp = time();

        $data = ProjectModel::where(
            [
                'creator_id' => $creatorId,
                'id' => $projectId,
                'group_id' => $group_id
            ]
        )->update(
            [
                'name' => $name,
                'icon' => $icon,
                'update_time' => $timestamp
            ]
        );

        if (!$data) {
            return $this->exception(setLang('ProjectUpdateError'));
        }
        $updateProject = ProjectModel::where('id', $projectId)->find();
        return $this->success($updateProject, setLang('ProjectUpdateSuccess'));
    }

    /**
     *项目关闭或开启
     *
     * @param int $projectId //项目id
     * @param int $creatorId //项目创建人id
     * @return  array
     */
    public function dismiss()
    {
        $param = Request::param();
        $header = Request::header();
        $timestamp = time();
        $token = $header['token'];
        $projectId = $param['project_id'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByProject($projectId, $creatorId);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $project = ProjectModel::where('id', $projectId)->find();
        $status = $project['status'] == 1 ? 0 : 1;
        $updateStatus = ['status' => $status];

        if ($status == 0) {
            $updateStatus['close_time'] = $timestamp;
        }

        $projectClose = ProjectModel::where('id', $projectId)
            ->update($updateStatus);

        if (!$projectClose) {
            return $this->exception($status == 0 ? setlang('ProjectCloseError') : setLang('ProjectOpenError'));
        }

        return $this->success($status == 0 ? setLang('ProjectCloseSuccess') : setLang('ProjectOpenSuccess'));
    }

    /**
     *添加成员到项目
     *
     * @param int $projectId //项目id
     * @param int $groupId //组织id
     * @param int $creatorId //项目创建人id
     * @param int $memberId //被邀请成员id
     * @return array
     */
    public function memberAdd()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $projectId = $param['project_id'];
        $groupId = $param['group_id'];
        $creatorId = $param['creator_id'];
        $memberId = $param['member_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByProject($projectId, $creatorId);

        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        if ($memberId == $creatorId) {
            return $this->exception(setLang('CannotAndYouSelfToGroup'));
        }

        $checkMember = ProjectUserModel::where(['user_id' => $memberId, 'project_id' => $projectId, 'group_id' => $groupId])->find();
        if ($checkMember) {
            return $this->exception(setLang('MemberAlreadyExistsInProject'));
        }

        $projectMember = ProjectUserModel::create(
            [
                'user_id' => $memberId,
                'group_id' => $groupId,
                'project_id' => $projectId
            ]);

        if (!$projectMember) {
            return $this->exception(setLang('MemberAddError'));
        }
        return $this->success(setLang('MemberAddSuccess'));
    }

    /**
     *项目成员列表
     *
     * @param int $page //页码
     * @param int $size //每页数量
     * @param string $nickName //用户昵称
     * @param int $groupId //组织id
     * @param int $projectId // 项目id
     * @return  array
     *
     */
    public function memberList()
    {
        $page = input('get.page') ?? '1';
        $size = input('get.size') ?? '999';
        $nickName = input('get.nickname') ?? '';
        $groupId = input('get.group_id');
        $projectId = input('get.project_id');

        $group = GroupModel::where('id', $groupId)->find();
        if (!$group) {
            return $this->exception(setLang('GroupNotFound'));
        }

        $project = ProjectModel::where('id', $projectId)->find();
        if (!$project) {
            return $this->exception(setLang('ProjectNotFound'));
        }

        $where = [['group_id', '=', $groupId], ['project_id', '=', $projectId]];
        if ($nickName != '') {
            $userIdList = UserModel::where('nickname', 'like', '%' . $nickName . '%')->column('id');
            $where[] = ['user_id', 'in', $userIdList];
        }

        $projectUserList = ProjectUserModel::where($where)->page($page, $size)->select();
        //遍历$projectUserList 通过用户id获取用户信息
        foreach ($projectUserList as $k => $user) {
            $userInfo = UserModel::where('id', $user['user_id'])->find();
            $projectUserList[$k]['user'] = [
                'nickname' => $userInfo['nickname'],
                'avatar' => $userInfo['avatar'],
                'bio' => $userInfo['bio']
            ];
        }

        $count = count($projectUserList);
        return $this->success(['count' => $count, 'page' => $page, 'size' => $size, 'project_list' => $projectUserList]);
    }

    /**
     *剔除项目成员
     *
     * @param int $memberId //项目成员id
     * @param int $creatorId //创建人id
     * @param int $groupId //组织id
     * @param int $projectId //项目id
     * @return  array;
     */
    public function memberDel()
    {
        $param = Request::param();
        $header = Request::header();
        $token = $header['token'];

        $creatorId = $param['creator_id'];
        $memberIds = $param['member_ids']; // 改为 memberIds，表示可以接收多个成员ID
        $groupId = $param['group_id'];
        $projectId = $param['project_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByProject($projectId, $creatorId);

        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $project = ProjectModel::where(['creator_id' => $creatorId, 'id' => $projectId, 'group_id' => $groupId])->find();
        foreach ($memberIds as $memberId) {

            $checkMember = ProjectUserModel::where(["project_id" => $projectId, 'user_id' => $memberId, "group_id" => $groupId])->find();
            if (!$checkMember) {
                return $this->exception(setLang('MemberDoesNotExistOrHasBeenRemove'));
            }

            if ($project['creator_id'] == $checkMember['user_id']) {
                return $this->exception(setLang('UnableToExcludeOneSelf'));
            }
            $delMember = ProjectUserModel::where(['group_id' => $groupId, 'user_id' => $memberId, 'project_id' => $projectId])->delete();

            if (!$delMember) {
                return $this->exception(setLang('MemberRemoveError'));
            }
        }
        return $this->success(setLang('MemberRemoveSuccess'));
    }
}