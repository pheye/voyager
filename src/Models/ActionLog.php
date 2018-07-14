<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionLog extends Model
{
    /**
     *  用户相关
     */
    const ACTION_USER_LOGIN = "USER_LOGIN";
    const ACTION_USER_LOGIN_MOBILE = "USER_LOGIN_MOBILE";
    const ACTION_USER_LOGOUT = "USER_LOGOUT";
    const ACTION_USER_REGISTERED = "USER_REGISTERED";
    const ACTION_USER_REGISTERED_MOBILE = "USER_REGISTERED_MOBILE";
    const ACTION_USER_BIND_SOCIALITE_MOBILE_BASE = "USER_BIND_SOCIALITE_MOBILE_BASE";
    const ACTION_USER_BIND_SOCIALITE_BASE = "USER_BIND_SOCIALITE_BASE";
    const ACTION_USER_EXPIRED = "USER_EXPIRED";

    /**
     * 权限相关
     */
    const ACTION_ROLE_MANUAL_CHANGE = "ROLE_MANUAL_CHANGE";

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
