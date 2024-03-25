<?php
/**
 * 讨论接口相关
 *
 * @author:uuz
 * @createTime: 2024-03-18
 */

namespace app\controller;

use app\BaseController;
use think\facade\Request;
use app\model\Group as GroupModel;
use app\model\User as UserModel;
use app\model\Discussion as DiscussionModel;
use app\validate\Discussion as DiscussionValidate;
use app\model\GroupUser as GroupUserModel;
use app\model\DiscussionComment as DiscussionCommentModel;
use app\validate\DiscussionComment as DiscussionCommentValidate;
use function app\setLang;

class Discussion extends BaseController
{
    /**
     *创建讨论组
     *
     * @param int $groupId //组织id
     * @param int $creatorId //创建人id
     * @param string $content //内容
     * @param string $tilte //标题
     * @return array
     */
    public function create()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $groupId = $param['group_id'];
        $title = $param['title'];
        $content = $param['content'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(DiscussionValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $group = GroupModel::where('id', $groupId)->find();
        $checkUser = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $creatorId])->find();

        if (!$group) {
            return $this->exception(setLang('GroupNotFound'));
        }

        if (!$checkUser) {
            return $this->exception(setLang('MemberDoesNotExistOrHasBeenRemove'));
        }

        $data = DiscussionModel::create(
            [
                'creator_id' => $creatorId,
                'group_id' => $groupId,
                'title' => $title,
                'content' => $content,
                'update_time' => time()
            ]
        );

        if (!$data) {
            return $this->exception(setLang('DiscussionReleaseError'));
        }

