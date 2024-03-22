<?php
/**
 * 通知接口相关
 *
 * @author:uuz
 * @createTime: 2024-03-18
 */

namespace app\controller;

use app\BaseController;
use app\model\Notice as NoticeModel;
use app\validate\Notice as NoticeValidate;
use app\model\Group as GroupModel;
use app\model\User as UserModel;
use think\Exception;
use think\facade\Request;
use function app\setLang;

class Notice extends BaseController
{
    /**
     *创建通知
     *
     * @param int $groupId //组织id
     * @param int $creatorId //创建人id
     * @param string $content //内容
     * @return array
     */
    public function create()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $groupId = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);
            validate(NoticeValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $content = $param['content'];

        $data = NoticeModel::create
        (
            [
                'group_id' => $groupId,
                'creator_id' => $creatorId,
                'content' => $content
            ]
        );

        if (!$data) {
            return $this->exception(setLang('CreateNoticeError'));
        }

        return $this->success($data, setLang('CreateNoticeSuccess'));

    }

    /**
     *通知列表查询
     *
     * @param int $groupId //组织id
     * @param int $page //页码
     * @param int $size //每页数量
     * @return array
     *
     */
    public function list()
    {
        $groupId = input('get.group_id');
        $page = input('get.page') ?? 1;
        $size = input('get.size') ?? 10;

        $data = NoticeModel::where(['group_id' => $groupId, 'status' => 1])->page($page, $size)->order('update_time', 'desc')->select();

        return $this->success(['page' => $page, 'size' => $size, 'notice_list' => $data]);

    }

    /**
     *通知更新
     *
     * @param int $groupId //组织id
     * @param int $noticeId //通知id
     * @param int $creatorId //创建者id
     * @param string $content //通知内容
     * @return array
     */
    public function update()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $groupId = $param['group_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);
            validate(NoticeValidate::class)->scene('update')->check($param);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $noticeId = $param['notice_id'];
        $content = $param['content'];
        $time = time();

        $data = NoticeModel::where('id', $noticeId)->update(
            [
                'content' => $content,
                'update_time' => $time
            ]
        );

        if (!$data) {
            return $this->exception(setLang('NoticeUpdateError'));
        }

        return $this->success(setLang('NoticeUpdateSuccess'));

    }

    /**
     *通知删除
     *
     * @param int $groupId //组织id
     * @param int $noticeId //通知id
     * @param int $creatorId //创建者id
     * @return array
     *
     */
    public function del()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $groupId = $param['group_id'];
        $noticeId = $param['notice_id'];


        try {
            Common::checkByTokenUid($token, $creatorId);
            Common::checkByGroup($groupId, $creatorId);
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }

        $data = NoticeModel::where('id', $noticeId)->update(
            [
                'status' => 0,
                'delete_time' => time()
            ]
        );

        if (!$data) {
            return $this->exception(setLang('NoticeDeleteError'));
        }
        return $this->success(setLang('NoticeDeleteSuccess'));
    }
}