<?php

/**
 * 公共控制器
 *
 * @author: dr
 * @createTime: 2024/02/20
 */

namespace app\controller;

use app\BaseController;
use app\model\Folder as FolderModel;
use app\model\ProjectUser as ProjectUserModel;
use app\model\User as UserModel;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Request;
use OSS\Core\OssException;
use OSS\OssClient;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\model\Group as groupModel;
use app\model\Project as projectModel;
use app\model\File as FileModel;
use function app\setLang;

class Common extends BaseController
{

    /**
     * 阿里云OSS配置
     *
     **/
    private static $accessKeyId;
    private static $accessKeySecret;
    private static $endpoint;
    private static $bucket;

    /**
     * JWT配置
     */
    const key = 'JWT-key@CoreSite';
    const expTime = 7200;

    public function __construct()
    {
        $this->setConstants();
    }


    private function setConstants()
    {
        self::$accessKeyId = Env::get('ALIYUN_OOS.ACCESSKEYID');
        self::$accessKeySecret = Env::get('ALIYUN_OOS.ACCESSKEYSCECRET');
        self::$endpoint = Env::get('ALIYUN_OOS.ENDPOINT');
        self::$bucket = Env::get('ALIYUN_OOS.BUCKET');
    }


    /**
     *文件重复和覆盖状态码
     */
    const duplicate = 2; //重复
    const cover = 1; //覆盖

