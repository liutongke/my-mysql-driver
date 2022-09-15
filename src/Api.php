<?php

namespace Sapi;

class Api
{
    public function __construct()
    {
//        $this->userCheck();//用户检验
    }

    //定义规则
    protected function rule()
    {
        return [];
    }

    //定义规则,用户自定义规则
    protected function userCheck($request)
    {
    }

    public function getRules($data, string $action, $request): array
    {
        $check = $this->userCheck($request);

        if (!empty($check)) {
            return ["res" => true, "data" => $check];
        }

        return Rule::getInstance()->getByRule($data, $action, $this->rule());
    }
}