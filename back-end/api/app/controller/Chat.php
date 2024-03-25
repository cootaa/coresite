<?php

/**
 * 聊天接口相关
 *
 * @author:uuz
 * @createTime: 2024-03-11
 */


namespace app\controller;
use app\BaseController;
use think\facade\Request;
use app\model\Chat as ChatModel;
use app\validate\Chat as ChatValidate;
use app\model\Project as ProjectModel;
use app\model\User as UserModel;
use app\model\ProjectUser as ProjectUserModel;
use function app\setLang;

class Chat extends BaseController
{
    /**
     *保存聊天记录
     *
     * @param int $projectId //项目id
     * @param int $userId //用户id
     * @param string $message //单条聊天内容
     * @return array
     */
    public function save()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $projectId = $param['project_id'];
        $message = $param['message'];
        $userId = $param['user_id'];

        try {
            Common::checkByTokenUid($token, $userId);
            validate(ChatValidate::class)->scene('save')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $project = ProjectModel::where('id', $projectId)->find();
        if (!$project) {
            return $this->exception(setLang('projectNotFound'));
        }

        $data = ChatModel::create(
            [
                'project_id' => $projectId,
                'user_id' => $userId,
                'message' => $message
            ]);

        if (!$data) {
            return $this->exception(setLang('ChatSaveError'));
        }
        return $this->success([]);
    }

    /**
     *聊天记录查询
     *
     * @param int $page //页码
     * @param int $size // 每页数量
     * @param int $projectId //项目id
     * @param string $nickName //消息人
     * @param string $message //消息
     * @param int $userId //用户id
     * @return array
     *
     */
    public function list()
    {
        $header = Request::header();
        $token = $header['token'];
        $userId = input('get.user_id');

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $page = input('get.page') ?? '1';
        $size = input('get.size') ?? '10';
        $projectId = input('get.project_id');
        $nickName = input('get.nickname') ?? '';
        $message = input('get.message') ?? '';

        $projectUser = ProjectUserModel::where(['project_id' => $projectId, 'user_id' => $userId])->find();
        if (!$projectUser) {
            return $this->exception(setLang('UserIsNotAMemberOfTheProject'));
        }

        //获取用户id
        $userIds = UserModel::where('nickname', 'like', '%' . $nickName . '%')->column('id');
        $chatList = ChatModel::where('project_id', $projectId)
            ->whereIn('user_id', $userIds)//筛选出用户id在userId数组的记录
            ->where('message', 'like', '%' . $message . '%')
            ->with('user')//预加载,关联用户表模型
            ->page($page, $size)
            ->order('create_time', 'desc')
            ->select();

        foreach ($chatList as $k => $chat) {
            $userInfo = $chat->user;
            if ($userInfo) {
                $chatList[$k]['user'] = [
                    'nickname' => $userInfo['nickname'],
                    'avatar' => $userInfo['avatar']
                ];
            }
        }

        $count = count($chatList);
        return $this->success(['count' => $count, 'page' => $page, 'size' => $size, 'chat_list' => $chatList]);
    }


}