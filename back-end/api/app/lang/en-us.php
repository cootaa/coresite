<?php

/**
 * 系统语言包
 * en-us 英文
 *
 * @author:dr
 * @createTime:2024-03-12
 */

return [

    /**
     *公共信息
     *
     */
    'SUCCESS'=>'success',
    'CannotAndYouSelfToGroup' => 'Can\'t and youSelf to group',
    'NoPermission' => 'No permission',
    'UserDoesNotExist' => 'User does\'t not exists',
    'InvitingMemberError' => 'Inviting members is failed',
    'InvitingMemberSuccess' => 'Inviting members is success ',
    'MemberDoesNotExistOrHasBeenRemove' => 'Member does\'t exists or has been remove',
    'UnableToExcludeOneSelf' => 'you can\'t exclude youSelf',
    'MemberRemoveError' => 'Member remove failed',
    'MemberRemoveSuccess' => 'Member remove success',
    'RenameError' => 'Rename failed',
    'RenameSuccess' => 'Rename success',
    'UserStatus' => 'User not logged in',
    'CheckByTokenUid' => 'Token and uid are inconsistent ',
    'UploadError' => 'Upload Error',
    'MemberAddSuccess' => 'Add member success ',
    'MemberAddError' => 'Add member failed',
    'UserMemoryExceedsLimitSize' => 'The total file size uploaded by the user exceeds the limit',
    'NonProjectMembersCannotOperate' => 'Non\'t project members Can\'t operate',
    'FileIsNull'=>'File is null',
    /**
     * 用户模块
     *
     */
    'UserRegisterSuccess' => 'Register success',
    'UserRegisterFailed' => 'Register failed',
    'UserLoginFailed' => 'Sign in failed',
    'userNotFound' => 'User not found',
    'AccountFreeze' => 'Account freeze',
    'passwordError' => 'password error',
    'UserNameIsRepeat' => 'user name is repeat',
    'ExitedTheGroupSuccess' => 'Exited the group success',
    'ExitedTheGroupError' => 'Exited the group failed',
    'ExitedTheProjectSuccess' => 'Exited the project success',
    'ExitedTheProjectError' => 'Exited the project failed',
    'UserLogoutError' => 'User logout failed',
    'UserLogoutSuccess' => 'User logout success',
    'UserUpdateError' => 'User update failed',
    'UserUpdateSuccess' => 'User update success',
    'UserPasswordMust6To30' => 'password length must be between 6 and 30 digits',
    'UserNameMust' => 'Username must be filled in',
    'MustEmailFormat' => 'The username must be in email format',
    'PasswordMust' => 'Password must be filled in',
    'NickNameMust' => 'Nickname must be filled in',
    'NickNameFormat' => 'Nickname must be in Chinese,letters,or numbers and special symbols are not supported ',
    'NicknameLength' => 'Nickname length must be between 2 and 20 digits ',
    'AvatarError' => 'Avatar Url Error',
    'BioMax' => 'Personal profile can\'t exceed 255 characters',
    /**
     * 组织模块
     *
     */
    'GroupNotFound' => 'Group not found',
    'GroupCreateSuccess' => 'Group create Success',
    'TheGroupNameIsDuplicated' => 'The group name is duplicated',
    'GroupUpdateSuccess' => 'Group update success',
    'GroupUpdateError' => 'Group update error',
    'GroupCreateError' => 'Group create error',
    'MemberAlreadyExistsInGroup' => 'Member already exists in group',
    'GroupCloseSuccess' => 'Group close success',
    'GroupOpenSuccess' => 'Group open success',
    'GroupCloseError' => 'Group close failed',
    'GroupOpenError' => 'Group open failed',
    'GroupNameMust' => 'Group name must be filled in',
    'GroupNameLength' => 'Group name must be between 2 and 20 digit',
    'IconMust' => 'Icon can\'t null',
    /**
     * 项目模块
     *
     */
    'ProjectNotFound' => 'Project not found',
    'ProjectCreateSuccess' => 'Project create success',
    'TheProjectNameIsDuplicated' => 'The project name is duplicated',
    'ProjectCreateError' => 'Project create error',
    'ProjectUpdateError' => 'Project update failed',
    'ProjectUpdateSuccess' => 'Project update success',
    'ProjectCloseError' => 'Project close error',
    'ProjectOpenError' => 'Project open success',
    'ProjectCloseSuccess' => 'Project close success',
    'ProjectOpenSuccess' => 'Project open success',
    'MemberAlreadyExistsInProject' => 'Member already exists in project',
    'ProjectNameMust' => 'Project name must be failed',
    'ProjectNameLength' => 'Project name must be between 20 and 25 digit',
    'GroupIdMust' => 'Group Name must be failed in',
    /**
     * 文件夹模块
     *
     */
    'FolderCreateError' => 'Folder create failed',
    'FolderCreateSuccess' => 'Folder create success',
    'FolderNotFound' => 'Folder not found',
    'FolderAlreadyExists'=>'Folder already exists',
    'FolderRemoveError' => 'Folder remove error',
    'FolderRemoveSuccess' => 'Folder remove success',
    'FolderHaveFileCanNotDelete' => 'Folder have files,you can\'t delete',
    'FolderDeleteError' => 'Folder delete failed',
    'FolderDeleteSuccess' => 'Folder delete success',
    'FolderNameMust' => 'Folder name must be filled in',
    'FolderLengthMust' => 'Folder name length must be between  2 and 20 digits',
    'CreatorIdMust' => 'Creator must be filled in',
    'PrentIdMust' => 'Parent id can\'t null',
    'TargetFolderIsASubFolderOfTheSourceFolder' => 'The target folder is a sub folder of the source folder',
    'MoveToTheDestinationFolderWithDuplicateFolderNames' => 'Move to the destination folder with duplicate folder names',
    'DuplicateFiles' => 'Duplicate files',
    'FolderAlreadyExistsInTheCurrentFolder'=>'Folder already exists in the current folder',
    /**
     *文件模块
     */
    'FileNotFound' => 'File not found',
    'FileDeleteError' => 'File delete failed',
    'FileDeleteSuccess' => 'File delete success',
    'FileRemoveError' => 'File moved failed',
    'FileRemoveSuccess' => 'File moved success',
    'FileSaveError' => 'File save failed',
    'FileSaveSuccess' => 'File save success',
    'MoveToTheDestinationFolderWithDuplicateFileNames' => 'Move to the destination folder with duplicate file names',
    /**
     *聊天相关
     */
    'ChatSaveError' => 'Chat Save failed',
    'UserIsNotAMemberOfTheProject' => 'The user is not a member of the project',
    'ProjectIdMust' => 'The project id must',
    'UserIdMust' => 'The user id must',
    'MessageLength' => 'Chat connect length must be between  1 and 5000 digits',
    /**
     *公告通知相关
     */
    'CreateNoticeError' => 'Create notice Error',
    'CreateNoticeSuccess' => 'Create notice Success',
    'NoticeNotFound' => 'Notice is not found',
    'NoticeUpdateError' => 'Notice update failed',
    'NoticeUpdateSuccess' => 'Notice update success',
    'NoticeDeleteError' => 'Notice delete failed',
    'NoticeDeleteSuccess' => 'Notice delete success',
    'NoticeIdMust' => 'Notice id must',
    'NoticeContentMust' => 'Notice content must',
    'NoticeContentLength'=>'Notice content length must be 5 to 2000 digits',
    /**
     *讨论组相关
     */
    'DiscussionReleaseError' => 'Discussion release failed',
    'DiscussionReleaseSuccess' => 'Discussion release success',
    'GetDiscussionListError' => 'discussionList Get Error',
    'DiscussionNotFound' => 'Discussion is not found',
    'DiscussionUpdateError' => 'No update available ',
    'DiscussionUpdateSuccess' => 'Discussion update success',
    'NotCreatorOrGroupCreatorCanNotDelete' => 'Not Creator or GroupCreator can\'t delete',
    'DeleteError' => 'Discussion Delete failed',
    'DeleteSuccess' => 'Discussion Delete success',
    'CommentReleaseError' => 'Comment Release failed',
    'CommentReleaseSuccess' => 'Comment  Release success',
    'CommentListGetError' => 'CommentList get error',
    'NotYouSelfCreatorOrGroupCreatorCanNotDelete' => 'Not youSelf or Creator or GroupCreator can\'t delete',
    'CommentDeleteError' => 'Comment delete error',
    'CommentDeleteSuccess' => 'Comment delete success',
    'DiscussionTitleMust' => 'Discussion Must be filled in',
    'DiscussionLength' => 'title length must be between 5 and 80 digits',
    'DiscussionContentMust' => 'Content must be filed in',
    'DiscussionContentMust5to5000' => 'Content length must be between 5 and 5000 digits',
    'DiscussionIdMust' => 'Discussion Id must',
    'DiscussionCommentContentMust2to5000'=>'Discussion Content length must be between 2 to 5000 digits',
    'DiscussionCommentIdMust'=>'Discussion Comment id must'

];