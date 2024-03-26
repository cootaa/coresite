<?php

namespace app\controller;

use app\BaseController;
use think\facade\Cache;
use think\facade\Request;
use app\model\File as FileModel;
use app\model\Folder as FolderModel;
use app\model\User as UserModel;
use function app\setLang;


class File extends BaseController
{
    /**
     * 文件重命名
     *
     * @param string $name //文件名
     * @param int $creatorId //创建人
     * @param int $folderId //文件夹id
     * @param int $fileId //文件夹id
     * @return array
     */
    public function rename()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $name = $param['name'];
        $creatorId = $param['creator_id'];
        $folderId = $param['folder_id'];
        $fileId = $param['file_id'];
        $timeStamp = time();

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {

            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $file = FileModel::where(['folder_id' => $folderId, 'creator_id' => $creatorId, 'id' => $fileId])->find();

        if (!$file) {
            return $this->exception(setLang('FileNotfound'));
        }

        if ($file['creator_id'] != $creatorId) {
            return $this->exception(setLang('NoPermission'));
        }

        $data = FileModel::where(['folder_id' => $folderId, 'creator_id' => $creatorId, 'id' => $fileId])
            ->update(['name' => $name, 'update_time' => $timeStamp]);

        if (!$data) {
            return $this->exception(setLang('RenameError'));
        }

        $updateFile = FileModel::where('id', $fileId)->find();

        return $this->success($updateFile, setLang('RenameSuccess'));
    }