    /**
     * 上传图片(阿里云OSS)
     *
     * @param object $file 文件对象
     * @param string $type 图片用途 （avatar:头像）
     * @return string $url 文件访问绝对路径
     * 示例：http://cota-coresite.oss-cn-hangzhou.aliyuncs.com/avatar/2024/03/5434112530203cf678515c15b7cec468e1e3f736.png
     */
    public function uploadImg()
    {

        $param = Request::param();
        $files = $_FILES['file'];

        $year = date('Y'); // 获取当前年份
        $month = date('m'); // 获取当前月份
        $name = $files['name']; // 获取文件名（包含后缀）
        $format = strrchr($name, '.'); // 截取文件后缀名如 (.jpg)
        $filePath = $files['tmp_name']; // 本地文件临时路径
        $genFileName = sha1(date('YmdHis', time()) . uniqid()) . $format; // 新文件名构造
        $object = 'avatar/' . $year . '/' . $month . '/' . $genFileName; // 新文件存放完整路径

        try {
            $ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint);

            $result = $ossClient->uploadFile(self::$bucket, $object, $filePath);
            if (!$result) {
                return $this->exception(setLang('UploadError'));
            } else {
                return $this->success([
                    'url' => $result['info']['url']
                ]);
            }
        } catch (OssException $e) {
            return $this->exception($e);
        }
    }

    /**
     * 上传文件(阿里云OSS)
     *
     * @param object $file 文件对象
     * @param number $group_id 组织id
     * @param number $project_id 项目id
     */
    public function uploadFile()
    {
        $param = Request::param();
        $header = Request::header();

        $files = $_FILES['file'];
        $token = $header['token'];
        $groupId = $param['group_id'];
        $projectId = $param['project_id'];
        $name = $files['name']; // 获取文件名（包含后缀）
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $size = $files['size']; // 文件大小
        $type = $files['type']; // 文件类型
        $format = strrchr($name, '.'); // 截取文件后缀名
        $filePath = $files['tmp_name']; // 本地文件临时路径
        $genFileName = sha1(date('YmdHis', time()) . uniqid()) . $format; // 新文件名构造
        $object = 'group/' . $groupId . '/' . $projectId . '/' . $genFileName; // 新文件存放完整路径
        try {
            $ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint);
            $this->checkUserMemory($token);
            $result = $ossClient->uploadFile(self::$bucket, $object, $filePath);

            if (!$result) {
                return $this->exception(setLang('UploadError'));
            } else {
                return $this->success([
                    'url' => $result['info']['url'],
                    'name' => $filename,
                    'format' => $format,
                    'size' => $size,
                    'type' => $type,
                ]);
            }
        } catch (\Exception $e) {
            return $this->exception($e->getMessage());
        }
    }

    /**
     * 创建 token
     *
     * @param token 过期时间 单位:秒 例子：7200=2小时
     * @return string
     */
    public static function createToken()
    {
        $nowTime = time();
        try {
            $token['iss'] = ''; //签发者 可选
            $token['aud'] = ''; //接收该JWT的一方，可选
            $token['iat'] = $nowTime; //签发时间
            $token['nbf'] = $nowTime + 30; //某个时间点后才能访问
            $token['exp'] = $nowTime + self::expTime; //token过期时间,这里设置2个小时
            $token['uid'] = ''; //自定义参数
            $token['name'] = ''; //自定义参数
            $token['mobile'] = ''; //自定义参数
            $json = JWT::encode($token, self::key, "HS256");
            return $json;
        } catch (\Firebase\JWT\ExpiredException $e) {//签名不正确
            $returndata['code'] = "104";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        } catch (\Exception $e) {//其他错误
            $returndata['code'] = "199";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        }
    }

    /**
     * 验证token是否有效
     *
     * @param string $jwt 需要验证的token
     */
    public static function checkToken($jwt = '')
    {
        try {
            JWT::$leeway = 60; //当前时间减去60，把时间留点余地
            $decoded = JWT::decode($jwt, new Key(self::key, 'HS256')); //HS256方式，这里要和签发的时候对应
            $arr = (array)$decoded;
            $returndata['code'] = "200";
            $returndata['msg'] = setLang('SUCCESS');
            $returndata['data'] = $arr;
            return json_encode($returndata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $returndata['code'] = "101";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            $returndata['code'] = "102";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        } catch (\Firebase\JWT\ExpiredException $e) {
            $returndata['code'] = "103";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        } catch (\Exception $e) {
            $returndata['code'] = "199";
            $returndata['msg'] = $e->getMessage();
            $returndata['data'] = "";
            return json_encode($returndata);
        }

    }

    /**
     * 获取用户网络信息
     *
     * @return [ip,ua]
     */
    public static function getNetworkInfo()
    {
        static $ip = '';
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        return ['ip' => $ip, 'ua' => $user_agent];
    }

    /**
     * 入参uid与token uid 验证
     *
     * @param int $token //请求头token
     * @param int $creatorId //用户id
     * @return bool
     * @throws \Exception
     */
    public static function checkByTokenUid($token, $creatorId)
    {
        $redis = Cache::store('redis');
        $tokenUid = $redis->get($token);

        if ($tokenUid == null) {
            throw new \Exception(setLang('UserStatus'), 440);
        }

        if ($tokenUid != $creatorId) {
            throw new \Exception(setLang('CheckByTokenUid'), 440);
        }

        return true;
    }

    /**
     * 验证用户是否为项目成员操作文件
     *
     * @param int $userId //用户Id
     * @param int $fileId //文件Id
     * @throws \Exception 当非项目成员无法操作时抛出异常
     */
    public static function checkProjectMember($userId, $fileId)
    {
        $file = FileModel::where('id', $fileId)->find();
        $folder = FolderModel::where('id', $file['folder_id'])->find();
        $project = ProjectModel::where('id', $folder['project_id'])->find();
        $projectMember = ProjectUserModel::where('project_id', $project['id'])->find();

        if ($userId != $projectMember['user_id']) {
            throw new \Exception(setLang('NonProjectMembersCannotOperate'), 205);
        }
    }

    /**
     * 验证组织操作权限
     *
     * @param int $groupId 组织id
     * @param int $creatorId 创建者id
     * @return bool
     * @throws \Exception
     */
    public static function checkByGroup($groupId, $creatorId)
    {
        $group = groupModel::where('id', $groupId)->find();
        if (!$group) {
            throw new \Exception(setLang('groupNotFound'), 205);
        }

        if ($groupId != $group['id'] || $creatorId != $group['creator_id']) {
            throw new  \Exception(setLang('NoPermission'), 205);
        }
        return true;
    }

    /**
     *项目操作权限验证
     *
     * @param int $projectId 项目id
     * @param int $creatorId 创建者id
     * @return bool
     * @throws \Exception
     */
    public static function checkByProject($projectId, $creatorId)
    {
        $project = projectModel::where('id', $projectId)->find();
        if (!$project) {
            throw new \Exception(setLang('projectNotFound'), 205);
        }

        if ($projectId != $project['id'] || $creatorId != $project['creator_id']) {
            throw new \Exception(setLang('NoPermission'), 205);
        }
        return true;
    }

    /**
     * 检测用户上传文件是否超出规定内存
     *
     * 通过用户的身份验证token，检查用户上传的文件总大小是否超出规定的内存限制。
     *
     * @param string $token 用户token
     * @return bool
     * @throws \Exception
     */

    public static function checkUserMemory($token)
    {
        $redis = Cache::store('redis');
        $tokenUid = $redis->get($token);

        $user = UserModel::where('id', $tokenUid)->find();
        $userUpload = FileModel::where('creator_id', $user['id'])->select();

        $totalSize = 0;

        foreach ($userUpload as $file) {
            $totalSize += $file['size']; // 累加每个文件的大小
        }

        if ($totalSize > 1073741824) {
            throw new \Exception(setLang('UserMemoryExceedsLimitSize'), 205);
        }
        return true;
    }

}