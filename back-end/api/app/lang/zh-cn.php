<?php

/**
 * 系统语言包
 * zh-cn 中文
 *
 * @author:dr
 * @createTime:2024-03-12
 */

return [

    /**
     *公共信息
     *
     */
    'SUCCESS' => '成功',
    'CannotAndYouSelfToGroup' => '不能将自己添加',
    'NoPermission' => '无操作权限',
    'UserDoesNotExist' => '不存在用户',
    'InvitingMemberError' => '成员邀请失败',
    'InvitingMemberSuccess' => '成员邀请成功',
    'MemberDoesNotExistOrHasBeenRemove' => '成员不存在或已剔除',
    'UnableToExcludeOneSelf' => '无法将自己剔除',
    'MemberRemoveError' => '成员剔除失败',
    'MemberRemoveSuccess' => '成员剔除成功',
    'RenameError' => '重命名失败',
    'RenameSuccess' => '重命名成功',
    'ProjectIdMust' => '项目id必须',
    'CreatorIdMust' => '创建者Id必须',
    'IconMust' => 'icon不能为空',
    'UserStatus' => '用户未登录',
    'CheckByTokenUid' => 'Token 和 用户 Id 不一致',
    'UploadError' => '上传失败',
    'MemberAddSuccess' => '成员添加成功',
    'MemberAddError' => '成员添加失败',
    'UserMemoryExceedsLimitSize' => '用户上传总文件超出限制大小',
    'NonProjectMembersCannotOperate' => '非项目成员无法操作',
    'FileIsNull' => '文件为空',
    /**
     * 用户模块
     *
     */
    'UserRegisterSuccess' => '注册成功',
    'UserRegisterFailed' => '注册失败',
    'UserLoginFailed' => '用户名或密码错误',
    'userNotFound' => '用户不存在',
    'AccountFreeze' => '该账号限制登录',
    'passwordError' => '密码错误',
    'UserNameIsRepeat' => '该用户名已被注册',
    'ExitedTheGroupSuccess' => '退出组织成功',
    'ExitedTheGroupError' => '退出组织失败',
    'ExitedTheProjectSuccess' => '退出项目成功',
    'ExitedTheProjectError' => '退出项目失败',
    'UserLogoutError' => '用户登出失败',
    'UserLogoutSuccess' => '用户登出成功',
    'UserUpdateError' => '无可用更新',
    'UserUpdateSuccess' => '用户信息更新成功',
    'UserPasswordMust6To30' => '密码长度必须在6到30位之间',
    'UserNameMust' => '用户名必须填写',
    'MustEmailFormat' => '用户名必须为邮箱格式',
    'PasswordMust' => '密码必须填写',
    'NickNameMust' => '昵称必须填写',
    'NickNameFormat' => '昵称必须为中文、字母或数字，不支持特殊符号',
    'NicknameLength' => '昵称长度必须在2到20位之间',
    'AvatarError' => '头像链接错误',
    'BioMax' => '个人介绍长度不能超过255个字符',
    /**
     * 组织模块
     *
     */
    'GroupNotFound' => '组织不存在',
    'GroupCreateSuccess' => '创建组织成功',
    'TheGroupNameIsDuplicated' => '该组织名称已重复',
    'GroupCreateError' => '组织创建失败',
    'GroupUpdateSuccess' => '组织更新成功',
    'GroupUpdateError' => '组织更新失败',
    'MemberAlreadyExistsInGroup' => '成员已存在组织里',
    'GroupCloseSuccess' => '组织关闭成功',
    'GroupOpenSuccess' => '组织开启成功',
    'GroupCloseError' => '组织关闭失败',
    'GroupOpenError' => '组织开启失败',
    'GroupIsClosed' => '该组织已关闭',
    'GroupNameMust' => '组织名称必须填写',
    'GroupNameLength' => '组织名称长度必须在2到20之间',
    /**
     * 项目模块
     *
     */
    'ProjectNotFound' => '项目不存在',
    'ProjectCreateSuccess' => '创建项目成功',
    'TheProjectNameIsDuplicated' => '该项目名称已重复',
    'ProjectCreateError' => '创建项目失败',
    'ProjectUpdateError' => '项目更新失败',
    'ProjectUpdateSuccess' => '项目更新成功',
    'ProjectCloseError' => '项目关闭失败',
    'ProjectOpenError' => '项目开启失败',
    'ProjectCloseSuccess' => '项目关闭成功',
    'ProjectOpenSuccess' => '项目开启成功',
    'ProjectNameMust' => '项目名称必须填写',
    'ProjectNameLength' => '项目名称长度必须再2到25之间',
    'GroupIdMust' => '组织id必须',
    /**
     * 文件夹模块
     *
     */
    'FolderCreateError' => '文件夹创建失败',
    'FolderCreateSuccess' => '文件夹创建成功',
    'FolderNotFound' => '目标文件夹不存在',
    'FolderAlreadyExists' => '文件夹已存在',
    'FolderRemoveError' => '文件夹移动失败',
    'FolderRemoveSuccess' => '文件夹移动成功',
    'FolderHaveFileCanNotDelete' => '文件夹存在文件,无法删除',
    'FolderDeleteError' => '文件夹删除失败',
    'FolderDeleteSuccess' => '文件夹删除成功',
    'FolderNameMust' => '文件夹名必须填写',
    'FolderLengthMust' => '文件夹名必须在2到20之间',
    'PrentIdMust' => '父文件夹id不能为空',
    'TargetFolderIsASubFolderOfTheSourceFolder' => '目标文件夹是源文件夹的子文件夹',
    'MoveToTheDestinationFolderWithDuplicateFolderNames' => '移动至目标文件夹内文件夹重名',
    'FolderAlreadyExistsInTheCurrentFolder' => '文件夹已存在于当前文件夹',
    /**
     *文件模块
     */
    'FileNotFound' => '文件不存在',
    'FileDeleteError' => '文件删除失败',
    'FileDeleteSuccess' => '文件删除成功',
    'FileRemoveError' => '文件移动失败',
    'FileRemoveSuccess' => '文件移动成功',
    'FileSaveError' => '文件保存失败',
    'FileSaveSuccess' => '文件保存成功',
    'MoveToTheDestinationFolderWithDuplicateFileNames' => '移动至目标文件夹内文件重名',
    'DuplicateFiles' => '文件重复',
    /**
     *聊天相关
     */
    'ChatSaveError' => '聊天内容记录失败',
    'UserIsNotAMemberOfTheProject' => '该用户不属于项目成员',
    'MemberAlreadyExistsInProject' => '成员已存在此项目',
    'UserIdMust' => '用户id必须',
    'MessageLength' => '聊天内容必须在1到5000之间',
    /**
     *公告通知相关
     */
    'CreateNoticeError' => '通知创建失败',
    'CreateNoticeSuccess' => '通知创建成功',
    'NoticeNotFound' => '通知不存在',
    'NoticeUpdateError' => '通知更新失败',
    'NoticeUpdateSuccess' => '通知更新成功',
    'NoticeDeleteError' => '通知删除失败',
    'NoticeDeleteSuccess' => '通知删除成功',
    'NoticeIdMust' => '通知id必须',
    'NoticeContentMust' => '通知内容必须填写',
    'NoticeContentLength'=>'通知内容长度必须在5到2000之间',
    /**
     *讨论组相关
     */
    'DiscussionReleaseError' => '讨论组发布失败',
    'DiscussionReleaseSuccess' => '讨论组发布成功',
    'GetDiscussionListError' => '讨论组列表获取失败',
    'DiscussionNotFound' => '讨论组不存在',
    'DiscussionUpdateError' => '无可用更新',
    'DiscussionUpdateSuccess' => '讨论内容更新成功',
    'NotCreatorOrGroupCreatorCanNotDelete' => '非发布者或组织创建人无法删除',
    'DeleteError' => '讨论组删除失败',
    'DeleteSuccess' => '讨论组删除成功',
    'CommentReleaseError' => '评论发表失败',
    'CommentReleaseSuccess' => '评论发表成功',
    'CommentListGetError' => '评论列表获取失败',
    'NotYouSelfCreatorOrGroupCreatorCanNotDelete' => '非本人或发布者或组织创建人无法删除',
    'CommentDeleteError' => '评论删除失败',
    'CommentDeleteSuccess' => '评论删除成功',
    'DiscussionTitleMust' => '讨论标题必须填写',
    'DiscussionLength' => '标题长度必须在5到80之间',
    'DiscussionContentMust' => '讨论内容必须填写',
    'DiscussionContentMust5to5000' => '讨论内容必须在5到5000之间',
    'DiscussionIdMust' => '讨论id必须',
    'DiscussionCommentContentMust2to5000'=>'评论内容必须在2到5000之间',
    'DiscussionCommentIdMust'=>'评论id必须'


];