        return $this->success($data, setLang('DiscussionReleaseSuccess'));
    }

    /**
     *组织讨论组列表
     *
     * @param int $groupId //组织id
     * @param string $content //内容
     * @param string $tilte //标题
     * @param int $userId //用户id
     * @param int $page //页码
     * @param int $size //每页数量
     * @return array
     */
    public function  list()
    {
        $header = Request::header();

        $token = $header['token'];
        $groupId = input('get.group_id');
        $content = input('get.content') ?? '';
        $title = input('get.title') ?? '';
        $userId = input('get.user_id');
        $page = input('get.page') ?? 1;
        $size = input('get.size') ?? 10;

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $where = [];

        $checkGroup = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $userId])->find();

        if (!$checkGroup) {
            return $this->exception(setLang('MemberDoesNotExistOrHasBeenRemove'));
        }

        if ($content != '') {
            $where[] = ['content', 'like', '%' . $content . '%'];
        }
        if ($title != '') {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }

        $discussion = DiscussionModel::where($where)->where(['group_id' => $groupId])
            ->field('id,group_id,creator_id,title,status,create_time,update_time')
            ->order('update_time', 'desc')
            ->page($page, $size)
            ->select();

        foreach ($discussion as $discussionItem) {
            $userList = UserModel::where('id', $discussionItem['creator_id'])->find();
            $commentUser = DiscussionCommentModel::where(['discussion_id' => $discussionItem['id'], 'status' => 1])->select();
            $discussionItem['creator'] = [
                'nickname' => $userList['nickname'],
                'avatar' => $userList['avatar']
            ];

            // 对应评论
            $comments = [];
            $commentUserIds = []; // 用于存储已添加的评论用户Id，去重
            foreach ($commentUser as $comment) {
                if ($comment['user_id'] != $discussionItem['creator_id']) { // 判断评论用户是否为创建者
                    if (!in_array($comment['user_id'], $commentUserIds)) {
                        $commentUserList = UserModel::where('id', $comment['user_id'])->find();
                        $comments[] = [
                            'nickname' => $commentUserList['nickname'],
                            'avatar' => $commentUserList['avatar'],
                        ];

                        $commentUserIds[] = $comment['user_id'];
                    }
                }
            }

            $discussionItem['comment_user'] = $comments;
            $discussionItem['comment_count'] = count($commentUser); // 计算评论总数
            $discussionItem['user_count'] = count($commentUserIds); // 去重获取的评论用户数量
        }

        if (!$discussion) {
            return $this->exception(setLang('GetDiscussionListError'));
        }

        return $this->success(['page' => $page, 'size' => $size, 'discussion_list' => $discussion]);
    }

    /**
     *组织讨论组更新
     *
     * @param int $groupId //组织id
     * @param string $content //内容
     * @param string $tilte //标题
     * @param int $discussionId //讨论id
     * @param int $creatorId //创建人id
     * @return array
     */
    public function update()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $groupId = $param['group_id'];
        $discussionId = $param['discussion_id'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(DiscussionValidate::class)->scene('update')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $checkUser = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $creatorId])->find();
        if (!$checkUser) {
            return $this->exception(setLang('MemberDoesNotExistOrHasBeenRemove'));
        }

        $discussion = DiscussionModel::where(['id' => $discussionId, 'status' => 1])->find();
        if (!$discussion) {
            return $this->exception(setLang('DiscussionNotFound'));
        }

        $title = $param['title'] ?? $discussion['title'];
        $content = $param['content'] ?? $discussion['content'];

        if ($creatorId != $discussion['creator_id']) {
            return $this->exception(setLang('NoPermission'));
        }

        $data = DiscussionModel::where('id', $discussionId)->update(['title' => $title, 'content' => $content, 'update_time' => time(),]);

        if (!$data) {
            return $this->exception(setLang('DiscussionUpdateError'));
        }

        $update = DiscussionModel::where('id', $discussionId)->find();

        return $this->success($update, setLang('DiscussionUpdateSuccess'));
    }

    /**
     *组织讨论组删除
     *
     * @param int $groupId //组织id
     * @param int $discussionId //讨论id
     * @param int $creatorId //创建人id
     * @return array
     */
    public function del()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $discussionId = $param['discussion_id'];
        $groupId = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $group = GroupModel::where('id', $groupId)->find();
        $discussion = DiscussionModel::where('id', $discussionId)->find();
        if (!$group) {
            return $this->exception(setLang('GroupNotFound'));
        }

        if ($creatorId != $discussion['creator_id'] && $creatorId != $group['creator_id']) {
            return $this->exception(setLang('NotCreatorOrGroupCreatorCanNotDelete'));
        }

        $delete = DiscussionModel::where('id', $discussionId)->update(['status' => 0, 'delete_time' => time()]);
        if (!$delete) {
            return $this->exception(setLang('DeleteError'));
        }

        return $this->success(setLang('DeleteSuccess'));
    }

    /**
     *组织讨论组评论发布
     *
     * @param int $groupId //组织id
     * @param int $discussionId //讨论id
     * @param int $userId //用户id
     * @param string $comment // 评论内容
     * @return array
     *
     */
    public function addComment()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $groupId = $param['group_id'];
        $discussionId = $param['discussion_id'];
        $userId = $param['user_id'];
        $comment = $param['comment'];

        try {
            Common::checkByTokenUid($token, $userId);
            validate(DiscussionCommentValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $checkMember = GroupUserModel::where(['group_id' => $groupId, 'user_id' => $userId])->find();
        if (!$checkMember) {
            return $this->exception(setLang('NoPermission'));
        }

        $discussion = DiscussionModel::where(['id' => $discussionId, 'status' => 1])->find();
        if (!$discussion) {
            return $this->exception(setLang('DiscussionNotFound'));
        }

        $data = DiscussionCommentModel::create(
            [
                'user_id' => $userId,
                'comment' => $comment,
                'group_id' => $groupId,
                'discussion_id' => $discussionId,
            ]);

        if (!$data) {
            return $this->exception(setLang('CommentReleaseError'));
        }

        return $this->success($data, setLang('CommentReleaseSuccess'));
    }

    /**
     *组织讨论组评论列表
     *
     * @param int $groupId //组织id
     * @param int $discussionId //讨论组id
     * @param int $userId //用户id
     * @param int $page //页码
     * @param int $size //每页数量
     * @param string $order //排序方式
     * @return array
     *
     */
    public function CommentList()
    {
        $header = Request::header();

        $token = $header['token'];
        $groupId = input('get.group_id');
        $discussionId = input('get.discussion_id');
        $userId = input('get.user_id');
        $page = input('get.page') ?? 1;
        $size = input('get.size') ?? 10;
        $order = input('get.order') ?? 'asc';

        try {
            Common::checkByTokenUid($token,$userId);
            Common::checkByGroup($groupId, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $data = DiscussionCommentModel::where(['discussion_id' => $discussionId, 'status' => 1])
            ->page($page, $size)
            ->order('create_time', $order)->select();

        foreach ($data as &$commentList) {
            $userList = UserModel::where('id', $commentList['user_id'])->find();
            $commentList['user_list'] =
                [
                    'nickname' => $userList['nickname'],
                    'avatar' => $userList['avatar'],
                ];
        }

        if (!$data) {
            return $this->exception(setLang('CommentListGetError'));
        }

        return $this->success($data);
    }

    /**
     *组织讨论组评论删除
     *
     * @param int $groupId //组织id
     * @param int $discussionId //讨论组id
     * @param int $userId //用户id
     * @param int $commentId //评论id
     * @return array
     */
    public function commentDel()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $groupId = $param['group_id'];
        $discussionId = $param['discussion_id'];
        $userId = $param['user_id'];
        $commentId = $param['comment_id'];

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(),$e->getMessage());
        }

        $group = GroupModel::where('id', $groupId)->find();
        $discussion = DiscussionModel::where('id', $discussionId)->find();
        $comment = DiscussionCommentModel::where(['user_id' => $userId, 'discussion_id' => $discussionId])->find();
        if ($userId != $comment['user_id'] && $userId != $group['creator_id'] && $userId != $discussion['creator_id']) {
            return $this->exception(setLang('NotYouSelfCreatorOrGroupCreatorCanNotDelete'));
        }

        $delete = DiscussionCommentModel::where(['discussion_id' => $discussionId, 'id' => $commentId])->update(['status' => 0, 'delete_time' => time()]);

        if (!$delete) {
            return $this->exception('CommentDeleteError');
        }

        return $this->success('CommentDeleteSuccess');
    }

    /**
     *获取讨论详情
     *
     * @param int $discussionId //讨论id
     * @param string $order //排序
     * @return  array;
     */
    public function discussionContent()
    {
        $discussionId = input('get.discussion_id');
        $order = input('get.order') ?? 'asc';

        $discussion = DiscussionModel::where('id', $discussionId)->find();
        $creator = UserModel::where('id', $discussion['creator_id'])->field('nickname,avatar')->find();

        if (!$discussion) {
            return $this->exception(setLang('DiscussionNotFound'));
        }

        $comments = DiscussionCommentModel::where(['discussion_id' => $discussionId, 'status' => 1])->order('create_time', $order)->select();
        foreach ($comments as $comment) {
            $user = UserModel::where('id', $comment['user_id'])->find();
            $comment['comment_user'] =
                [
                    'nickname' => $user['nickname'],
                    'avatar' => $user['avatar']
                ];
        }

        $discussion['creator'] = $creator;
        $discussion['comments'] = $comments;
        return $this->success($discussion);
    }


}