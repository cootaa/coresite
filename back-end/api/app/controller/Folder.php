<?php
/**
 * 文件夹相关接口
 *
 * @author: uuz
 * @createTime: 2024-03-08
 */

namespace app\controller;

use app\BaseController;
use think\facade\Request;
use app\model\File as FileModel;
use app\model\Folder as FolderModel;
use app\model\Project as ProjectModel;
use app\model\User as UserModel;
use function app\setLang;
use app\validate\Folder as FolderValidate;

class Folder extends BaseController
{
    /**
     *文件夹列表
     *
     * @param string $folderName //文件夹名称
     * @param int $parentId //上级文件夹
     * @param int $projectId //项目id
     * @return array
     */
    public function list()
    {
        $header = Request::header();

        $token = $header['token'];
        $folderName = input('get.name') ?? '';
        $projectId = input('get.project_id');
        $folderId = input('get.folder_id') ?? '';
        $userId = input('get.user_id');

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $where[] = ['project_id', '=', $projectId];
        $folderList = FolderModel::where($where);
        if ($folderName != '') {
            $where[] = ['name', 'like', '%' . $folderName . '%'];
        }

        if ($folderId != '') {
            $folderList = $folderList->where(['parent_id' != null, 'id' => $folderId]);
        }

        $result = [];
        $folderList = $folderList->where($where)->select();

        foreach ($folderList as $folderData) {

            // 获取文件夹创建者信息
            $folderCreator = UserModel::where('id', $folderData['creator_id'])->find();
            $folderData['creator'] = [
                'nickname' => $folderCreator['nickname'],
                'avatar' => $folderCreator['avatar']
            ];

            // 获取文件信息
            $files = FileModel::where('folder_id', $folderData['id'])->select();
            foreach ($files as $file) {
                // 获取文件创建者信息
                $fileCreator = UserModel::where('id', $file['creator_id'])->find();
                $file['creator'] = [
                    'nickname' => $fileCreator['nickname'],
                    'avatar' => $fileCreator['avatar']
                ];
            }
            $folderData['files'] = $files;

            // 获取子文件夹信息
            $subFolders = FolderModel::where('parent_id', $folderData['id'])->select();
            $folderData['sub_folders'] = $subFolders;
            $count = count($files) + count($subFolders);
            $result[] = $folderData;
        }

        return $this->success(['folder_info' => $result[0], 'count' => $count]);
    }


