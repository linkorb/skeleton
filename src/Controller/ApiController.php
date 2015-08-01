<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use LinkORB\Skeleton\Model\Thing;

class ApiController
{
    public function rootAction(Application $app, Request $request)
    {
        $data = array('hello' => 'world');
        return new JsonResponse($data);
    }
}
