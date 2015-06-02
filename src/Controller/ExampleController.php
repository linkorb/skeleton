<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExampleController
{
    public function indexAction(Application $app, Request $request)
    {
        return new Response('Cool');
    }
}