    /**
     *创建文件夹
     *
     * @param int $projectId //项目id
     * @param string $name //文件夹名称
     * @param int $parentId //上级文件夹id
     * @param int $creatorId //创建人id
     * @return  array
     */
    public function create()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];
        $name = $param['name'];
        $projectId = $param['project_id'];
        $parentId = $param['parent_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(FolderValidate::class)->scene('create')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $project = ProjectModel::where(['id' => $projectId])->find();

        if (!$project) {
            return $this->exception(setLang('ProjectNotFound'));
        }

        $checkFolder = FolderModel::where(['project_id' => $projectId, 'name' => $name, 'creator_id' => $creatorId, 'parent_id' => $parentId])->find();
        if ($checkFolder != null) {
            return $this->exception(setLang('FolderAlreadyExists'));
        }

        $folder = FolderModel::create(
            [
                'project_id' => $projectId,
                'name' => $name,
                'creator_id' => $creatorId,
                'parent_id' => $parentId
            ]
        );
        return $this->success($folder, setLang('FolderCreateSuccess'));
    }

    /**
     *文件夹重名
     *
     * @param string $name //文件夹名称
     * @param int $folderId //文件夹id
     * @param int $creatorId // 创建人id
     * @param int $projectId // 项目id
     * @return array
     */
    public function rename()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $name = $param['name'];
        $folderId = $param['folder_id'];
        $projectId = $param['project_id'];
        $creatorId = $param['creator_id'];
        $timeStamp = time();

        try {
            Common::checkByTokenUid($token, $creatorId);
            validate(FolderValidate::class)->scene('rename')->check($param);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $folder = FolderModel::where(['id' => $folderId, 'creator_id' => $creatorId, 'project_id' => $projectId])
            ->update(
                [
                    'name' => $name,
                    'update_time' => $timeStamp
                ]
            );
        if (!$folder) {
            return $this->exception(setLang('RenameError'));
        }

        $updateFolder = FolderModel::where('id', $folderId)->find();
        return $this->success($updateFolder, setLang('RenameSuccess'));
    }

    /**
     *文件夹移动
     *
     * @param int @parentId //上级文件夹
     * @param int @folderId //文件夹id
     * @param int @creatorId //创建人id
     * @return array
     */
    public function remove()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $folderId = $param['folder_id'];
        $parentId = $param['parent_id'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $folder = FolderModel::where(['id' => $folderId, 'creator_id' => $param['creator_id']])->find();

        if ($creatorId != $folder['creator_id']) {
            return $this->exception(setLang('NoPermission'));

        }

        $parent = FolderModel::where('id', $parentId)->find();

        if (!$parent) {
            return $this->exception(setLang('FolderNotFound'));
        }

        if ($parent['parent_id'] == $folder['id']) {

            return $this->exception(setLang('TargetFolderIsASubFolderOfTheSourceFolder'));
        }

        if ($folder['parent_id'] == $parent['id']) {
            return $this->exception(setLang('FolderAlreadyExistsInTheCurrentFolder'));
        }

        //判断移动目标文件夹是否重复,是否覆盖
        $duplicateFolder = FolderModel::where('parent_id', $parentId)
            ->where('name', $folder['name'])
            ->find();

        if ($duplicateFolder && $duplicateFolder['id'] !== $folderId) {
            return $this->exception(setLang('MoveToTheDestinationFolderWithDuplicateFolderNames'));
        }

        // 执行移动文件夹的操作
        $folderRemove = FolderModel::where([
            'id' => $folderId,
            'creator_id' => $creatorId
        ])->update([
            'parent_id' => $parentId
        ]);

        if (!$folderRemove) {
            return $this->exception(setLang('FolderRemoveError'));
        }
        return $this->success(setLang('FolderRemoveSuccess'));
    }

    /**
     *删除文件夹
     *
     * @param int $projectId //项目id
     * @param int $folderId //文件夹id
     * @param int $creatorId //创建人id
     * @return array;
     */
    public function del()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $projectId = $param['project_id'];
        $folderId = $param['folder_id'];
        $creatorId = $param['creator_id'];

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $folder = FolderModel::where(['project_id' => $projectId, 'id' => $folderId, 'creator_id' => $creatorId])->find();
        if (!$folder) {
            return $this->exception(setLang('FolderNotFound'));
        }

        $file = FileModel::where('folder_id', $folderId)->find();
        if ($file) {
            return $this->exception(setLang('FolderHaveFileCanNotDelete'));
        }

        $delFolder = FolderModel::where(['project_id' => $projectId, 'id' => $folderId, 'creator_id' => $creatorId])->delete();
        if (!$delFolder) {
            return $this->exception(setLang('FolderDeleteError'));
        }
        return $this->success(setLang('FolderDeleteSuccess'));
    }


    /**
     *文件夹树
     *
     * @param int $projectId //项目id
     * @return  array
     */
    public function folderTree()
    {
        $projectId = input('get.project_id');

        $result = [];

        $folder = FolderModel::where('project_id', $projectId)->find();
        if ($folder) {
            $result = $this->getFolderInfo($folder);
        }

        return $this->success(['folder_info' => $result]);
    }

    /**
     *文件夹树获取子文件夹
     *
     * @param $folder
     * @return  array
     */
    public function getFolderInfo($folder)
    {
        $folderData = $folder->toArray();

        $subFolders = FolderModel::where('parent_id', $folder['id'])->select();//通过父文件夹获取子文件夹
        if (!$subFolders->isEmpty()) {
            $folderData['sub_folders'] = $subFolders->map(function ($subFolder) {
                $subFolderData = $this->getFolderInfo($subFolder);
                return $subFolderData;
            })->all();
        }

        return $folderData;
    }
}