    /**
     * 文件列表
     *
     * @param string $name //文件名
     * @param int $folderId //文件夹id
     * @return array
     */
    public function list()
    {
        $header = Request::header();

        $token = $header['token'];
        $name = input('get.name') ?? '';
        $folderId = input('get.folder_id') ?? '';
        $userId = input('get.user_id');

        try {
            Common::checkByTokenUid($token, $userId);
        } catch (\Exception $e) {

            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $where = [];
        if ($name != '') {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        if ($folderId != '') {
            $where[] = ['folder_id', '=', $folderId];
        }

        $fileList = FileModel::where($where)->select();
        foreach ($fileList as $k => $file) {
            // 确保$file是数组形式，如果是模型对象，转换为数组
            $fileData = $file->toArray();

            $userInfo = UserModel::where('id', $fileData['creator_id'])->find();
            if ($userInfo) {
                $fileList[$k]['creator'] = [
                    'nickname' => $userInfo->nickname,
                    'avatar' => $userInfo->avatar,
                ];
            }
        }

        $count = count($fileList);

        return $this->success(['count' => $count, 'file_list' => $fileList]);
    }

    /**
     *删除文件
     *
     * @param int $folderId //文件夹id
     * @param int $fileId //文件id
     * @param int $creatorId //创建人id
     * @return array;
     */
    public function del()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $folderId = $param['folder_id'];
        $creatorId = $param['creator_id'];
        $fileIds = is_array($param['file_id'])?$param['file_id']:[$param['file_id']];

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        foreach ($fileIds as $fileId) {
            $file = FileModel::where('id', $fileId)
                ->where('folder_id', $folderId)
                ->find();

            if (!$file) {
                return $this->exception(setLang('FileNotFound'));
            }

            if($file['creator_id']!=$creatorId)
            {
                return $this->exception(setLang('NoPermission'));
            }

            $deleteCount = FileModel::where('id', $fileId)
                ->where('folder_id', $folderId)
                ->delete();

            if ($deleteCount !== 1) {
                return $this->exception(setLang('FileDeleteError'));
            }
        }

        return $this->success(setLang('FileDeleteSuccess'));
    }


    /**
     *文件移动
     *
     * @param int $folderId //文件夹id
     * @param int $fileId //文件id
     * @param int $creatorId //创建人id
     * @param int $confirm //是否覆盖文件
     * @return array;
     */
    public function remove()
    {
        $param = Request::param();
        $header = Request::header();

        $token = $header['token'];
        $folderId = $param['folder_id'];
        $creatorId = $param['creator_id'];
        $fileIds = is_array($param['file_id'])?$param['file_id']:[$param['file_id']];
        $confirm = $param['confirm'] ?? 0;

        try {
            Common::checkByTokenUid($token, $creatorId);
        } catch (\Exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        foreach ($fileIds as $fileId) {
            $file = FileModel::where('id', $fileId)->find();

            if (!$file) {
                return $this->exception(setLang('FileNotFound'));
            }

            if($file['creator_id']!=$creatorId)
            {
                return $this->exception(setLang('NoPermission'));
            }

            if ($creatorId != $file['creator_id']) {
                return $this->exception(setLang('NoPermission'));
            }

            $folder = FolderModel::where('id', $folderId)->find();
            if (!$folder) {
                return $this->exception(setLang('FolderNotFound'));
            }

            // 判断移动文件至目标文件夹内是否有重名文件
            $duplicateFile = FileModel::where(['folder_id' => $folderId, 'name' => $file['name'], 'format' => $file['format']])->find();
            if ($duplicateFile && $duplicateFile['id'] !== $fileId && $confirm == 0) {
                return $this->success(\app\controller\Common::duplicate, setLang('MoveToTheDestinationFolderWithDuplicateFileNames'));
            }

            // 如果确认覆盖文件
            if ($confirm == 1) {
                if ($duplicateFile) {
                    FileModel::where('id', $duplicateFile['id'])->delete();
                }
                FileModel::where('id', $fileId)->update(['folder_id' => $folderId]);
            } else {
                FileModel::where('id', $fileId)->update(['folder_id' => $folderId]);
            }
        }

        return $this->success(\app\controller\Common::cover, setLang('FileRemoveSuccess'));
    }


    /**
     *上传文件
     *
     * @param int $folderId //文件夹id
     * @param string $url //图片路径
     * @param string $name //文件名
     * @param string $type //文件类型
     * @param string $format //文件格式(后缀)
     * @param int $size //文件大小
     * @param int $creatorId //上传者id
     * @return array;
     */
    public function upload()
    {
        $param = request::param();
        $header = request::header();

        $token = $header['token'];
        $creatorId = $param['creator_id'];

        try {
            \app\controller\common::checkbytokenuid($token, $creatorId);
            \app\controller\Common::checkUserMemory($token);
        } catch (\exception $e) {
            return $this->Catchexception($e->getCode(), $e->getMessage());
        }

        $filesdata = $param['files'];

        $duplicatefiles = []; // 存储重复文件名

        foreach ($filesdata as $filedata) {
            $chatFolderId = foldermodel::where(['id' => $filedata['folder_id'], 'name' => 'chat'])->find();

            //如果上传目标为chat文件夹,则文重复文件名+副本
            if ($chatFolderId && $chatFolderId['name'] === 'chat') {
                $checkFilename = filemodel::where(['folder_id' => $chatFolderId['id'], 'name' => $filedata['name']])->count();

                $filename = $checkFilename > 0 ? $filedata['name'] . '-副本' . ($checkFilename + 1) : $filedata['name'];

                while (filemodel::where(['folder_id' => $chatFolderId['id'], 'name' => $filename])->find()) {
                    $checkFilename++;
                    $filename = $filedata['name'] . '-副本' . ($checkFilename + 1);
                }

                $data = filemodel::create([
                    'folder_id' => $chatFolderId['id'],
                    'name' => $filename,
                    'type' => $filedata['type'],
                    'format' => $filedata['format'],
                    'size' => $filedata['size'],
                    'creator_id' => $creatorId,
                    'url' => $filedata['url']
                ]);

                if (!$data) {
                    return $this->exception(setlang('filesaveerror'));
                }
            } else {
                $existingfile = filemodel::where(['folder_id' => $filedata['folder_id'], 'name' => $filedata['name'], 'format' => $filedata['format']])->find();

                $newFileName = $filedata['name'] . $filedata['format'];

                if ($existingfile) {
                    $duplicatefiles[] = $newFileName;// 将重复文件加入数组
                    continue; // 跳过当前文件，继续处理下一个文件
                }

                $data = filemodel::create([
                    'folder_id' => $filedata['folder_id'],
                    'name' => $filedata['name'],
                    'type' => $filedata['type'],
                    'format' => $filedata['format'],
                    'size' => $filedata['size'],
                    'creator_id' => $creatorId,
                    'url' => $filedata['url']
                ]);

                if (!$data) {
                    return $this->exception(setlang('filesaveerror'));
                }
            }
        }

        if (!empty($duplicatefiles) && $param['confirm'] == 0) {
            return $this->success(\app\controller\Common::duplicate, setLang('DuplicateFiles'));
        }

        //如果确认覆盖文件,执行更新重复文件
        if ($param['confirm'] == 1) {
            foreach ($param['files'] as $file) {
                filemodel::where(['folder_id' => $file['folder_id'], 'name' => $file['name']])
                    ->where('type', $file['type'])
                    ->update([
                        'format' => $file['format'],
                        'size' => $file['size'],
                        'url' => $file['url'],
                        'update_time' => time()
                    ]);
            }
        }
        return $this->success(\app\controller\Common::cover, setlang('filesavesuccess'));
    }

    /**
     *文件下载
     *
     * @param int $userId //用户id
     * @param int $fileId //文件id
     * @return  array
     */
    public function getFileUrl()
    {
        $params = Request::param();
        $redis = Cache::store('redis');

        $fileId = $params['file_id'];

        $file = FileModel::where('id', $fileId)->find();

        //将获取到的文件url转换为哈希值
        $hash = hash('sha256', $file['url']);

        //将生成的哈希值作为建名存储文件信息
        $redis->set($hash, json_encode($file));

        //拼接下载文件链接
        $download_url = 'http://' . $_SERVER['HTTP_HOST'] . '/file/download/' . $hash;

        $data = [
            'file_name' => $file['name'],
            'download_url' => $download_url
        ];

        return $this->success($data);
    }

    /**
     * 文件下载
     *
     * @param string $hash //文件哈希值
     * @return void
     */
    public function download($hash)
    {
        $redis = Cache::store('redis');
        $fileInfo = $redis->get($hash);

        if ($fileInfo) {
            $file = json_decode($fileInfo, true);

            // 新文件名和格式
            $newFileName = $file['name'];
            $format = $file['format'];

            // 获取文件内容
            $fileContent = file_get_contents($file['url']);

            // 设置 HTTP 头信息，指定文件下载的名称
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $newFileName . $format . '"');
            // 输出文件内容
            echo $fileContent;
            exit; // 结束执行，确保只输出文件内容
        } else {
            return $this->exception('FileIsNull');
        }
    }


}

