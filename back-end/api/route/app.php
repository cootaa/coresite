<?php

/**
 * API路由
 *
 * @author: dr
 * @createTime: 2024-02-20
 */

use think\facade\Route;

/**
 * 经过Token验证的接口
 */
Route::group(function () {

    ## 公共接口
    Route::post('upload/file', 'common/uploadFile'); // 上传文件(单文件、多文件、文件夹)
    Route::post('upload/img', 'common/uploadImg'); // 上传图片(用户头像)

    ## 用户
    Route::get('user/info', 'user/info'); // 用户详细信息
    Route::post('user/update', 'user/update'); // 用户信息更新
    Route::post('user/logout', 'user/logout'); // 用户退出登陆
    Route::post('user/group/leave', 'user/groupLeave'); // 离开组织
    Route::post('user/project/leave', 'user/projectLeave'); // 离开项目

    ## 组织
    Route::get('group/list', 'group/list'); // 组织列表
    Route::post('group/create', 'group/create'); // 创建组织
    Route::post('group/update', 'group/update'); // 组织信息更新
    Route::post('group/dismiss', 'group/dismiss'); // 关闭组织
    Route::get('group/member/list', 'group/memberList'); // 组织成员列表
    Route::post('group/member/add', 'group/memberAdd'); // 添加成员到组织
    Route::post('group/member/del', 'group/memberDel'); // 从组织剔除成员

    ## 项目
    Route::get('project/list', 'project/list'); // 项目列表
    Route::post('project/create', 'project/create'); // 创建项目
    Route::post('project/update', 'project/update'); // 项目信息更新
    Route::post('project/dismiss', 'project/dismiss'); // 关闭项目
    Route::get('project/member/list', 'project/memberList'); // 项目成员列表
    Route::post('project/member/add', 'project/memberAdd'); // 添加成员到项目
    Route::post('project/member/del', 'project/memberDel'); // 从项目剔除成员

    ## 文件
    Route::get('file/list', 'file/list'); // 文件列表
    Route::post('file/upload', 'file/upload'); // 文件上传
    Route::post('file/rename', 'file/rename'); // 文件重命名
    Route::post('file/remove', 'file/remove'); // 文件移动
    Route::post('file/del', 'file/del'); // 删除文件
    Route::post('file/getFileUrl', 'file/getFileUrl');//下载文件

    ## 文件夹
    Route::get('folder/list', 'folder/list'); // 文件夹列表
    Route::post('folder/create', 'folder/create'); // 创建文件夹
    Route::post('folder/rename', 'folder/rename'); // 文件夹重命名
    Route::post('folder/remove', 'folder/remove'); // 文件夹移动
    Route::post('folder/del', 'folder/del'); // 删除文件夹
    Route::get('folder/tree', 'folder/folderTree'); // 删除文件夹

    ## 聊天
    Route::get('chat/list', 'chat/list'); // 获取聊天记录
    Route::post('chat/save', 'chat/save'); // 保存聊天记录

    ## 通知
    Route::get('notice/list', 'notice/list'); // 通知列表
    Route::post('notice/create', 'notice/create'); // 创建通知
    Route::post('notice/update', 'notice/update'); // 通知更新
    Route::post('notice/del', 'notice/del'); // 删除通知

    ## 讨论
    Route::get('discussion/list', 'discussion/list'); // 讨论列表
    Route::post('discussion/create', 'discussion/create'); // 创建讨论
    Route::post('discussion/update', 'discussion/update'); // 讨论更新
    Route::get('discussion/content','discussion/discussionContent');//讨论详情内容
    Route::post('discussion/del', 'discussion/del'); // 删除讨论
    Route::post('discussion/comment/add', 'discussion/addComment'); // 评论
    Route::get('discussion/comment/list', 'discussion/CommentList'); // 评论列表
    Route::post('discussion/comment/del', 'discussion/commentDel'); // 删除评论

})->middleware(app\middleware\CheckToken::class);

/**
 * 其他接口
 */

## 用户
Route::post('user/register', 'user/register'); // 用户注册
Route::post('user/login', 'user/login'); // 用户登陆
Route::get('file/download/:hash', 'file/download');//文件下载
