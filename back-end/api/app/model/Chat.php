<?php

namespace app\model;

use think\model\relation\BelongsTo;
use app\model\User as UserModel;

class Chat extends BaseModel
{
    //定义与用户表的关联,user表和chat表的关系
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }


}