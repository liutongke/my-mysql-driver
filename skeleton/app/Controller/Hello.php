<?php

namespace App\Controller;

use Sapi\Api;

class Hello extends Api
{
    public function index()
    {
        return [
            'code' => 200,
            'data' => 'hello world'
        ];
    }
}