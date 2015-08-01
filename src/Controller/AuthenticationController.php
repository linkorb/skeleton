<?php

namespace LinkORB\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController
{
    public function loginAction(Application $app, Request $request)
    {
        $data = array(
            'error' => $app['security.last_error']($request)
        );

        return $app['twig']->render('login.html.twig', $data);
    }
}
