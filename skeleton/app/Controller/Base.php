<?php

namespace App\Controller;

use Sapi\Api;

class Base extends Api
{
    //用户权限验证
    public function userCheck()
    {
        if (true) {
            return "token过期";
        }
    }

}